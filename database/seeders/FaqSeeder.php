<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // Catégorie Général
            [
                'question' => 'Comment accéder à l\'intranet depuis l\'extérieur ?',
                'answer' => 'L\'accès à l\'intranet depuis l\'extérieur se fait via VPN. Contactez le service IT pour obtenir vos identifiants VPN et le guide d\'installation. Une fois connecté au VPN, vous pourrez accéder à l\'intranet normalement.',
                'category' => 'général',
                'order' => 1,
                'is_active' => true
            ],
            [
                'question' => 'Que faire si j\'ai oublié mon mot de passe ?',
                'answer' => 'En cas d\'oubli de mot de passe, cliquez sur "Mot de passe oublié" sur la page de connexion ou contactez votre administrateur système qui pourra réinitialiser votre mot de passe.',
                'category' => 'général',
                'order' => 2,
                'is_active' => true
            ],
            [
                'question' => 'Comment modifier mes informations personnelles ?',
                'answer' => 'Rendez-vous dans votre profil en cliquant sur votre avatar en haut à droite, puis sur "Mon profil". Vous pouvez y modifier vos informations personnelles, sauf votre rôle qui doit être modifié par un administrateur.',
                'category' => 'général',
                'order' => 3,
                'is_active' => true
            ],

            // Catégorie Missions
            [
                'question' => 'Comment créer une nouvelle mission ?',
                'answer' => 'Seuls les managers et administrateurs peuvent créer des missions. Allez dans l\'onglet "Missions" puis cliquez sur "Créer une mission". Remplissez tous les champs obligatoires et assignez la mission aux collaborateurs concernés.',
                'category' => 'missions',
                'order' => 1,
                'is_active' => true
            ],
            [
                'question' => 'Comment suivre l\'avancement d\'une mission ?',
                'answer' => 'Dans l\'onglet "Missions", cliquez sur la mission souhaitée pour voir le détail. Vous y trouverez le statut, les commentaires, les fichiers joints et l\'historique des actions.',
                'category' => 'missions',
                'order' => 2,
                'is_active' => true
            ],
            [
                'question' => 'Puis-je modifier le statut d\'une mission ?',
                'answer' => 'Les collaborateurs assignés peuvent marquer une mission comme "En cours" ou "Terminée". Seuls les managers et créateurs de la mission peuvent modifier tous les statuts et supprimer une mission.',
                'category' => 'missions',
                'order' => 3,
                'is_active' => true
            ],

            // Catégorie Demandes
            [
                'question' => 'Quels types de demandes puis-je faire ?',
                'answer' => 'Vous pouvez faire plusieurs types de demandes : demande d\'achat de produits communication, demande de documentation manager, demandes de prestations (location, syndic, ménage, travaux, autres administratifs). Chaque demande suit un workflow de validation.',
                'category' => 'demandes',
                'order' => 1,
                'is_active' => true
            ],
            [
                'question' => 'Combien de temps prend le traitement d\'une demande ?',
                'answer' => 'Le délai dépend du type de demande et du workflow de validation. En général : demandes d\'achat (2-5 jours), demandes de documentation (1-2 jours), demandes de prestations (3-7 jours). Vous recevez des notifications à chaque étape.',
                'category' => 'demandes',
                'order' => 2,
                'is_active' => true
            ],
            [
                'question' => 'Comment suivre ma demande ?',
                'answer' => 'Dans l\'onglet "Demandes", vous pouvez voir toutes vos demandes avec leur statut : En attente, Validée, Rejetée, ou Terminée. Cliquez sur une demande pour voir les détails et commentaires.',
                'category' => 'demandes',
                'order' => 3,
                'is_active' => true
            ],

            // Catégorie Formations
            [
                'question' => 'Comment s\'inscrire à une formation ?',
                'answer' => 'Allez dans l\'onglet "Formations", parcourez le catalogue et cliquez sur "Demander une participation" pour la formation qui vous intéresse. Votre demande sera soumise à validation.',
                'category' => 'formations',
                'order' => 1,
                'is_active' => true
            ],
            [
                'question' => 'Qui valide les demandes de formation ?',
                'answer' => 'Les demandes de formation sont validées par votre manager direct ou un administrateur RH. Ils prennent en compte vos besoins, votre charge de travail et le budget formation.',
                'category' => 'formations',
                'order' => 2,
                'is_active' => true
            ],
            [
                'question' => 'Comment consulter mon parcours de formation ?',
                'answer' => 'Dans l\'onglet "Formations", cliquez sur "Mes formations" pour voir votre historique : formations suivies, en cours, et demandes en attente. Votre profil affiche aussi vos heures de formation de l\'année.',
                'category' => 'formations',
                'order' => 3,
                'is_active' => true
            ],

            // Catégorie Technique
            [
                'question' => 'L\'intranet est lent, que faire ?',
                'answer' => 'Plusieurs solutions : vider le cache de votre navigateur, vérifier votre connexion internet, fermer les onglets inutiles. Si le problème persiste, contactez le support technique avec les détails (navigateur, heure, actions effectuées).',
                'category' => 'technique',
                'order' => 1,
                'is_active' => true
            ],
            [
                'question' => 'Sur quels navigateurs l\'intranet fonctionne-t-il ?',
                'answer' => 'L\'intranet est compatible avec : Chrome (recommandé), Firefox, Safari, Edge. Versions récentes uniquement (moins de 2 ans). Internet Explorer n\'est pas supporté.',
                'category' => 'technique',
                'order' => 2,
                'is_active' => true
            ],
            [
                'question' => 'Puis-je utiliser l\'intranet sur mobile ?',
                'answer' => 'Oui, l\'intranet est responsive et s\'adapte aux smartphones et tablettes. L\'expérience est optimisée pour les écrans tactiles tout en conservant toutes les fonctionnalités.',
                'category' => 'technique',
                'order' => 3,
                'is_active' => true
            ]
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}