# SCIManager - Gestion Immobiliere Multi-SCI

Plateforme web de gestion immobiliere multi-SCI.
Stack : Laravel 12, MySQL, Blade + Tailwind CSS, Alpine.js.

## Fonctionnalites

- Gestion multi-SCI avec cloisonnement des donnees
- RBAC : Super Admin, Gestionnaire, Lecture seule
- Gestion des biens immobiliers (CRUD, statuts)
- Gestion des locataires (identite, documents, garants)
- Gestion des baux (creation, activation, resiliation)
- Generation automatique des echeances mensuelles
- Suivi des paiements (complet, partiel, impaye)
- Generation PDF : quittances, recus, avis, attestations, releves, recaps
- Relances pour impayes (manuelles et automatiques)
- Dashboard avec KPIs et reporting
- Exports CSV/Excel
- Journal d'activite (audit log)

## Prerequis

- PHP 8.3+
- Composer
- MySQL/MariaDB
- Node.js 18+ / npm

## Installation

```bash
# Installer les dependances PHP
composer install

# Installer les dependances JS et compiler
npm install && npm run build

# Copier et configurer l'environnement
cp .env.example .env
php artisan key:generate
```

## Configuration base de donnees

Modifier `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=scimanager
DB_USERNAME=root
DB_PASSWORD=root
```

Creer la base MySQL :

```sql
CREATE DATABASE scimanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Migration et seeding

```bash
php artisan migrate
php artisan db:seed
```

### Comptes de test (apres seeding)

| Email                       | Mot de passe | Role          |
|-----------------------------|-------------|---------------|
| admin@scimanager.com        | password    | Super Admin   |
| gestionnaire@scimanager.com | password    | Gestionnaire  |
| lecteur@scimanager.com      | password    | Lecture seule |

## Lancement

```bash
# Serveur de dev
php artisan serve

# Compiler les assets en mode dev (autre terminal)
npm run dev

# Queue worker (autre terminal)
php artisan queue:work

# Scheduler local (autre terminal)
php artisan schedule:work
```

Acceder a l'application : http://localhost:8000

## Architecture

```
app/
  Http/
    Controllers/     # Controleurs REST (logique mince)
    Middleware/       # CheckRole, SetActiveSci
    Requests/         # Form Requests (validation)
  Models/            # Eloquent models (relations, scopes, casts)
  Policies/          # Authorization policies (RBAC)
  Services/          # Logique metier
    LeaseService.php              # Creation/resiliation baux
    MonthlyGenerationService.php  # Generation echeances
    PaymentService.php            # Paiements partiels/complets
    DocumentService.php           # Generation PDF
    ReminderService.php           # Relances
    ReportService.php             # Reporting/dashboard
    AuditService.php              # Journal d'activite
  Jobs/              # Jobs asynchrones
  Exports/           # Classes d'export Excel/CSV
database/
  migrations/        # Schema BDD
  factories/         # Factories pour tests
  seeders/           # Donnees de test
resources/views/
  layouts/           # Layout principal (sidebar)
  components/        # Composants Blade reutilisables
  dashboard/         # Dashboard
  scis/              # CRUD SCIs
  properties/        # CRUD Biens
  tenants/           # CRUD Locataires
  leases/            # CRUD Baux
  monthlies/         # Echeances mensuelles
  payments/          # Paiements
  documents/         # Documents generes
  reminders/         # Relances
  audit-logs/        # Journal d'activite
  pdf/               # Templates PDF (dompdf)
```

## Flux metiers

### Creation d'un bail
1. Selectionner SCI -> Bien (disponible) -> Locataire
2. `LeaseService::createLease()` :
   - Verifie qu'aucun bail actif n'existe sur le bien
   - Cree le bail
   - Passe le bien en statut "occupe"
   - Genere les echeances mensuelles
   - Log audit

### Generation des echeances
- Automatique : Job mensuel `GenerateMonthliesJob`
- Manuelle : bouton "Generer echeances" dans l'interface
- `MonthlyGenerationService::generateForLease()` cree un enregistrement par mois

### Enregistrement d'un paiement
1. Selectionner une echeance impayee
2. `PaymentService::recordPayment()` :
   - Cree l'enregistrement paiement
   - Met a jour `paid_amount` et `remaining_amount`
   - Statut : paye (reste=0), partiel (reste>0 et paye>0)
   - Log audit

### Generation de documents PDF
- `DocumentService` genere via Blade + dompdf
- Stockage : `storage/app/documents/{sci_id}/{year}/{month}/`
- Enregistrement en base (table `documents`)

## Scheduler / Jobs

| Job                           | Frequence        | Description                              |
|-------------------------------|------------------|------------------------------------------|
| `GenerateMonthliesJob`        | 1er du mois 01h  | Genere les echeances + penalites         |
| `SendRemindersJob`            | Quotidien 08h    | Genere et envoie les relances            |
| `GenerateMonthlySciReportJob` | 1er du mois 06h  | Recap mensuel par SCI                    |
| Mise a jour statuts           | Quotidien 00h30  | impayes -> en_retard si date depassee    |

Cron systeme :

```cron
* * * * * cd /path/to/scimanager && php artisan schedule:run >> /dev/null 2>&1
```

## Tests

```bash
php artisan test
```

Tests Feature :
- `LeaseServiceTest` : creation bail, occupation bien, resiliation
- `MonthlyGenerationServiceTest` : generation echeances, doublons, penalites
- `PaymentServiceTest` : paiement complet, partiel, cumul
- `DocumentServiceTest` : generation PDF quittance, rapport, avis

## Roles et permissions

| Action                     | Super Admin | Gestionnaire | Lecture seule |
|----------------------------|:-----------:|:------------:|:-------------:|
| Voir toutes les SCIs       | Oui         | Assignees    | Assignees     |
| Creer/modifier SCI         | Oui         | Non          | Non           |
| CRUD Biens/Locataires/Baux | Oui         | SCI assignees| Non           |
| Enregistrer paiements      | Oui         | SCI assignees| Non           |
| Generer documents          | Oui         | SCI assignees| Non           |
| Consulter / Exporter       | Oui         | Oui          | Oui           |
| Journal d'activite         | Oui         | Oui          | Oui           |
# mdapatrimoine
