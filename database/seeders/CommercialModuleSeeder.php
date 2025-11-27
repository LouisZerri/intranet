<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Quote;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Mission;


class CommercialModuleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Ce seeder orchestre tous les seeders du module commercial
     * dans le bon ordre pour respecter les dépendances.
     */
    public function run(): void
    {
        $this->command->info('🚀 Démarrage du seeding du module commercial...');
        $this->command->newLine();

        // Vérification des prérequis
        $this->command->info('📋 Vérification des prérequis...');
        
        $userCount = \App\Models\User::where('is_active', true)->count();
        if ($userCount === 0) {
            $this->command->error('❌ Aucun utilisateur actif trouvé !');
            $this->command->warn('⚠️  Veuillez d\'abord créer des utilisateurs (collaborateurs/managers)');
            return;
        }
        
        $this->command->info("✅ {$userCount} utilisateur(s) actif(s) trouvé(s)");
        $this->command->newLine();

        // 1. Clients (base de données clients)
        $this->command->info('👥 Création des clients...');
        $this->call(ClientSeeder::class);
        $this->command->newLine();

        // 2. Devis (avec leurs lignes)
        $this->command->info('📋 Création des devis...');
        $this->call(QuoteSeeder::class);
        $this->command->newLine();

        // 3. Factures (avec paiements)
        $this->command->info('💰 Création des factures et paiements...');
        $this->call(InvoiceSeeder::class);
        $this->command->newLine();

        // Statistiques finales
        $this->displayStatistics();
    }

    /**
     * Afficher les statistiques après seeding
     */
    private function displayStatistics(): void
    {
        $this->command->info('📊 STATISTIQUES DU MODULE COMMERCIAL');
        $this->command->info('═══════════════════════════════════════');
        
        // Clients
        $clientsTotal = Client::count();
        $clientsParticuliers = Client::where('type', 'particulier')->count();
        $clientsProfessionnels = Client::where('type', 'professionnel')->count();
        
        $this->command->info("👥 Clients : {$clientsTotal}");
        $this->command->info("   • Particuliers : {$clientsParticuliers}");
        $this->command->info("   • Professionnels : {$clientsProfessionnels}");
        $this->command->newLine();

        // Devis
        $quotesTotal = Quote::count();
        $quotesBrouillon = Quote::where('status', 'brouillon')->count();
        $quotesEnvoye = Quote::where('status', 'envoye')->count();
        $quotesAccepte = Quote::where('status', 'accepte')->count();
        $quotesConverti = Quote::where('status', 'converti')->count();
        $quotesRefuse = Quote::where('status', 'refuse')->count();
        
        $this->command->info("📋 Devis : {$quotesTotal}");
        $this->command->info("   • Brouillon : {$quotesBrouillon}");
        $this->command->info("   • Envoyés : {$quotesEnvoye}");
        $this->command->info("   • Acceptés : {$quotesAccepte}");
        $this->command->info("   • Convertis : {$quotesConverti}");
        $this->command->info("   • Refusés : {$quotesRefuse}");
        
        $tauxTransformation = $quotesTotal > 0 
            ? round((($quotesAccepte + $quotesConverti) / $quotesTotal) * 100, 1)
            : 0;
        $this->command->info("   📈 Taux de transformation : {$tauxTransformation}%");
        $this->command->newLine();

        // Factures
        $invoicesTotal = Invoice::count();
        $invoicesBrouillon = Invoice::where('status', 'brouillon')->count();
        $invoicesEmise = Invoice::where('status', 'emise')->count();
        $invoicesPayee = Invoice::where('status', 'payee')->count();
        $invoicesRetard = Invoice::where('status', 'en_retard')->count();
        
        $this->command->info("💰 Factures : {$invoicesTotal}");
        $this->command->info("   • Brouillon : {$invoicesBrouillon}");
        $this->command->info("   • Émises : {$invoicesEmise}");
        $this->command->info("   • Payées : {$invoicesPayee}");
        $this->command->info("   • En retard : {$invoicesRetard}");
        $this->command->newLine();

        // Chiffres d'affaires
        $caTotal = Invoice::where('status', 'payee')->sum('total_ht');
        $caEnAttente = Invoice::whereIn('status', ['emise', 'en_retard'])->sum('total_ttc');
        
        $this->command->info("💵 Chiffre d'affaires :");
        $this->command->info("   • CA payé (HT) : " . number_format($caTotal, 2, ',', ' ') . " €");
        $this->command->info("   • En attente (TTC) : " . number_format($caEnAttente, 2, ',', ' ') . " €");
        $this->command->newLine();

        // Paiements
        $paymentsCount = InvoicePayment::count();
        $paymentsTotal = InvoicePayment::sum('amount');
        
        $this->command->info("💳 Paiements : {$paymentsCount} enregistrement(s)");
        $this->command->info("   • Montant total : " . number_format($paymentsTotal, 2, ',', ' ') . " €");
        $this->command->newLine();

        // Missions créées depuis devis
        $missionsFromQuotes = Mission::whereNotNull('quote_id')->count();
        if ($missionsFromQuotes > 0) {
            $this->command->info("🎯 Missions créées automatiquement : {$missionsFromQuotes}");
            $this->command->newLine();
        }

        $this->command->info('═══════════════════════════════════════');
        $this->command->info('✅ Seeding du module commercial terminé !');
        $this->command->newLine();
        
        // Conseils
        $this->command->warn('💡 CONSEILS :');
        $this->command->info('   • Utilisez php artisan tinker pour explorer les données');
        $this->command->info('   • Les devis "acceptés" ont créé des missions automatiquement');
        $this->command->info('   • Les factures "payées" sont comptabilisées dans le CA');
        $this->command->info('   • Certaines factures ont des paiements partiels');
    }
}