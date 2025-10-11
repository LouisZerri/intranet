<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    /**
     * Afficher la liste des actualités selon le cahier des charges
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupération des actualités personnalisées selon le rôle
        $query = News::published()
            ->forUser($user)
            ->with('author');

        // Filtre par priorité si demandé
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Filtre par mot-clé dans le titre ou contenu
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        // Tri par priorité puis par date
        $news = $query->byPriority()
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        // Statistiques pour l'affichage
        $stats = [
            'total' => News::published()->forUser($user)->count(),
            'urgent' => News::published()->forUser($user)->where('priority', 'urgent')->count(),
            'important' => News::published()->forUser($user)->where('priority', 'important')->count(),
            'normal' => News::published()->forUser($user)->where('priority', 'normal')->count(),
        ];

        return view('news.index', compact('news', 'stats', 'request'));
    }

    /**
     * Afficher une actualité complète
     */
    public function show(News $news)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur peut voir cette actualité
        if (!$this->canUserViewNews($user, $news)) {
            abort(403, 'Vous n\'avez pas accès à cette actualité.');
        }

        // Actualités similaires/récentes
        $relatedNews = News::published()
            ->forUser($user)
            ->where('id', '!=', $news->id)
            ->where('priority', $news->priority)
            ->take(3)
            ->get();

        return view('news.show', compact('news', 'relatedNews'));
    }

    /**
     * Formulaire de création d'actualité (admin/manager)
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Seuls admin et managers peuvent créer des actualités
        if (!$user->isAdministrateur() && !$user->isManager()) {
            abort(403, 'Vous n\'avez pas l\'autorisation de créer des actualités.');
        }

        // Options pour les formulaires
        $priorities = [
            'normal' => 'Normal',
            'important' => 'Important', 
            'urgent' => 'Urgent'
        ];

        // Rôles disponibles selon le type d'utilisateur
        $roles = $this->getAvailableRoles($user);

        return view('news.create', compact('priorities', 'roles'));
    }

    /**
     * Enregistrer une nouvelle actualité
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isAdministrateur() && !$user->isManager()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:normal,important,urgent',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'in:collaborateur,manager,administrateur',
            'expires_at' => 'nullable|date|after:today',
            'publish_now' => 'boolean'
        ], [
            'title.required' => 'Le titre est obligatoire.',
            'content.required' => 'Le contenu est obligatoire.',
            'priority.required' => 'La priorité est obligatoire.',
            'expires_at.after' => 'La date d\'expiration doit être dans le futur.',
        ]);

        // Validation des rôles selon le type d'utilisateur
        $this->validateTargetRoles($user, $validated['target_roles'] ?? []);

        $newsData = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'priority' => $validated['priority'],
            'target_roles' => $this->getValidTargetRoles($user, $validated['target_roles'] ?? []),
            'expires_at' => $validated['expires_at'] ?? null,
            'author_id' => $user->id,
        ];

        // Publication immédiate ou brouillon
        if ($request->boolean('publish_now')) {
            $newsData['status'] = 'published';
            $newsData['published_at'] = now();
        } else {
            $newsData['status'] = 'draft';
        }

        $news = News::create($newsData);

        $message = $news->status === 'published' 
            ? 'Actualité publiée avec succès !' 
            : 'Actualité sauvegardée en brouillon.';

        return redirect()->route('news.index')->with('success', $message);
    }

    /**
     * Formulaire d'édition d'actualité
     */
    public function edit(News $news)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Seul l'auteur ou admin peut modifier
        if (!$user->isAdministrateur() && $news->author_id !== $user->id) {
            abort(403, 'Vous ne pouvez modifier que vos propres actualités.');
        }

        $priorities = [
            'normal' => 'Normal',
            'important' => 'Important', 
            'urgent' => 'Urgent'
        ];

        // Rôles disponibles selon le type d'utilisateur
        $roles = $this->getAvailableRoles($user);

        return view('news.edit', compact('news', 'priorities', 'roles'));
    }

    /**
     * Mettre à jour une actualité
     */
    public function update(Request $request, News $news)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isAdministrateur() && $news->author_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:normal,important,urgent',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'in:collaborateur,manager,administrateur',
            'expires_at' => 'nullable|date',
            'status' => 'required|in:draft,published,archived'
        ]);

        // Validation des rôles selon le type d'utilisateur
        $this->validateTargetRoles($user, $validated['target_roles'] ?? []);

        $updateData = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'priority' => $validated['priority'],
            'target_roles' => $this->getValidTargetRoles($user, $validated['target_roles'] ?? []),
            'expires_at' => $validated['expires_at'] ?? null,
            'status' => $validated['status'],
        ];

        // Si passage en publié et pas encore publié
        if ($validated['status'] === 'published' && !$news->published_at) {
            $updateData['published_at'] = now();
        }

        $news->update($updateData);

        return redirect()->route('news.index')->with('success', 'Actualité mise à jour avec succès !');
    }

    /**
     * Supprimer une actualité
     */
    public function destroy(News $news)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isAdministrateur() && $news->author_id !== $user->id) {
            abort(403);
        }

        $news->delete();

        return redirect()->route('news.index')->with('success', 'Actualité supprimée avec succès !');
    }

    /**
     * Vérifier si un utilisateur peut voir une actualité
     */
    private function canUserViewNews($user, $news): bool
    {
        if (!$news->isPublished()) {
            return false;
        }

        // Vérification du rôle ciblé
        if ($news->target_roles && !in_array($user->role, $news->target_roles)) {
            return false;
        }

        return true;
    }

    /**
     * Récupérer les rôles disponibles selon le type d'utilisateur
     */
    private function getAvailableRoles($user): array
    {
        if ($user->isAdministrateur()) {
            // L'admin peut cibler tous les rôles
            return [
                'collaborateur' => 'Collaborateurs',
                'manager' => 'Managers',
                'administrateur' => 'Administrateurs'
            ];
        } elseif ($user->isManager()) {
            // Le manager peut seulement cibler les collaborateurs
            return [
                'collaborateur' => 'Collaborateurs (mon équipe)'
            ];
        }

        return [];
    }

    /**
     * Valider les rôles cibles selon le type d'utilisateur
     */
    private function validateTargetRoles($user, array $targetRoles): void
    {
        if ($user->isManager()) {
            // Le manager ne peut cibler que les collaborateurs
            foreach ($targetRoles as $role) {
                if ($role !== 'collaborateur') {
                    abort(403, 'En tant que manager, vous ne pouvez créer des actualités que pour votre équipe (collaborateurs).');
                }
            }
        }
    }

    /**
     * Obtenir les rôles cibles valides selon le type d'utilisateur
     */
    private function getValidTargetRoles($user, array $targetRoles): ?array
    {
        if (empty($targetRoles)) {
            // Si aucun rôle spécifié, comportement par défaut selon le type d'utilisateur
            if ($user->isManager()) {
                // Le manager cible automatiquement les collaborateurs
                return ['collaborateur'];
            } else {
                // L'admin peut cibler tout le monde
                return null;
            }
        }

        // Si des rôles sont spécifiés, les retourner après validation
        return $targetRoles;
    }
}