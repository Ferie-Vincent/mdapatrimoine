# SCIManager - MDA Patrimoine

Plateforme web de gestion immobiliere et de patrimoine multi-SCI, developpee pour **Madoud's Art Patrimoine (MDA)**.

**Stack** : Laravel 12 &bull; PHP 8.2+ &bull; MySQL &bull; Blade &bull; Tailwind CSS 3 &bull; Alpine.js &bull; PWA

---

## Fonctionnalites

### Gestion locative
- **Multi-SCI** : cloisonnement des donnees par SCI, bascule rapide entre entites
- **Biens immobiliers** : CRUD complet, statuts, photos multiples, galerie
- **Locataires** : identite, documents, garants, historique
- **Baux** : creation, activation, resiliation, archivage
- **Echeances mensuelles** : generation automatique et manuelle, suivi par mois
- **Paiements** : complet, partiel, impaye, remboursement de caution
- **Relances** : manuelles et automatiques (SMS via Twilio)

### Point financier courant
- **Prestations de services** : suivi des interventions (electricite, plomberie, menuiserie, etc.)
- **Achats de materiel** : suivi des achats fournisseurs
- **Charges fixes** : CIE, SODECI, honoraires avec filtres par type
- **Budget mensuel (Caisse)** : definition et suivi du solde
- **Attestations de reception de fonds** : generation avec signature numerique

### Personnel & Prestataires
- **Annuaire prestataires** : fiche prestataire, contrats, suivi
- **Personnel & Paie** : gestion du personnel, fiches de paie mensuelles

### Documents & Exports
- **Generation PDF** : quittances, recus, avis d'echeance, attestations, releves, recaps mensuels, fiches locataire
- **Exports CSV/Excel** : locataires, biens, paiements, impayes, baux, echeances, prestataires, personnel, SCIs

### Reporting & Analytique
- **Dashboard** : KPIs temps reel (total attendu, encaisse, impaye, taux de recouvrement, occupation)
- **Analytique** : graphiques de tendances, repartition par SCI, evolution mensuelle
- **Recherche globale** : recherche instantanee multi-entites

### Administration
- **RBAC** : 3 roles (Super Admin, Gestionnaire, Lecture seule)
- **Gestion des utilisateurs** : CRUD, assignation aux SCIs
- **Journal d'activite** : audit log de toutes les actions
- **Parametres** : configuration globale de la plateforme

### PWA & Responsive
- **Progressive Web App** : installable sur mobile/tablette, mode offline basique
- **Responsive** : interface optimisee pour desktop, tablette et mobile
- **Navigation mobile** : barre de navigation en bas + menu lateral overlay

---

## Prerequis

- PHP 8.2+
- Composer 2.x
- MySQL 8.0+ / MariaDB 10.6+
- Node.js 18+ / npm

---

## Installation

```bash
# Cloner le depot
git clone <url-du-repo> scimanager
cd scimanager

# Dependances PHP
composer install

# Dependances JS et compilation
npm install && npm run build

# Configuration
cp .env.example .env
php artisan key:generate

# Lien de stockage public
php artisan storage:link
```

## Configuration

Copier `.env.example` vers `.env` et renseigner les variables d'environnement :

```env
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Les variables Twilio (optionnelles, pour les relances SMS) sont a renseigner dans le `.env`.

## Migration

```bash
php artisan migrate
php artisan db:seed
```

---

## Lancement

```bash
# Serveur de developpement
php artisan serve

# Compilation des assets (mode dev)
npm run dev

# Queue worker
php artisan queue:work

# Scheduler local
php artisan schedule:work
```

---

## Roles et permissions

| Action                        | Super Admin | Gestionnaire | Lecture seule |
|-------------------------------|:-----------:|:------------:|:-------------:|
| Voir toutes les SCIs          |     Oui     |  Assignees   |  Assignees    |
| Creer/modifier SCI            |     Oui     |     Non      |     Non       |
| Gerer utilisateurs/parametres |     Oui     |     Non      |     Non       |
| CRUD Biens/Locataires/Baux    |     Oui     | SCI assignees|     Non       |
| Enregistrer paiements         |     Oui     | SCI assignees|     Non       |
| Point financier courant       |     Oui     | SCI assignees|     Non       |
| Generer documents/attestations|     Oui     | SCI assignees|     Non       |
| Personnel & Prestataires      |     Oui     | SCI assignees|     Non       |
| Consulter / Exporter          |     Oui     |     Oui      |     Oui       |

---

## Scheduler (production)

```cron
* * * * * cd /path/to/scimanager && php artisan schedule:run >> /dev/null 2>&1
```

---

## Dependances principales

| Package                  | Usage                          |
|--------------------------|--------------------------------|
| `laravel/framework` 12   | Framework PHP                  |
| `laravel/breeze`         | Authentification               |
| `barryvdh/laravel-dompdf`| Generation PDF                 |
| `maatwebsite/excel`      | Exports Excel/CSV              |
| `twilio/sdk`             | Envoi SMS (relances)           |
| Tailwind CSS 3           | Styles CSS utilitaires         |
| Alpine.js                | Interactivite cote client      |
| ApexCharts               | Graphiques et analytique       |
| Simple-DataTables        | Tri des tableaux cote client   |

---

## Licence

Projet prive - MDA Patrimoine.
