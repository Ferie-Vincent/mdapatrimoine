# SCIManager - Project Context

> Ce fichier est la reference principale pour tout agent AI travaillant sur ce projet.
> Genere le 2026-02-10 par BMad Document Project Workflow (Quick Scan).

---

## 1. Presentation du Projet

**Nom** : SCIManager
**Type** : Plateforme web de gestion immobiliere multi-SCI
**Etat** : Brownfield (projet existant en production)
**Repository** : Monolith (codebase unique)

### Objectif
Centraliser la gestion de plusieurs SCI (Societe Civile Immobiliere) : biens, locataires, baux, echeances, paiements, documents PDF, relances, reporting et audit.

### Contexte metier
- **Devise** : FCFA (Franc CFA ouest-africain)
- **Langue** : Francais
- **Cible** : Gestionnaires immobiliers en Afrique de l'Ouest
- **Multi-tenancy** : Par SCI (cloisonnement des donnees par SCI)

---

## 2. Stack Technique

| Composant | Technologie | Version |
|-----------|-------------|---------|
| Backend | PHP | >= 8.2 |
| Framework | Laravel | 12 |
| Base de donnees | MySQL / MariaDB | - |
| ORM | Eloquent | - |
| Frontend | Blade Templates | - |
| CSS | Tailwind CSS | v3 |
| Interactivite | Alpine.js | v3 |
| Graphiques | ApexCharts | - |
| Cartes | Leaflet | - |
| Tableaux | Simple-DataTables | - |
| Build | Vite | v7 |
| PDF | barryvdh/laravel-dompdf | v3.1 |
| Excel/CSV | maatwebsite/excel | v3.1 |
| SMS/WhatsApp | Twilio SDK | v8.10 |
| Auth | Laravel Breeze | v2.3 |
| Tests | PHPUnit | v11.5 |
| Font | Nunito | - |

---

## 3. Architecture

### Pattern : MVC + Service Layer

```
app/
  Console/Commands/     # Commandes Artisan (ExpireLeases)
  Exports/              # 9 classes d'export Excel/CSV
  Http/
    Controllers/        # 22 controleurs (logique mince)
    Middleware/          # CheckRole, SetActiveSci
    Requests/           # Form Requests (validation)
  Jobs/                 # Jobs asynchrones (GenerateMonthlies, SendReminders, MonthlyReport)
  Mail/                 # ReminderMail
  Models/               # 20 modeles Eloquent
  Policies/             # 10 policies d'autorisation (RBAC)
  Providers/            # AppServiceProvider
  Services/             # 8 services metier (coeur logique)
  View/Components/      # AppLayout, GuestLayout
```

### Services metier (app/Services/)
| Service | Responsabilite |
|---------|---------------|
| `AuditService` | Journal d'activite |
| `MonthlyGenerationService` | Generation echeances mensuelles |
| `PaymentService` | Paiements partiels/complets |
| `DocumentService` | Generation PDF (quittances, recus, etc.) |
| `ReminderService` | Relances impayes |
| `ReportService` | Reporting/dashboard KPIs |
| `LeaseService` | Creation/resiliation baux |

### Routes (routes/)
- `web.php` : Routes principales (protegees par auth + middleware SCI)
- `auth.php` : Routes d'authentification (Breeze)
- `console.php` : Commandes schedulees

---

## 4. Modele de Donnees

### 20 Modeles Eloquent (app/Models/)

| Modele | Table | Description |
|--------|-------|-------------|
| `User` | users | Utilisateurs avec role (super_admin, gestionnaire, lecteur) |
| `Sci` | scis | Societes Civiles Immobilieres |
| `Property` | properties | Biens immobiliers (appartements, maisons, etc.) |
| `Tenant` | tenants | Locataires (identite, documents, garants) |
| `Lease` | leases | Baux (lien bien-locataire, duree, montant loyer) |
| `LeaseMonthly` | lease_monthlies | Echeances mensuelles (loyer du, paye, reste) |
| `Payment` | payments | Paiements enregistres |
| `Document` | documents | Documents PDF generes |
| `Reminder` | reminders | Relances pour impayes |
| `AuditLog` | audit_logs | Journal d'audit |
| `ServiceProvider` | service_providers | Prestataires de services |
| `ProviderContract` | provider_contracts | Contrats prestataires |
| `ServiceProvision` | service_provisions | Prestations realisees |
| `MonthlyBudget` | monthly_budgets | Budgets mensuels |
| `FixedCharge` | fixed_charges | Charges fixes |
| `MaterialPurchase` | material_purchases | Achats materiels |
| `DepositRefund` | deposit_refunds | Remboursements caution |
| `StaffMember` | staff_members | Personnel |
| `Payroll` | payrolls | Fiches de paie |

### 30 Migrations (database/migrations/)
- Tables de base Laravel (users, cache, jobs)
- Tables metier SCI (scis, properties, tenants, leases, etc.)
- Tables financieres (budgets, charges, achats, staff, payrolls)
- Extensions (coordonnees GPS, avatars, WhatsApp, settings)

---

## 5. Systeme de Roles et Permissions (RBAC)

### 3 Roles
| Role | Scope | Droits |
|------|-------|--------|
| `super_admin` | Toutes les SCIs | CRUD complet + gestion utilisateurs |
| `gestionnaire` | SCIs assignees | CRUD biens/locataires/baux/paiements + documents |
| `lecteur` | SCIs assignees | Consultation + export uniquement |

### Implementation
- **Middleware** : `CheckRole` (verification role utilisateur)
- **Middleware** : `SetActiveSci` (injection SCI active en session)
- **Policies** : 10 policies Eloquent pour autorisation granulaire
- **Multi-tenancy** : Scope par `sci_id` sur toutes les queries

---

## 6. Modules Fonctionnels

### 6.1 Gestion SCI
- CRUD SCI avec coordonnees GPS (Leaflet)
- Attribution utilisateurs aux SCIs

### 6.2 Gestion Biens Immobiliers
- CRUD avec statuts (disponible, occupe)
- Niveau et porte (etage, numero)
- Coordonnees GPS, galerie photos

### 6.3 Gestion Locataires
- Wizard de creation multi-etapes
- Documents : carte d'identite (recto/verso), recu de paiement
- Contact WhatsApp
- Garants

### 6.4 Gestion Baux
- Creation avec verification qu'aucun bail actif n'existe sur le bien
- Activation : bien passe en statut "occupe"
- Resiliation : bien repasse en "disponible"
- Generation auto des echeances mensuelles

### 6.5 Echeances Mensuelles
- Generation automatique (Job mensuel) ou manuelle
- Statuts : impaye, partiel, paye, en_retard
- Penalites de retard

### 6.6 Paiements
- Enregistrement paiement complet ou partiel
- Mise a jour automatique du reste a payer
- Recu de paiement PDF

### 6.7 Documents PDF (dompdf)
Templates : quittance, recu de paiement, avis d'echeance, attestation, releve de compte locataire, rapport mensuel SCI, recu caution, recu agence, fiche locataire
- Stockage : `storage/app/documents/{sci_id}/{year}/{month}/`

### 6.8 Relances
- Manuelles et automatiques (Job quotidien)
- Niveaux de relance avec tracking
- Envoi email (ReminderMail)
- Envoi SMS/WhatsApp (Twilio)

### 6.9 Exports
- 9 classes d'export : SCIs, Properties, Tenants, Leases, Monthlies, Payments, Unpaid, ServiceProviders, Staff, StaffPayroll
- Formats : Excel, CSV, PDF (table-pdf)

### 6.10 Dashboard et Analytics
- KPIs en temps reel (taux occupation, recouvrement, impayes)
- Graphiques ApexCharts (couleurs brand)
- Page analytics dediee

### 6.11 Journal d'Audit
- Traçabilite de toutes les actions
- AuditService centralise

### 6.12 Prestataires et Contrats
- Gestion prestataires de services
- Contrats avec suivi

### 6.13 Personnel et Paie
- Fiches personnel (StaffMember)
- Fiches de paie (Payroll)

---

## 7. Jobs et Taches Planifiees

| Job | Frequence | Description |
|-----|-----------|-------------|
| `GenerateMonthliesJob` | 1er du mois 01h | Genere echeances + penalites |
| `SendRemindersJob` | Quotidien 08h | Genere et envoie relances |
| `GenerateMonthlySciReportJob` | 1er du mois 06h | Recap mensuel par SCI |
| `ExpireLeases` (Command) | Quotidien 00h30 | Met a jour statuts en retard |

Cron : `* * * * * php artisan schedule:run`

---

## 8. Design et UI

### Couleurs du Logo Madoud's Art
| Couleur | Hex | Usage |
|---------|-----|-------|
| Bleu Navy | `#1E3A8A` | Brand principal |
| Orange | `#D4812A` | Accent chaud |
| Jaune | `#F0D020` | Highlight |
| Rouge | `#C42618` | Danger/alerte |
| Vert | `#1E9B3E` | Succes |

### Composants UI
- **Sidebar** : Dark navy (`#0F1D3D`), items actifs `bg-white/10 text-white`
- **Navigation mobile** : Barre en bas avec 5 items
- **Stat cards** : Icones gradient + blobs decoratifs colores
- **Tableaux** : thead `bg-brand-50/40`, hover `hover:bg-brand-50/20`
- **Font** : Nunito

### Composants Blade reutilisables (resources/views/components/)
`stat-card`, `table`, `badge`, `modal`, `form-modal`, `wizard-modal`, `empty-state`, `filters`, `export-dropdown`, `money-input`, `file-upload`, `multi-photo-upload`, `primary-button`, `secondary-button`, `danger-button`, `text-input`, `input-label`, `input-error`, `dropdown`, `dropdown-link`, `nav-link`, `responsive-nav-link`, `auth-session-status`, `application-logo`

---

## 9. Vues (resources/views/)

| Dossier | Contenu |
|---------|---------|
| `layouts/` | app.blade.php (sidebar), guest.blade.php, navigation.blade.php |
| `dashboard/` | index.blade.php (KPIs + charts) |
| `scis/` | index, show, _form |
| `properties/` | index, show, _form |
| `tenants/` | index, show, _wizard_steps |
| `leases/` | index, show |
| `monthlies/` | index, show |
| `payments/` | index, show |
| `documents/` | index, show |
| `reminders/` | index |
| `staff/` | index |
| `service-providers/` | index, _form, _contract-form |
| `users/` | index, show, _form |
| `audit-logs/` | index, show |
| `analytics/` | index |
| `gallery/` | index |
| `settings/` | index |
| `pdf/` | quittance, payment_receipt, tenant_statement, reminder, attestation, rent_notice, monthly_report, recu-excel, quittance-excel, fiche-locataire-excel, recu-entree-caution, recu-entree-agence |
| `excel/` | dossier-card, database, financial-current, monthly-management |
| `auth/` | login, register, forgot-password, reset-password, confirm-password, verify-email |
| `profile/` | edit + partials |
| `emails/` | reminder |
| `exports/` | table-pdf |

---

## 10. Configuration

### Fichiers Config (config/)
`app.php`, `auth.php`, `cache.php`, `database.php`, `dompdf.php`, `filesystems.php`, `logging.php`, `mail.php`, `queue.php`, `services.php`, `session.php`

### Environnement (.env)
- DB : MySQL (scimanager, root/root, port 3306)
- Queue : database driver
- Mail : SMTP configurable
- Twilio : SID, Token, WhatsApp number

---

## 11. Developpement

### Prerequis
- PHP 8.3+, Composer, MySQL/MariaDB, Node.js 18+/npm

### Commandes
```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan db:seed
php artisan serve      # Serveur dev
npm run dev            # Vite en mode dev
php artisan queue:work # Queue worker
php artisan schedule:work # Scheduler
```

### Comptes de test
| Email | Mot de passe | Role |
|-------|-------------|------|
| admin@scimanager.com | password | Super Admin |
| gestionnaire@scimanager.com | password | Gestionnaire |
| lecteur@scimanager.com | password | Lecture seule |

### Tests
```bash
php artisan test
```

---

## 12. Regles Critiques pour les Agents AI

1. **TOUJOURS** respecter le scope SCI : toutes les queries doivent filtrer par `sci_id`
2. **JAMAIS** supprimer de donnees en dur : utiliser soft deletes ou changement de statut
3. **TOUJOURS** logger dans AuditService pour toute action CRUD
4. **RESPECTER** les 3 roles et les policies existantes
5. **UTILISER** les Services existants pour la logique metier (jamais dans les controleurs)
6. **DEVISE** : FCFA, formater avec `number_format($amount, 0, ',', ' ')` + ' FCFA'
7. **LANGUE** : Interface en francais, messages de validation en francais
8. **DESIGN** : Respecter les couleurs Madoud's Art et le style existant (Tailwind classes)
9. **COMPOSANTS** : Reutiliser les composants Blade existants avant d'en creer de nouveaux
10. **PDF** : Utiliser dompdf avec les templates Blade existants dans `resources/views/pdf/`
11. **EXPORTS** : Suivre le pattern maatwebsite/excel dans `app/Exports/`
12. **MIDDLEWARE** : Toute route protegee doit passer par `auth`, `CheckRole`, `SetActiveSci`
