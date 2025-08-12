<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\Resource;

class DocumentationController extends Controller
{
    /**
     * Page principale de documentation
     */
    public function index()
    {
        $contacts = Contact::orderBy('sector')->orderBy('name')->get();
        $faqs = Faq::where('is_active', true)->orderBy('order')->orderBy('created_at')->get();
        $resources = Resource::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        
        return view('documentation.index', compact('contacts', 'faqs', 'resources'));
    }

    /**
     * Page contacts par secteur
     */
    public function contacts()
    {
        $contacts = Contact::orderBy('sector')->orderBy('name')->get();
        $sectors = Contact::distinct('sector')->pluck('sector');
        
        return view('documentation.contacts.index', compact('contacts', 'sectors'));
    }

    /**
     * Page FAQ
     */
    public function faq()
    {
        $faqs = Faq::where('is_active', true)
                   ->orderBy('order')
                   ->orderBy('created_at')
                   ->get();
        $categories = Faq::distinct('category')->pluck('category');
        
        return view('documentation.faq.index', compact('faqs', 'categories'));
    }

    /**
     * Page ressources
     */
    public function resources()
    {
        $resources = Resource::where('is_active', true)
                           ->orderBy('category')
                           ->orderBy('name')
                           ->get();
        $categories = Resource::distinct('category')->pluck('category');
        
        return view('documentation.resources.index', compact('resources', 'categories'));
    }

    /**
     * Télécharger une ressource
     */
    public function downloadResource(Resource $resource)
    {
        if (!$resource->is_active) {
            abort(404);
        }

        $filePath = storage_path('app/' . $resource->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Fichier non trouvé');
        }

        // Incrémenter le compteur de téléchargements
        $resource->increment('download_count');

        return response()->download($filePath, $resource->original_filename);
    }

    // --- MÉTHODES ADMIN POUR GESTION DES CONTACTS ---

    public function createContact()
    {
        return view('documentation.contacts.create');
    }

    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'sector' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Contact::create($validated);

        return redirect()->route('documentation.index')
                        ->with('success', 'Contact ajouté avec succès');
    }

    public function editContact(Contact $contact)
    {
        return view('documentation.contacts.edit', compact('contact'));
    }

    public function updateContact(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'sector' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $contact->update($validated);

        return redirect()->route('documentation.index')
                        ->with('success', 'Contact modifié avec succès');
    }

    public function destroyContact(Contact $contact)
    {
        $contact->delete();
        
        return redirect()->route('documentation.index')
                        ->with('success', 'Contact supprimé avec succès');
    }

    // --- MÉTHODES ADMIN POUR GESTION DES FAQ ---

    public function createFaq()
    {
        return view('documentation.faq.create');
    }

    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        Faq::create($validated);

        return redirect()->route('documentation.index')
                        ->with('success', 'FAQ ajoutée avec succès');
    }

    public function editFaq(Faq $faq)
    {
        return view('documentation.faq.edit', compact('faq'));
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $faq->update($validated);

        return redirect()->route('documentation.index')
                        ->with('success', 'FAQ modifiée avec succès');
    }

    public function destroyFaq(Faq $faq)
    {
        $faq->delete();
        
        return redirect()->route('documentation.index')
                        ->with('success', 'FAQ supprimée avec succès');
    }

    // --- MÉTHODES ADMIN POUR GESTION DES RESSOURCES ---

    public function createResource()
    {
        return view('documentation.resources.create');
    }

    public function storeResource(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'file' => 'required|file|max:10240', // 10MB max
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $filePath = $file->store('documentation/resources');

            $validated['file_path'] = $filePath;
            $validated['original_filename'] = $originalFilename;
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        unset($validated['file']);
        Resource::create($validated);

        return redirect()->route('documentation.index')
                        ->with('success', 'Ressource ajoutée avec succès');
    }

    public function editResource(Resource $resource)
    {
        return view('documentation.resources.edit', compact('resource'));
    }

    public function updateResource(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'file' => 'nullable|file|max:10240', // 10MB max
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('file')) {
            // Supprimer l'ancien fichier
            if ($resource->file_path && Storage::exists($resource->file_path)) {
                Storage::delete($resource->file_path);
            }

            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $filePath = $file->store('documentation/resources');

            $validated['file_path'] = $filePath;
            $validated['original_filename'] = $originalFilename;
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        unset($validated['file']);
        $resource->update($validated);

        return redirect()->route('documentation.index')
                        ->with('success', 'Ressource modifiée avec succès');
    }

    public function destroyResource(Resource $resource)
    {
        // Supprimer le fichier
        if ($resource->file_path && Storage::exists($resource->file_path)) {
            Storage::delete($resource->file_path);
        }

        $resource->delete();
        
        return redirect()->route('documentation.index')
                        ->with('success', 'Ressource supprimée avec succès');
    }
}