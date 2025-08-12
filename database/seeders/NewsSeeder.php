<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\User;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'administrateur')->first();

        // Actualité urgente
        News::create([
            'title' => '🚨 Mise à jour importante du système de sécurité',
            'content' => 'Suite aux récentes mises à jour de sécurité, tous les collaborateurs doivent changer leur mot de passe avant vendredi. Merci de suivre les instructions envoyées par email.',
            'priority' => 'urgent',
            'status' => 'published',
            'target_roles' => ['collaborateur', 'manager', 'administrateur'],
            'published_at' => now()->subDays(1),
            'expires_at' => now()->addDays(7),
            'author_id' => $admin->id,
        ]);

        // Actualité importante
        News::create([
            'title' => '📊 Résultats exceptionnels du trimestre !',
            'content' => 'Félicitations à toutes les équipes ! Nous avons dépassé nos objectifs de 15% ce trimestre. Un pot de célébration aura lieu vendredi à 18h dans la salle de réunion principale.',
            'priority' => 'important',
            'status' => 'published',
            'target_roles' => ['collaborateur', 'manager', 'administrateur'],
            'published_at' => now()->subDays(2),
            'author_id' => $admin->id,
        ]);

        // Actualité pour les managers uniquement
        News::create([
            'title' => '👥 Réunion managers - Point mensuel',
            'content' => 'La réunion mensuelle des managers aura lieu mardi 10 août à 14h. Ordre du jour : révision des KPI, nouveaux objectifs et planning des congés d\'été.',
            'priority' => 'important',
            'status' => 'published',
            'target_roles' => ['manager', 'administrateur'],
            'published_at' => now()->subDays(3),
            'author_id' => $admin->id,
        ]);

        // Actualité normale
        News::create([
            'title' => '🍕 Nouveau service de restauration',
            'content' => 'Un nouveau service de livraison de repas est désormais disponible pour les collaborateurs. Commandes possibles jusqu\'à 11h pour le déjeuner. Plus d\'infos sur l\'intranet.',
            'priority' => 'normal',
            'status' => 'published',
            'target_roles' => ['collaborateur', 'manager', 'administrateur'],
            'published_at' => now()->subDays(5),
            'author_id' => $admin->id,
        ]);

        // Actualité pour département Commercial
        News::create([
            'title' => '🎯 Nouvelle campagne commerciale lancée',
            'content' => 'La nouvelle campagne "Été 2025" est maintenant active. Tous les outils et supports sont disponibles dans l\'espace commercial. Objectif : +20% sur ce trimestre !',
            'priority' => 'important',
            'status' => 'published',
            'target_roles' => ['collaborateur', 'manager'],
            'target_departments' => ['Commercial'],
            'published_at' => now()->subDays(4),
            'author_id' => $admin->id,
        ]);

        // Actualité pour département Marketing
        News::create([
            'title' => '🎨 Nouvelle charte graphique disponible',
            'content' => 'La nouvelle charte graphique de l\'entreprise est maintenant finalisée. Tous les templates mis à jour sont disponibles dans l\'espace ressources. Merci d\'utiliser uniquement ces nouveaux éléments.',
            'priority' => 'normal',
            'status' => 'published',
            'target_roles' => ['collaborateur', 'manager'],
            'target_departments' => ['Marketing'],
            'published_at' => now()->subDays(6),
            'author_id' => $admin->id,
        ]);

        // Actualité RH
        News::create([
            'title' => '🏖️ Planification des congés d\'été',
            'content' => 'N\'oubliez pas de poser vos congés d\'été avant le 15 août. Les demandes tardives ne pourront pas être garanties. Consultez le planning équipe avant de faire votre demande.',
            'priority' => 'important',
            'status' => 'published',
            'target_roles' => ['collaborateur', 'manager'],
            'published_at' => now()->subDays(7),
            'author_id' => $admin->id,
        ]);

        // Actualité en brouillon (non publiée)
        News::create([
            'title' => '🔧 Maintenance serveurs prévue',
            'content' => 'Une maintenance des serveurs est prévue le weekend prochain. Plus de détails à venir sur les horaires et les services impactés.',
            'priority' => 'normal',
            'status' => 'draft',
            'target_roles' => ['collaborateur', 'manager', 'administrateur'],
            'author_id' => $admin->id,
        ]);

        // Actualité expirée (pour test)
        News::create([
            'title' => '🎉 Bonne année 2025 !',
            'content' => 'Toute l\'équipe vous souhaite une excellente année 2025 pleine de succès et de projets passionnants !',
            'priority' => 'normal',
            'status' => 'published',
            'target_roles' => ['collaborateur', 'manager', 'administrateur'],
            'published_at' => now()->subDays(30),
            'expires_at' => now()->subDays(1),
            'author_id' => $admin->id,
        ]);

        // Actualité programmée pour plus tard
        News::create([
            'title' => '📚 Nouveau programme de formation',
            'content' => 'Un nouveau programme de formation continue sera lancé le mois prochain. Restez connectés pour plus d\'informations !',
            'priority' => 'normal',
            'status' => 'published',
            'target_roles' => ['collaborateur', 'manager'],
            'published_at' => now()->addDays(5),
            'author_id' => $admin->id,
        ]);

        $this->command->info('Actualités créées avec succès !');
        $this->command->info('- 1 actualité urgente');
        $this->command->info('- 4 actualités importantes'); 
        $this->command->info('- 5 actualités normales');
        $this->command->info('- Actualités ciblées par département');
        $this->command->info('- 1 brouillon et 1 expirée pour les tests');
    }
}