<?php

/**
 * Fichier principal de routage
 * 
 * Ce fichier charge tous les fichiers de routes séparés par domaine fonctionnel.
 * Chaque fichier de routes est responsable d'un module ou d'un groupe de rôles spécifique.
 */

// ============================================================================
// ROUTES D'AUTHENTIFICATION
// ============================================================================
// - Routes publiques (login, Google OAuth)
// - Routes protégées (logout, dashboard)
require __DIR__.'/auth.php';

// ============================================================================
// ROUTES DE PROFIL UTILISATEUR
// ============================================================================
// - Accessibles à tous les utilisateurs authentifiés
require __DIR__.'/profile.php';

// ============================================================================
// ROUTES ADMINISTRATEUR
// ============================================================================
// - Gestion complète : formations, communication, équipe, documentation
require __DIR__.'/admin.php';

// ============================================================================
// ROUTES MANAGERS & ADMINISTRATEURS
// ============================================================================
// - Gestion : actualités, formations, recrutement, statistiques
require __DIR__.'/manager.php';

// ============================================================================
// ROUTES COLLABORATEURS, MANAGERS & ADMINISTRATEURS
// ============================================================================
// - Consultation et utilisation : actualités, missions, communication, formations, documentation
require __DIR__.'/collaborator.php';

// ============================================================================
// ROUTES MODULE COMMERCIAL
// ============================================================================
// - Accessibles aux collaborateurs, managers et administrateurs
// - Gestion : clients, devis, factures, URSSAF
require __DIR__.'/commercial.php';
