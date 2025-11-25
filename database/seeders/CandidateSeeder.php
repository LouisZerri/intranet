<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidate;
use App\Models\User;
use Carbon\Carbon;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'administrateur')->first();
        $managers = User::where('role', 'manager')->get();

        $candidates = [
            // Nouvelles candidatures
            [
                'first_name' => 'Julien',
                'last_name' => 'Mercier',
                'email' => 'julien.mercier@gmail.com',
                'phone' => '06 11 22 33 44',
                'city' => 'Lyon',
                'department' => 'Rhône',
                'position_applied' => 'Commercial immobilier',
                'desired_location' => 'Rhône',
                'available_from' => Carbon::now()->addDays(15),
                'source' => 'LinkedIn',
                'status' => 'new',
                'notes' => 'Candidature spontanée très motivée. À contacter rapidement.',
            ],
            [
                'first_name' => 'Marine',
                'last_name' => 'Duval',
                'email' => 'marine.duval@outlook.com',
                'phone' => '06 22 33 44 55',
                'city' => 'Paris',
                'department' => 'Paris',
                'position_applied' => 'Négociateur immobilier',
                'desired_location' => 'Hauts-de-Seine',
                'available_from' => Carbon::now()->addDays(30),
                'source' => 'Indeed',
                'status' => 'new',
                'notes' => 'Expérience de 2 ans dans l\'immobilier de luxe.',
            ],

            // En cours d'examen
            [
                'first_name' => 'Thomas',
                'last_name' => 'Lefevre',
                'email' => 'thomas.lefevre@yahoo.fr',
                'phone' => '06 33 44 55 66',
                'city' => 'Marseille',
                'department' => 'Bouches-du-Rhône',
                'position_applied' => 'Commercial terrain',
                'desired_location' => 'Bouches-du-Rhône',
                'available_from' => Carbon::now()->addDays(7),
                'source' => 'Leboncoin',
                'status' => 'in_review',
                'rating_motivation' => 4,
                'rating_seriousness' => 4,
                'rating_experience' => 3,
                'rating_commercial_skills' => 4,
                'notes' => 'Bon profil, expérience dans la vente automobile. À approfondir.',
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Martin',
                'email' => 'sophie.martin@gmail.com',
                'phone' => '06 44 55 66 77',
                'city' => 'Toulouse',
                'department' => 'Haute-Garonne',
                'position_applied' => 'Agent immobilier',
                'desired_location' => 'Haute-Garonne',
                'available_from' => Carbon::now(),
                'source' => 'Recommandation',
                'status' => 'in_review',
                'rating_motivation' => 5,
                'rating_seriousness' => 4,
                'rating_experience' => 2,
                'rating_commercial_skills' => 3,
                'notes' => 'Recommandée par Pierre Bernard. Très motivée mais peu d\'expérience.',
            ],

            // Entretien programmé
            [
                'first_name' => 'Alexandre',
                'last_name' => 'Petit',
                'email' => 'alexandre.petit@hotmail.com',
                'phone' => '06 55 66 77 88',
                'city' => 'Bordeaux',
                'department' => 'Gironde',
                'position_applied' => 'Responsable secteur',
                'desired_location' => 'Gironde',
                'available_from' => Carbon::now()->addDays(45),
                'source' => 'LinkedIn',
                'status' => 'interview',
                'interview_date' => Carbon::now()->addDays(3),
                'rating_motivation' => 5,
                'rating_seriousness' => 5,
                'rating_experience' => 5,
                'rating_commercial_skills' => 4,
                'notes' => 'Excellent profil. 8 ans d\'expérience chez un concurrent.',
                'interview_notes' => 'Entretien prévu le ' . Carbon::now()->addDays(3)->format('d/m/Y') . ' à 14h avec Marie Martin.',
            ],
            [
                'first_name' => 'Camille',
                'last_name' => 'Rousseau',
                'email' => 'camille.rousseau@gmail.com',
                'phone' => '06 66 77 88 99',
                'city' => 'Nice',
                'department' => 'Alpes-Maritimes',
                'position_applied' => 'Commercial immobilier',
                'desired_location' => 'Alpes-Maritimes',
                'available_from' => Carbon::now()->addDays(21),
                'source' => 'Site web',
                'status' => 'interview',
                'interview_date' => Carbon::now()->addDays(5),
                'rating_motivation' => 4,
                'rating_seriousness' => 5,
                'rating_experience' => 3,
                'rating_commercial_skills' => 4,
                'notes' => 'Profil intéressant, bonne présentation.',
            ],

            // Recrutés
            [
                'first_name' => 'Lucas',
                'last_name' => 'Bernard',
                'email' => 'lucas.bernard@gmail.com',
                'phone' => '06 77 88 99 00',
                'city' => 'Nantes',
                'department' => 'Loire-Atlantique',
                'position_applied' => 'Commercial immobilier',
                'desired_location' => 'Loire-Atlantique',
                'available_from' => Carbon::now()->addDays(14),
                'source' => 'Indeed',
                'status' => 'recruited',
                'decision_date' => Carbon::now()->subDays(2),
                'rating_motivation' => 5,
                'rating_seriousness' => 5,
                'rating_experience' => 4,
                'rating_commercial_skills' => 5,
                'notes' => 'Excellent candidat, très motivé.',
                'interview_notes' => 'Entretien très positif. Candidat retenu à l\'unanimité.',
            ],

            // Intégrés
            [
                'first_name' => 'Emma',
                'last_name' => 'Dupuis',
                'email' => 'emma.dupuis@gmail.com',
                'phone' => '06 88 99 00 11',
                'city' => 'Strasbourg',
                'department' => 'Bas-Rhin',
                'position_applied' => 'Commercial immobilier',
                'desired_location' => 'Bas-Rhin',
                'available_from' => Carbon::now()->subDays(30),
                'source' => 'Salon emploi',
                'status' => 'integrated',
                'decision_date' => Carbon::now()->subDays(45),
                'rating_motivation' => 5,
                'rating_seriousness' => 5,
                'rating_experience' => 4,
                'rating_commercial_skills' => 5,
                'notes' => 'Intégrée avec succès dans l\'équipe de Strasbourg.',
                'interview_notes' => 'Parfaite adéquation avec nos valeurs. Intégration réussie.',
            ],

            // Refusés
            [
                'first_name' => 'Antoine',
                'last_name' => 'Moreau',
                'email' => 'antoine.moreau@gmail.com',
                'phone' => '06 99 00 11 22',
                'city' => 'Lille',
                'department' => 'Nord',
                'position_applied' => 'Agent immobilier',
                'desired_location' => 'Nord',
                'available_from' => Carbon::now()->subDays(15),
                'source' => 'Candidature spontanée',
                'status' => 'refused',
                'decision_date' => Carbon::now()->subDays(10),
                'rating_motivation' => 2,
                'rating_seriousness' => 2,
                'rating_experience' => 1,
                'rating_commercial_skills' => 2,
                'notes' => 'Profil ne correspondant pas aux attentes.',
                'interview_notes' => 'Entretien décevant. Manque de motivation et d\'expérience.',
            ],
            [
                'first_name' => 'Léa',
                'last_name' => 'Garnier',
                'email' => 'lea.garnier@yahoo.fr',
                'phone' => '06 00 11 22 33',
                'city' => 'Montpellier',
                'department' => 'Hérault',
                'position_applied' => 'Négociateur immobilier',
                'desired_location' => 'Hérault',
                'available_from' => Carbon::now()->subDays(20),
                'source' => 'LinkedIn',
                'status' => 'refused',
                'decision_date' => Carbon::now()->subDays(12),
                'rating_motivation' => 3,
                'rating_seriousness' => 4,
                'rating_experience' => 2,
                'rating_commercial_skills' => 2,
                'notes' => 'Candidate sérieuse mais compétences commerciales insuffisantes.',
            ],
        ];

        foreach ($candidates as $candidateData) {
            $candidateData['created_by'] = $admin->id;
            $candidateData['assigned_to'] = $managers->random()->id;
            
            Candidate::create($candidateData);
        }

        $this->command->info('✅ Candidats créés avec succès !');
        $this->command->info('📊 Répartition :');
        $this->command->info('   - Nouvelles candidatures : 2');
        $this->command->info('   - En cours d\'examen : 2');
        $this->command->info('   - Entretiens programmés : 2');
        $this->command->info('   - Recrutés : 1');
        $this->command->info('   - Intégrés : 1');
        $this->command->info('   - Refusés : 2');
    }
}