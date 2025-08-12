<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formation;
use App\Models\FormationRequest;
use App\Models\User;
use Carbon\Carbon;

class FormationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les utilisateurs pour les relations
        $admin = User::where('role', 'administrateur')->first();
        $managers = User::where('role', 'manager')->get();
        $collaborateurs = User::where('role', 'collaborateur')->get();

        if (!$admin) {
            $this->command->error('Aucun administrateur trouvé. Exécutez d\'abord le UserSeeder.');
            return;
        }

        // 1. Créer les formations
        $formations = [
            // Formations Management
            [
                'title' => 'Leadership et Management d\'équipe',
                'description' => 'Formation complète sur les techniques de management moderne, la motivation d\'équipe et le développement du leadership. Apprenez à diriger efficacement vos collaborateurs et à créer un environnement de travail performant.',
                'category' => 'Management',
                'level' => 'intermediaire',
                'duration_hours' => 16,
                'cost' => 890.00,
                'provider' => 'FormaPro Management',
                'format' => 'presentiel',
                'max_participants' => 12,
                'start_date' => now()->addDays(15),
                'end_date' => now()->addDays(17),
                'location' => 'Salle de formation - Siège social',
                'prerequisites' => [
                    'Avoir une expérience managériale d\'au moins 6 mois',
                    'Encadrer au moins 2 collaborateurs'
                ],
                'objectives' => [
                    'Maîtriser les fondamentaux du management',
                    'Développer son leadership naturel',
                    'Apprendre les techniques de motivation d\'équipe',
                    'Gérer les conflits et situations difficiles'
                ],
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Gestion de projet Agile et Scrum',
                'description' => 'Découvrez les méthodologies agiles et maîtrisez le framework Scrum pour gérer efficacement vos projets. Formation pratique avec études de cas réels.',
                'category' => 'Gestion de projet',
                'level' => 'intermediaire',
                'duration_hours' => 24,
                'cost' => 1200.00,
                'provider' => 'Agile Academy',
                'format' => 'hybride',
                'max_participants' => 15,
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(33),
                'location' => 'Formation hybride - Présentiel/Distanciel',
                'prerequisites' => [
                    'Connaissance de base en gestion de projet',
                    'Expérience en équipe projet souhaitée'
                ],
                'objectives' => [
                    'Comprendre les principes Agile',
                    'Maîtriser le framework Scrum',
                    'Organiser et animer les cérémonies',
                    'Utiliser les outils de suivi agile'
                ],
                'created_by' => $managers->first()->id,
            ],

            // Formations Techniques
            [
                'title' => 'Excel Avancé - Tableaux croisés et Macros',
                'description' => 'Perfectionnez vos compétences Excel avec les fonctions avancées, tableaux croisés dynamiques et automatisation par macros VBA. Devenez un expert Excel.',
                'category' => 'Bureautique',
                'level' => 'avance',
                'duration_hours' => 14,
                'cost' => 650.00,
                'provider' => 'TechnoFormation',
                'format' => 'distanciel',
                'max_participants' => 20,
                'start_date' => now()->addDays(20),
                'end_date' => now()->addDays(21),
                'location' => 'Plateforme Teams',
                'prerequisites' => [
                    'Maîtrise des fonctions Excel de base',
                    'Utilisation régulière d\'Excel'
                ],
                'objectives' => [
                    'Maîtriser les tableaux croisés dynamiques',
                    'Créer des macros VBA simples',
                    'Automatiser les tâches répétitives',
                    'Optimiser ses analyses de données'
                ],
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Cybersécurité et Protection des données',
                'description' => 'Formation essentielle sur les bonnes pratiques de sécurité informatique, protection des données personnelles (RGPD) et prévention des cyberattaques.',
                'category' => 'Informatique',
                'level' => 'debutant',
                'duration_hours' => 8,
                'cost' => 450.00,
                'provider' => 'CyberSafe Pro',
                'format' => 'presentiel',
                'max_participants' => 25,
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(10),
                'location' => 'Amphithéâtre A',
                'prerequisites' => [
                    'Utilisation quotidienne d\'un ordinateur',
                    'Connaissances de base d\'internet'
                ],
                'objectives' => [
                    'Identifier les principales menaces cyber',
                    'Appliquer les bonnes pratiques de sécurité',
                    'Comprendre les enjeux RGPD',
                    'Réagir face à une tentative d\'attaque'
                ],
                'created_by' => $managers->get(1)->id ?? $admin->id,
            ],

            // Formations Communication
            [
                'title' => 'Prise de parole en public',
                'description' => 'Surmontez votre stress et apprenez les techniques de communication orale. Développez votre aisance à l\'oral et votre capacité à convaincre votre auditoire.',
                'category' => 'Communication',
                'level' => 'debutant',
                'duration_hours' => 12,
                'cost' => 750.00,
                'provider' => 'Eloquence & Communication',
                'format' => 'presentiel',
                'max_participants' => 10,
                'start_date' => now()->addDays(25),
                'end_date' => now()->addDays(26),
                'location' => 'Salle de conférence B',
                'prerequisites' => [
                    'Aucun prérequis spécifique'
                ],
                'objectives' => [
                    'Gérer le stress de la prise de parole',
                    'Structurer ses interventions',
                    'Captiver son auditoire',
                    'Répondre aux questions avec assurance'
                ],
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Communication interculturelle',
                'description' => 'Développez vos compétences en communication interculturelle pour mieux travailler avec des équipes internationales et diversifiées.',
                'category' => 'Communication',
                'level' => 'intermediaire',
                'duration_hours' => 16,
                'cost' => 980.00,
                'provider' => 'Global Communication Institute',
                'format' => 'hybride',
                'max_participants' => 16,
                'start_date' => now()->addDays(40),
                'end_date' => now()->addDays(42),
                'location' => 'Formation hybride',
                'prerequisites' => [
                    'Expérience en environnement multiculturel',
                    'Niveau d\'anglais intermédiaire'
                ],
                'objectives' => [
                    'Comprendre les différences culturelles',
                    'Adapter sa communication au contexte',
                    'Éviter les malentendus interculturels',
                    'Créer des équipes multiculturelles efficaces'
                ],
                'created_by' => $managers->get(2)->id ?? $admin->id,
            ],

            // Formations Développement personnel
            [
                'title' => 'Gestion du stress et bien-être au travail',
                'description' => 'Apprenez à identifier, comprendre et gérer le stress professionnel. Techniques de relaxation, organisation du travail et équilibre vie pro/perso.',
                'category' => 'Développement personnel',
                'level' => 'debutant',
                'duration_hours' => 8,
                'cost' => 0.00, // Formation gratuite
                'provider' => 'Centre de bien-être RH',
                'format' => 'presentiel',
                'max_participants' => 20,
                'start_date' => now()->addDays(12),
                'end_date' => now()->addDays(12),
                'location' => 'Espace détente - RDC',
                'prerequisites' => [
                    'Aucun prérequis'
                ],
                'objectives' => [
                    'Identifier ses sources de stress',
                    'Apprendre des techniques de gestion du stress',
                    'Améliorer son organisation personnelle',
                    'Trouver son équilibre vie professionnelle/personnelle'
                ],
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Efficacité personnelle et gestion du temps',
                'description' => 'Optimisez votre productivité grâce aux meilleures techniques de gestion du temps, priorisation des tâches et organisation personnelle.',
                'category' => 'Développement personnel',
                'level' => 'debutant',
                'duration_hours' => 7,
                'cost' => 420.00,
                'provider' => 'Efficience Pro',
                'format' => 'distanciel',
                'max_participants' => null, // Illimité
                'start_date' => now()->addDays(18),
                'end_date' => now()->addDays(18),
                'location' => 'Formation en ligne',
                'prerequisites' => [
                    'Avoir des difficultés d\'organisation',
                    'Motivation à changer ses habitudes'
                ],
                'objectives' => [
                    'Maîtriser les techniques de priorisation',
                    'Optimiser son planning quotidien',
                    'Éliminer les pertes de temps',
                    'Développer de nouvelles habitudes efficaces'
                ],
                'created_by' => $managers->first()->id,
            ],

            // Formations Commerciales
            [
                'title' => 'Techniques de vente et négociation commerciale',
                'description' => 'Développez vos compétences commerciales et maîtrisez les techniques de négociation pour conclure davantage de ventes et fidéliser vos clients.',
                'category' => 'Commercial',
                'level' => 'intermediaire',
                'duration_hours' => 21,
                'cost' => 1150.00,
                'provider' => 'Sales Academy Pro',
                'format' => 'presentiel',
                'max_participants' => 12,
                'start_date' => now()->addDays(35),
                'end_date' => now()->addDays(37),
                'location' => 'Centre de formation commercial',
                'prerequisites' => [
                    'Expérience commerciale de base',
                    'Être en contact avec des clients'
                ],
                'objectives' => [
                    'Maîtriser le processus de vente',
                    'Développer ses techniques de négociation',
                    'Gérer les objections clients',
                    'Fidéliser sa clientèle'
                ],
                'created_by' => $managers->get(1)->id ?? $admin->id,
            ],
            [
                'title' => 'Marketing digital et réseaux sociaux',
                'description' => 'Formation complète sur les stratégies de marketing digital moderne, gestion des réseaux sociaux professionnels et mesure de la performance en ligne.',
                'category' => 'Marketing',
                'level' => 'intermediaire',
                'duration_hours' => 18,
                'cost' => 850.00,
                'provider' => 'Digital Marketing Institute',
                'format' => 'hybride',
                'max_participants' => 18,
                'start_date' => now()->addDays(28),
                'end_date' => now()->addDays(30),
                'location' => 'Formation hybride',
                'prerequisites' => [
                    'Connaissances de base en marketing',
                    'Utilisation des réseaux sociaux'
                ],
                'objectives' => [
                    'Créer une stratégie marketing digital',
                    'Maîtriser les principaux réseaux sociaux pro',
                    'Analyser les performances et ROI',
                    'Optimiser sa présence en ligne'
                ],
                'created_by' => $admin->id,
            ],

            // Formation Qualité/Sécurité
            [
                'title' => 'Formation Sécurité au Travail - Gestes et Postures',
                'description' => 'Formation obligatoire sur la prévention des risques professionnels, gestes et postures au poste de travail. Respect des normes sécurité.',
                'category' => 'Sécurité',
                'level' => 'debutant',
                'duration_hours' => 4,
                'cost' => 0.00, // Obligatoire donc gratuit
                'provider' => 'Service Prévention Interne',
                'format' => 'presentiel',
                'max_participants' => 30,
                'start_date' => now()->addDays(8),
                'end_date' => now()->addDays(8),
                'location' => 'Atelier sécurité',
                'prerequisites' => [
                    'Tous les collaborateurs (obligatoire)'
                ],
                'objectives' => [
                    'Identifier les risques au poste de travail',
                    'Adopter les bonnes postures',
                    'Connaître les procédures d\'urgence',
                    'Utiliser les équipements de protection'
                ],
                'created_by' => $admin->id,
            ],

            // Formation Innovation
            [
                'title' => 'Innovation et Créativité en Entreprise',
                'description' => 'Stimulez votre créativité et apprenez les méthodes d\'innovation collaborative. Design thinking, brainstorming et développement d\'idées innovantes.',
                'category' => 'Innovation',
                'level' => 'intermediaire',
                'duration_hours' => 14,
                'cost' => 720.00,
                'provider' => 'Innovation Lab',
                'format' => 'presentiel',
                'max_participants' => 14,
                'start_date' => now()->addDays(45),
                'end_date' => now()->addDays(46),
                'location' => 'Lab Innovation - 3ème étage',
                'prerequisites' => [
                    'Ouverture d\'esprit et curiosité',
                    'Envie de challenger les habitudes'
                ],
                'objectives' => [
                    'Développer sa créativité personnelle',
                    'Maîtriser le Design Thinking',
                    'Animer des sessions de brainstorming',
                    'Transformer les idées en projets concrets'
                ],
                'created_by' => $managers->get(2)->id ?? $admin->id,
            ],
        ];

        // Créer les formations
        $createdFormations = [];
        foreach ($formations as $formationData) {
            $formation = Formation::create($formationData);
            $createdFormations[] = $formation;
            $this->command->info("Formation créée: {$formation->title}");
        }

        // 2. Créer des demandes de formation réalistes
        $this->createFormationRequests($createdFormations, $collaborateurs, $managers);

        $this->command->info('Seeder Formations terminé avec succès !');
    }

    /**
     * Créer des demandes de formation réalistes
     */
    private function createFormationRequests($formations, $collaborateurs, $managers)
    {
        $priorities = ['basse', 'normale', 'haute'];
        $statuses = ['en_attente', 'approuve', 'refuse', 'termine'];
        
        // Motivations types pour les demandes
        $motivations = [
            'Cette formation s\'inscrit parfaitement dans mon plan de développement professionnel et me permettra d\'améliorer mes compétences pour mieux servir nos clients.',
            'J\'aimerais développer mes compétences dans ce domaine pour pouvoir prendre de nouvelles responsabilités dans mon équipe.',
            'Cette formation me permettra d\'être plus efficace dans mes missions quotidiennes et d\'apporter une valeur ajoutée à l\'entreprise.',
            'Je souhaite acquérir ces nouvelles compétences pour évoluer dans ma carrière et contribuer davantage aux objectifs de l\'équipe.',
            'Cette formation répond à un besoin identifié lors de mon dernier entretien annuel et m\'aidera à atteindre mes objectifs.',
            'J\'ai identifié des lacunes dans ce domaine qui limitent ma performance, cette formation m\'aiderait à les combler.',
            'Cette formation s\'aligne avec les évolutions de notre secteur et me permettra de rester à jour sur les meilleures pratiques.'
        ];

        $allUsers = $collaborateurs->merge($managers);
        
        foreach ($formations as $formation) {
            // Nombre aléatoire de demandes par formation (entre 2 et 8)
            $nbRequests = rand(2, min(8, $formation->max_participants ?? 8));
            $selectedUsers = $allUsers->random($nbRequests);
            
            foreach ($selectedUsers as $user) {
                // Éviter les doublons
                $existingRequest = FormationRequest::where('formation_id', $formation->id)
                                                 ->where('user_id', $user->id)
                                                 ->first();
                if ($existingRequest) continue;

                $requestData = [
                    'formation_id' => $formation->id,
                    'user_id' => $user->id,
                    'status' => $statuses[array_rand($statuses)],
                    'motivation' => $motivations[array_rand($motivations)],
                    'priority' => $priorities[array_rand($priorities)],
                    'requested_at' => Carbon::now()->subDays(rand(1, 60)),
                ];

                // Si la demande est approuvée, refusée ou terminée, ajouter des données supplémentaires
                if (in_array($requestData['status'], ['approuve', 'refuse', 'termine'])) {
                    $approver = $managers->random();
                    $requestData['approved_by'] = $approver->id;
                    $requestData['approved_at'] = $requestData['requested_at']->copy()->addDays(rand(1, 10));
                    
                    if ($requestData['status'] === 'refuse') {
                        $rejectionReasons = [
                            'Budget formation déjà alloué pour cette période.',
                            'Cette formation ne correspond pas aux priorités actuelles de votre poste.',
                            'Nous privilégions d\'abord la formation interne sur ce sujet.',
                            'Formation reportée - nous réévaluerons la demande au prochain trimestre.',
                            'Besoin de plus d\'expérience dans votre poste actuel avant cette formation.',
                        ];
                        $requestData['manager_comments'] = $rejectionReasons[array_rand($rejectionReasons)];
                    } elseif ($requestData['status'] === 'approuve') {
                        $approvalComments = [
                            'Formation approuvée - excellent développement pour votre poste.',
                            'Cette formation s\'aligne parfaitement avec vos objectifs professionnels.',
                            'Approuvé - merci de partager vos apprentissages avec l\'équipe au retour.',
                            'Formation validée - j\'attends un retour sur les bonnes pratiques apprises.',
                            'Excellente initiative, cette formation vous sera très utile.',
                        ];
                        $requestData['manager_comments'] = $approvalComments[array_rand($approvalComments)];
                    }
                }

                // Si la formation est terminée, ajouter les données de completion
                if ($requestData['status'] === 'termine') {
                    $requestData['completed_at'] = $requestData['approved_at']->copy()->addDays(rand(5, 30));
                    $requestData['hours_completed'] = $formation->duration_hours + rand(-2, 2); // Petite variation
                    $requestData['rating'] = rand(3, 5); // Notes entre 3 et 5
                    $requestData['final_cost'] = $formation->cost;
                    
                    $feedbacks = [
                        'Formation très enrichissante avec des cas pratiques pertinents. Le formateur était excellent.',
                        'Contenu de qualité et bien structuré. J\'ai pu appliquer immédiatement certaines techniques.',
                        'Formation utile qui m\'a donné de nouveaux outils pour mon quotidien professionnel.',
                        'Très satisfait de cette formation. Les exercices pratiques étaient particulièrement formateurs.',
                        'Formation complète avec un bon équilibre théorie/pratique. Je recommande.',
                        'Excellente formation, très interactive. J\'ai beaucoup appris en peu de temps.',
                    ];
                    
                    if (rand(1, 3) === 1) { // 1 chance sur 3 d'avoir un feedback
                        $requestData['feedback'] = $feedbacks[array_rand($feedbacks)];
                    }
                }

                FormationRequest::create($requestData);
            }
        }

        $totalRequests = FormationRequest::count();
        $this->command->info("Créé {$totalRequests} demandes de formation");
    }
}