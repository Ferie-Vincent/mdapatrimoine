# SCIManager - Documentation Technique et Fonctionnelle

## Table des matieres

1. [Presentation generale](#1-presentation-generale)
2. [Stack technique](#2-stack-technique)
3. [Architecture du projet](#3-architecture-du-projet)
4. [Modele de donnees](#4-modele-de-donnees)
5. [Systeme de roles et permissions](#5-systeme-de-roles-et-permissions)
6. [Multi-tenancy SCI](#6-multi-tenancy-sci)
7. [Modules fonctionnels](#7-modules-fonctionnels)
8. [Generation de documents PDF](#8-generation-de-documents-pdf)
9. [Systeme d'exports](#9-systeme-dexports)
10. [Systeme de relances](#10-systeme-de-relances)
11. [Journal d'audit](#11-journal-daudit)
12. [Taches planifiees](#12-taches-planifiees)
13. [Regles metier cles](#13-regles-metier-cles)
14. [Structure des routes](#14-structure-des-routes)
15. [Composants Blade](#15-composants-blade)
16. [Schema relationnel](#16-schema-relationnel)

---

## 1. Presentation generale

**SCIManager** est une plateforme de gestion immobiliere multi-SCI developpee en Laravel. Elle permet de gerer une ou plusieurs **SCI** (Societe Civile Immobiliere) et l'ensemble de leur activite locative : biens immobiliers, locataires, baux, echeances mensuelles, paiements, generation de documents officiels (quittances, recus, attestations...), relances automatiques et suivi comptable.

### Objectifs de la plateforme

- **Centraliser** la gestion de plusieurs SCI dans une interface unique
- **Automatiser** la generation des echeances mensuelles, des penalites de retard et des relances
- **Produire** les documents officiels en PDF (quittances, recus, avis d'echeance, attestations, releves de compte)
- **Suivre** les paiements et le taux de recouvrement en temps reel
- **Tracer** l'ensemble des actions via un journal d'audit complet
- **Controler** les acces par un systeme de roles a trois niveaux

### Devise et langue

- **Devise** : FCFA (Franc CFA ouest-africain)
- **Langue de l'interface** : Francais
- **Messages de validation** : Francais

---

## 2. Stack technique

| Composant | Technologie | Version |
|---|---|---|
| Backend | PHP | >= 8.2 |
| Framework | Laravel | 12 |
| Authentification | Laravel Breeze | 2.3 |
| PDF | barryvdh/laravel-dompdf | 3.1 |
| Exports XLSX/CSV | maatwebsite/excel | 3.1 |
| Frontend CSS | Tailwind CSS | via Vite |
| Frontend JS | Alpine.js | via Vite |
| Build | Vite | - |
| Qualite code | Laravel Pint | 1.24 |
| Tests | PHPUnit | - |

### Dependances de developpement

- `laravel/pail` : Visualisation des logs en temps reel
- `laravel/sail` : Environnement Docker
- `fakerphp/faker` : Donnees de test
- `mockery/mockery` : Mocking pour les tests

---

## 3. Architecture du projet

```
SCIManager/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 14 controllers
│   │   ├── Middleware/          # CheckRole, SetActiveSci
│   │   └── Requests/           # 14 form requests de validation
│   ├── Jobs/                   # 3 jobs planifies
│   ├── Models/                 # 10 modeles Eloquent
│   ├── Policies/               # 9 policies d'autorisation
│   ├── Providers/              # AppServiceProvider (policies + Gate::before)
│   └── Services/               # 7 services metier
├── database/
│   └── migrations/             # 13 migrations
├── resources/
│   └── views/
│       ├── components/         # 18 composants Blade reutilisables
│       ├── layouts/            # Layout principal (sidebar + header)
│       ├── pdf/                # 7 templates PDF
│       ├── auth/               # Vues authentification (Breeze)
│       ├── dashboard/          # Tableau de bord
│       ├── properties/         # CRUD biens
│       ├── tenants/            # CRUD locataires
│       ├── leases/             # CRUD baux
│       ├── monthlies/          # Echeances mensuelles
│       ├── payments/           # Paiements
│       ├── documents/          # Documents generes
│       ├── reminders/          # Relances
│       ├── scis/               # CRUD SCIs
│       ├── users/              # CRUD utilisateurs
│       └── audit-logs/         # Journal d'audit
├── routes/
│   ├── web.php                 # Routes principales
│   ├── auth.php                # Routes d'authentification (Breeze)
│   └── console.php             # Taches planifiees (schedule)
└── bootstrap/
    └── app.php                 # Configuration middleware
```

---

## 4. Modele de donnees

### 4.1 Users (Utilisateurs)

| Champ | Type | Description |
|---|---|---|
| `name` | string | Nom complet |
| `email` | string, unique | Adresse e-mail |
| `password` | string (hashed) | Mot de passe (hash automatique via cast) |
| `role` | enum | `super_admin`, `gestionnaire`, `lecture_seule` |
| `is_active` | boolean | Compte actif ou desactive |
| `email_verified_at` | datetime | Date de verification de l'email |

**Relations** : Appartient a plusieurs SCIs (pivot `sci_user`), possede plusieurs AuditLogs.

### 4.2 SCIs (Societes Civiles Immobilieres)

| Champ | Type | Description |
|---|---|---|
| `name` | string | Nom de la SCI |
| `rccm` | string, nullable | Numero RCCM |
| `ifu` | string, nullable | Numero IFU |
| `address` | text, nullable | Adresse du siege |
| `phone` | string, nullable | Telephone |
| `email` | string, nullable | E-mail de contact |
| `bank_name` | string, nullable | Nom de la banque |
| `bank_iban` | string, nullable | IBAN bancaire |
| `logo_path` | string, nullable | Chemin du logo (upload) |
| `is_active` | boolean | SCI active ou non |

**Relations** : Appartient a plusieurs Users, possede des Properties, Tenants, Leases, LeasesMonthlies, Payments, Documents, Reminders, AuditLogs.

**Suppression douce** (SoftDeletes) activee.

### 4.3 Properties (Biens immobiliers)

| Champ | Type | Description |
|---|---|---|
| `sci_id` | FK -> scis | SCI proprietaire |
| `reference` | string, unique | Reference du bien (ex: APP-001) |
| `type` | enum | `appartement`, `maison`, `studio`, `bureau`, `commerce`, `terrain`, `autre` |
| `address` | text | Adresse complete |
| `city` | string, nullable | Ville |
| `description` | text, nullable | Description detaillee |
| `surface` | decimal(10,2) | Surface en m² |
| `rooms` | integer, nullable | Nombre de pieces |
| `status` | enum | `disponible`, `occupe`, `travaux` |
| `rent_reference` | decimal(12,2) | Loyer de reference (FCFA) |
| `charges_reference` | decimal(12,2) | Charges de reference (FCFA) |
| `nb_keys` | integer, nullable | Nombre de cles |
| `nb_clim` | integer, nullable | Nombre de climatiseurs |
| `photos` | JSON, nullable | Tableau de chemins de photos |

**Relations** : Appartient a une SCI, possede plusieurs Leases.

**Suppression douce** activee. Index sur `[sci_id, status]`.

### 4.4 Tenants (Locataires)

| Champ | Type | Description |
|---|---|---|
| `sci_id` | FK -> scis | SCI du locataire |
| `first_name` | string | Prenom |
| `last_name` | string | Nom de famille |
| `email` | string, nullable | E-mail |
| `phone` | string | Telephone principal |
| `phone_secondary` | string, nullable | Telephone secondaire |
| `address` | text, nullable | Adresse personnelle |
| `id_type` | string, nullable | Type de piece d'identite (CNI, Passeport...) |
| `id_number` | string, nullable | Numero de la piece |
| `id_expiration` | date, nullable | Date d'expiration |
| `id_file_path` | string, nullable | Scan de la piece (upload) |
| `profession` | string, nullable | Profession |
| `employer` | string, nullable | Employeur |
| `emergency_contact_name` | string, nullable | Contact d'urgence |
| `emergency_contact_phone` | string, nullable | Telephone d'urgence |
| `guarantor_name` | string, nullable | Nom du garant |
| `guarantor_phone` | string, nullable | Telephone du garant |
| `guarantor_address` | text, nullable | Adresse du garant |
| `is_active` | boolean | Locataire actif |

**Accesseur** : `full_name` retourne `first_name last_name`.

**Suppression douce** activee.

### 4.5 Leases (Baux)

| Champ | Type | Description |
|---|---|---|
| `sci_id` | FK -> scis | SCI |
| `property_id` | FK -> properties | Bien loue |
| `tenant_id` | FK -> tenants | Locataire |
| `start_date` | date | Date de debut du bail |
| `end_date` | date, nullable | Date de fin |
| `duration_months` | integer, nullable | Duree en mois |
| `rent_amount` | decimal(12,2) | Loyer mensuel (FCFA) |
| `charges_amount` | decimal(12,2) | Charges mensuelles |
| `deposit_amount` | decimal(12,2) | Caution versee |
| `payment_method` | enum | `virement`, `especes`, `cheque`, `mobile_money`, `autre` |
| `due_day` | tinyint (1-28) | Jour d'echeance dans le mois |
| `penalty_rate` | decimal(5,2) | Taux de penalite (%) |
| `penalty_delay_days` | integer | Jours de grace avant penalite |
| `status` | enum | `actif`, `resilie`, `en_attente`, `expire` |
| `termination_date` | date, nullable | Date de resiliation |
| `termination_reason` | text, nullable | Motif de resiliation |
| `signed_lease_path` | string, nullable | Bail signe (upload PDF) |
| `entry_inspection_path` | string, nullable | Etat des lieux d'entree |
| `exit_inspection_path` | string, nullable | Etat des lieux de sortie |
| `notes` | text, nullable | Notes complementaires |

**Relations** : Appartient a SCI, Property, Tenant. Possede plusieurs LeasesMonthlies.

**Scopes** : `active()` (status=actif), `forProperty($id)`.

**Suppression douce** activee. Index sur `[sci_id, status]` et `[property_id, status]`.

### 4.6 LeasesMonthlies (Echeances mensuelles)

| Champ | Type | Description |
|---|---|---|
| `lease_id` | FK -> leases | Bail associe |
| `sci_id` | FK -> scis | SCI |
| `month` | varchar(7) | Mois au format YYYY-MM |
| `rent_due` | decimal(12,2) | Loyer du |
| `charges_due` | decimal(12,2) | Charges dues |
| `penalty_due` | decimal(12,2) | Penalite appliquee |
| `total_due` | decimal(12,2) | Total du (loyer + charges + penalite) |
| `paid_amount` | decimal(12,2) | Montant paye |
| `remaining_amount` | decimal(12,2) | Reste a payer |
| `status` | enum | `paye`, `partiel`, `impaye`, `en_retard` |
| `due_date` | date | Date d'echeance |

**Contrainte unique** : `[lease_id, month]` (un seul enregistrement par bail et par mois).

**Scopes** : `unpaid()` (impaye, partiel, en_retard), `overdue()` (en_retard).

### 4.7 Payments (Paiements)

| Champ | Type | Description |
|---|---|---|
| `lease_monthly_id` | FK -> lease_monthlies | Echeance concernee |
| `sci_id` | FK -> scis | SCI |
| `amount` | decimal(12,2) | Montant paye (FCFA) |
| `paid_at` | date | Date du paiement |
| `method` | enum | `virement`, `especes`, `cheque`, `mobile_money`, `autre` |
| `reference` | string, nullable | Reference du paiement |
| `note` | text, nullable | Note complementaire |
| `recorded_by` | FK -> users | Utilisateur ayant enregistre |

### 4.8 Documents

| Champ | Type | Description |
|---|---|---|
| `sci_id` | FK -> scis | SCI |
| `type` | string | Type de document (voir liste ci-dessous) |
| `related_type` | string, nullable | Type du modele lie (polymorphe) |
| `related_id` | bigint, nullable | ID du modele lie |
| `month` | varchar(7), nullable | Mois concerne |
| `path` | string | Chemin du fichier PDF |
| `meta` | JSON, nullable | Metadonnees supplementaires |
| `generated_by` | FK -> users | Utilisateur generateur |

**Types de documents** : `quittance`, `recu`, `attestation_location`, `avis_echeance`, `relance`, `releve_compte`, `recap_mensuel`, `attestation_reception_fonds`, `attestation_bail`, `attestation_sortie`.

### 4.9 Reminders (Relances)

| Champ | Type | Description |
|---|---|---|
| `sci_id` | FK -> scis | SCI |
| `lease_monthly_id` | FK -> lease_monthlies | Echeance concernee |
| `channel` | enum | `email`, `sms`, `whatsapp`, `courrier` |
| `message` | text | Contenu de la relance |
| `sent_at` | timestamp, nullable | Date/heure d'envoi |
| `status` | enum | `brouillon`, `envoye`, `echec` |
| `sent_by` | FK -> users | Utilisateur expediteur |

### 4.10 AuditLogs (Journal d'audit)

| Champ | Type | Description |
|---|---|---|
| `user_id` | FK -> users | Utilisateur ayant effectue l'action |
| `sci_id` | FK -> scis, nullable | SCI concernee |
| `action` | string | Type d'action (created, updated, deleted...) |
| `entity_type` | string, nullable | Classe du modele concerne |
| `entity_id` | bigint, nullable | ID de l'entite |
| `changes` | JSON, nullable | Donnees modifiees |
| `ip_address` | varchar(45) | Adresse IP |
| `user_agent` | text | Navigateur/client |

---

## 5. Systeme de roles et permissions

### Trois niveaux de roles

| Role | Description | Acces |
|---|---|---|
| **super_admin** | Administrateur global | Acces total. Gere les SCIs, les utilisateurs, voit toutes les donnees de toutes les SCIs. |
| **gestionnaire** | Gestionnaire de SCI | Cree/modifie/supprime les biens, locataires, baux, paiements. Genere les documents et relances. Limite aux SCIs qui lui sont affectees. |
| **lecture_seule** | Consultation uniquement | Consulte toutes les donnees (biens, locataires, baux, echeances, paiements, documents). Peut exporter. Ne peut rien creer, modifier ou supprimer. Limite aux SCIs affectees. |

### Mecanisme d'autorisation

L'autorisation fonctionne sur deux niveaux :

1. **Middleware de route** (`CheckRole`) : Filtre l'acces aux groupes de routes selon le role de l'utilisateur. Retourne une erreur 403 si le role n'est pas autorise.

2. **Policies Eloquent** : 9 policies enregistrees dans `AppServiceProvider`. Chaque policy definit les droits `viewAny`, `view`, `create`, `update`, `delete` par modele.

3. **Gate::before()** : Un hook global dans `AppServiceProvider` accorde automatiquement tous les droits aux `super_admin`, court-circuitant toutes les policies.

### Matrice des permissions

| Ressource | super_admin | gestionnaire | lecture_seule |
|---|---|---|---|
| SCIs (lecture) | Toutes | Affectees uniquement | Affectees uniquement |
| SCIs (ecriture) | Oui | Non | Non |
| Biens (lecture) | Tous | SCIs affectees | SCIs affectees |
| Biens (ecriture) | Oui | SCIs affectees | Non |
| Locataires | Idem Biens | Idem Biens | Lecture seule |
| Baux | Idem Biens | Idem Biens | Lecture seule |
| Echeances | Idem Biens | Idem Biens | Lecture seule |
| Paiements | Idem Biens | Idem Biens | Lecture seule |
| Documents | Idem Biens | Generation + lecture | Lecture seule |
| Relances | Idem Biens | Creation + envoi | Lecture seule |
| Utilisateurs | CRUD complet | Aucun acces | Aucun acces |
| Journal d'audit | Lecture | Lecture | Aucun acces |
| Exports | Oui | Oui | Oui |

---

## 6. Multi-tenancy SCI

### Principe

Chaque entite (bien, locataire, bail, echeance, paiement, document, relance) est rattachee a une SCI via une cle etrangere `sci_id`. Les utilisateurs sont relies aux SCIs par une table pivot `sci_user` (relation many-to-many).

### Middleware SetActiveSci

Ce middleware, applique a toutes les routes authentifiees, gere l'isolation des donnees :

1. **Determine les SCIs accessibles** : Un super_admin voit toutes les SCIs. Les autres roles ne voient que celles qui leur sont affectees.

2. **Lit la SCI active en session** : L'utilisateur peut basculer entre ses SCIs via le selecteur dans la sidebar.

3. **Valide l'acces** : Si la SCI en session n'est pas accessible, elle est reinitialise a la premiere SCI de l'utilisateur.

4. **Partage avec les vues** : Les variables `$activeSci` (SCI courante ou null pour "toutes") et `$userScis` (liste des SCIs de l'utilisateur) sont injectees dans toutes les vues Blade.

5. **Definit le scope** : `$request->attributes->set('sci_id', ...)` est lu par les controllers pour filtrer les donnees.

### Selecteur de SCI

Un selecteur en sidebar (Alpine.js) permet de :
- **Super admin** : Voir "Toutes les SCIs" (aucun filtre) ou selectionner une SCI specifique
- **Autres roles** : Basculer entre les SCIs affectees

Le changement se fait via une requete POST vers `/switch-sci` qui stocke le choix en session.

---

## 7. Modules fonctionnels

### 7.1 Tableau de bord

Le dashboard affiche des indicateurs cles calcules par le `ReportService` :

**KPIs globaux** :
- Total attendu (somme des total_due)
- Total encaisse (somme des paid_amount)
- Total impayes (somme des remaining_amount)
- Taux de recouvrement (%)

**KPIs du mois en cours** :
- Memes metriques filtrees sur le mois courant

**Statistiques du parc** :
- Nombre de biens total
- Biens occupes
- Biens vacants
- Baux actifs

**Alertes** :
- Liste des echeances en retard (impayees avec date depassee)

### 7.2 Gestion des SCIs

CRUD complet reserve au super_admin :
- Creation avec upload de logo (stockage public `logos/`)
- Informations legales : RCCM, IFU
- Coordonnees bancaires : banque, IBAN
- Page de detail avec compteurs (biens, locataires, baux actifs)

### 7.3 Gestion des biens immobiliers

CRUD complet (super_admin + gestionnaire) :
- 7 types de biens : appartement, maison, studio, bureau, commerce, terrain, autre
- 3 statuts : disponible, occupe, travaux
- Loyer et charges de reference
- Informations detaillees : surface, pieces, cles, climatiseurs
- Page de detail : bail actif en cours, historique complet des baux
- Filtres : statut, type, recherche (reference, adresse)

### 7.4 Gestion des locataires

CRUD complet (super_admin + gestionnaire) :
- Identite complete : nom, prenom, contacts, adresse
- Piece d'identite : type, numero, expiration, scan (upload PDF/image max 5 Mo)
- Informations professionnelles
- Contact d'urgence
- Garant : nom, telephone, adresse
- Page de detail : bail en cours, historique des baux, resume des paiements
- Filtres : recherche (nom, telephone, email)

### 7.5 Gestion des baux

CRUD complet + actions speciales (super_admin + gestionnaire) :

**Creation** :
- Selection du bien (uniquement les biens disponibles) et du locataire
- Dates de debut/fin, duree en mois
- Montants : loyer, charges, caution
- Mode de paiement : virement, especes, cheque, mobile money, autre
- Jour d'echeance (1 a 28)
- Penalites : taux (%) et delai de grace (jours)
- Statut initial : actif ou en attente
- Uploads : bail signe (PDF max 10 Mo), etat des lieux d'entree
- **Regle** : Un seul bail actif/en_attente par bien

**Activation** (pour les baux en attente) :
- Change le statut a `actif`
- Met le bien a `occupe`
- Genere les echeances mensuelles

**Resiliation** :
- Enregistre la date et le motif
- Upload optionnel de l'etat des lieux de sortie
- Remet le bien a `disponible`

**Detail** : echeances (paginee), paiements, documents lies

### 7.6 Echeances mensuelles

**Generation automatique** : A la creation/activation d'un bail, des enregistrements `LeaseMonthly` sont generes depuis la date de debut jusqu'a la date de fin (ou +12 mois). Chaque echeance contient le loyer du, les charges, le total, et la date d'echeance (calculee avec le `due_day` du bail, plafonne aux jours du mois).

**Generation manuelle** : Un bouton permet de lancer `generateAllPending()` pour generer les echeances de tous les baux actifs jusqu'au mois suivant.

**Statuts** :
- `impaye` : Aucun paiement recu
- `partiel` : Paiement partiel
- `paye` : Totalite payee
- `en_retard` : Impaye apres le delai de grace (penalite appliquee)

**Application des penalites** : Apres le delai de grace (`penalty_delay_days`), une penalite unique est calculee : `rent_amount * penalty_rate / 100`. Elle est ajoutee au `total_due` et au `remaining_amount`. Le statut passe a `en_retard`.

### 7.7 Paiements

**Enregistrement** (super_admin + gestionnaire) :
- Selection de l'echeance concernee
- Montant (ne peut pas depasser le `remaining_amount`)
- Date de paiement
- Methode : virement, especes, cheque, mobile money, autre
- Reference optionnelle et note

**Recalcul automatique** : A chaque paiement, le `PaymentService` recalcule :
- `paid_amount` = somme de tous les paiements de l'echeance
- `remaining_amount` = `total_due` - `paid_amount` (minimum 0)
- `status` : `paye` si remaining <= 0, `partiel` sinon

**Consultation** : Liste filtrable par mois, methode, recherche. Detail complet avec relations.

---

## 8. Generation de documents PDF

Le `DocumentService` utilise **barryvdh/laravel-dompdf** pour generer des PDF a partir de templates Blade.

### Types de documents

| Document | Description | Donnees requises |
|---|---|---|
| **Quittance de loyer** | Preuve de paiement du loyer mensuel | `lease_monthly_id` |
| **Recu de paiement** | Confirmation de reception d'un paiement | `payment_id` |
| **Avis d'echeance** | Notification de loyer a payer | `lease_monthly_id` |
| **Releve de compte** | Historique des echeances/paiements d'un locataire sur une periode | `lease_id`, `from_month`, `to_month` |
| **Recap mensuel** | Rapport mensuel complet d'une SCI (tous biens/baux) | `sci_id`, `month` |
| **Attestations** | 4 types : attestation de location, reception de fonds, bail, sortie | `type`, `lease_id` |

### Stockage

Les PDF sont stockes dans : `documents/{sciId}/{annee}/{mois}/{nom_fichier}.pdf`

Un enregistrement `Document` est cree en base avec le chemin, les metadonnees et le lien polymorphe vers l'entite source.

### Templates

7 templates Blade dans `resources/views/pdf/` :
- `quittance.blade.php`
- `payment_receipt.blade.php`
- `rent_notice.blade.php`
- `tenant_statement.blade.php`
- `monthly_report.blade.php`
- `attestation.blade.php`
- `reminder.blade.php`

---

## 9. Systeme d'exports

Le module d'export utilise **maatwebsite/excel** pour generer des fichiers XLSX et CSV.

### Exports disponibles

| Export | Colonnes | Filtres |
|---|---|---|
| **Locataires** | Nom, Prenom, Email, Telephone, SCI, Profession, Statut | SCI active |
| **Biens** | Reference, Type, Adresse, Ville, SCI, Statut, Loyer Ref., Charges Ref., Surface | SCI active |
| **Paiements** | Date, Montant, Methode, Reference, Locataire, Bien, Mois, Note | SCI active, plage de dates |
| **Impayes** | Mois, Locataire, Bien, Total Du, Paye, Reste, Statut, Echeance | SCI active |

Tous les exports sont accessibles a tous les utilisateurs authentifies (y compris lecture_seule).

Les montants sont formates avec le separateur de milliers (espace), conforme au format FCFA.

---

## 10. Systeme de relances

### Fonctionnement

Le `ReminderService` gere la creation et l'envoi des relances pour les echeances impayees.

**Canaux disponibles** : email, SMS, WhatsApp, courrier.

**Cycle de vie** :
1. `brouillon` : Relance creee mais non envoyee
2. `envoye` : Relance marquee comme envoyee
3. `echec` : Echec d'envoi

### Creation manuelle

Un gestionnaire peut creer une relance en selectionnant :
- L'echeance impayee
- Le canal de communication
- Le message personnalise

### Generation automatique

`autoGenerateReminders(daysOverdue)` :
1. Identifie les echeances dont la date d'echeance est depassee de plus de N jours (par defaut 5)
2. Filtre les statuts `impaye`, `partiel`, `en_retard` avec un `remaining_amount > 0`
3. Cree une relance par echeance et par mois calendaire (deduplication)
4. Genere automatiquement un message en francais contenant le nom du locataire, la reference du bien et le montant restant

### Envoi

L'envoi marque la relance comme `envoye` avec un timestamp. L'integration effective avec des services email/SMS/WhatsApp est preparee mais reste un placeholder a implementer.

---

## 11. Journal d'audit

### Principe

Chaque action significative est tracee par le `AuditService` :

- **Creation** d'un bien, locataire, bail, paiement, document, relance, SCI, utilisateur
- **Modification** de ces memes entites
- **Suppression**
- **Actions speciales** : activation/resiliation de bail, generation de documents

### Donnees enregistrees

- Utilisateur ayant effectue l'action
- SCI concernee
- Type d'action (`created`, `updated`, `deleted`, `terminated`, `activated`...)
- Type et ID de l'entite
- Donnees modifiees (JSON)
- Adresse IP
- User-Agent du navigateur

### Consultation

Interface de consultation avec filtres :
- SCI
- Utilisateur
- Type d'action
- Type d'entite
- Plage de dates

Paginee par 25 enregistrements. Accessible aux super_admin et gestionnaires.

---

## 12. Taches planifiees

Les taches sont definies dans `routes/console.php` et executees par le scheduler Laravel.

| Tache | Frequence | Description |
|---|---|---|
| **GenerateMonthliesJob** | 1er du mois a 01h00 | Genere les echeances mensuelles pour tous les baux actifs + applique les penalites de retard |
| **Mise a jour des retards** | Tous les jours a 00h30 | Passe les echeances `impaye` a `en_retard` quand la date d'echeance est depassee |
| **SendRemindersJob** | Tous les jours a 08h00 | Genere automatiquement les relances (echeances > 5 jours de retard) + envoie les relances en brouillon |
| **GenerateMonthlySciReportJob** | 1er du mois a 06h00 | Genere le PDF de recap mensuel pour chaque SCI active (mois precedent) |

Pour activer le scheduler, ajouter au crontab du serveur :
```
* * * * * cd /chemin/vers/SCIManager && php artisan schedule:run >> /dev/null 2>&1
```

---

## 13. Regles metier cles

1. **Un seul bail actif par bien** : La creation d'un bail verifie qu'aucun bail `actif` ou `en_attente` n'existe deja sur le bien. Sinon, une erreur est levee.

2. **Synchronisation statut bien/bail** :
   - Creation ou activation d'un bail -> bien passe a `occupe`
   - Resiliation d'un bail -> bien revient a `disponible`

3. **Generation des echeances** : Lors de la creation d'un bail `actif` (ou de son activation), les echeances sont generees automatiquement de la date de debut jusqu'a la date de fin (ou +12 mois). La date d'echeance est calculee avec le `due_day` du bail, plafonnee aux jours du mois (ex: due_day=31 en fevrier -> 28/29).

4. **Paiement plafonne** : Le montant d'un paiement ne peut pas depasser le `remaining_amount` de l'echeance concernee.

5. **Recalcul automatique** : Apres chaque paiement, le `paid_amount`, `remaining_amount` et `status` de l'echeance sont recalcules.

6. **Penalite unique** : La penalite n'est appliquee qu'une fois par echeance (quand `penalty_due = 0`), apres ecoulement du delai de grace. Formule : `rent_amount * penalty_rate / 100`.

7. **Deduplication des relances** : La generation automatique cree au maximum une relance par echeance et par mois calendaire.

8. **Impossibilite de se supprimer soi-meme** : Un super_admin ne peut pas supprimer son propre compte utilisateur.

9. **Suppression douce** : Les SCIs, biens, locataires et baux utilisent le `SoftDeletes` de Laravel. Les paiements, echeances, documents, relances et logs d'audit ne sont pas soft-deleted.

10. **Isolation des donnees** : Chaque entite porte un `sci_id`. Le middleware `SetActiveSci` et les scopes Eloquent (`scopeSci`, `scopeVisibleByUser`) garantissent que les utilisateurs ne voient que les donnees de leurs SCIs affectees.

---

## 14. Structure des routes

### Authentification (`routes/auth.php`)

Routes standard Laravel Breeze : inscription, connexion, mot de passe oublie, reinitialisation, verification email, confirmation mot de passe, deconnexion.

### Application (`routes/web.php`)

Toutes les routes applicatives sont protegees par les middlewares `auth`, `verified` et `SetActiveSci`.

**Tier 1 - Super admin uniquement** (`role:super_admin`) :
- CRUD SCIs (create/store/edit/update/destroy)
- CRUD Utilisateurs (resource complete)

**Tier 2 - Super admin + Gestionnaire** (`role:super_admin,gestionnaire`) :
- CRUD Biens, Locataires, Baux
- Activation/resiliation de baux
- Enregistrement de paiements
- Generation d'echeances
- Generation de documents (6 types)
- Gestion des relances
- Consultation du journal d'audit

**Tier 3 - Tous les utilisateurs authentifies** :
- Dashboard
- Consultation (index + show) : biens, locataires, baux, echeances, paiements, documents, relances, SCIs
- Telechargement de documents
- Exports (locataires, biens, paiements, impayes)

**Ordre des routes** : Les routes specifiques (`/create`, `/edit`) sont declarees avant les routes avec parametre (`/{id}`) pour eviter que le wildcard ne capture les mots-cles.

---

## 15. Composants Blade

18 composants reutilisables dans `resources/views/components/` :

| Composant | Description |
|---|---|
| `badge` | Badge colore pour les statuts (success, info, warning, danger, default) |
| `stat-card` | Carte de KPI pour le tableau de bord |
| `filters` | Barre de filtres avec formulaire GET |
| `empty-state` | Etat vide avec message et bouton d'action |
| `table` | Composant de tableau de donnees |
| `modal` | Dialogue modal (Alpine.js) |
| `dropdown` | Menu deroulant |
| `dropdown-link` | Lien dans un menu deroulant |
| `primary-button` | Bouton principal (indigo) |
| `secondary-button` | Bouton secondaire (bordure grise) |
| `danger-button` | Bouton dangereux (rouge) |
| `text-input` | Champ de saisie texte |
| `input-label` | Label de formulaire |
| `input-error` | Message d'erreur de validation |
| `nav-link` | Lien de navigation |
| `responsive-nav-link` | Lien de navigation responsive |
| `application-logo` | Logo de l'application |
| `auth-session-status` | Message de statut de session |

---

## 16. Schema relationnel

```
┌─────────────┐     M:N      ┌─────────────┐
│    Users     │─────────────>│    SCIs      │
│              │  (sci_user)  │              │
│ - name       │              │ - name       │
│ - email      │              │ - rccm       │
│ - role       │              │ - ifu        │
│ - is_active  │              │ - bank_iban  │
└──────┬───────┘              └──────┬───────┘
       │                             │
       │ 1:N                         │ 1:N (sur toutes les entites)
       │                             │
       ▼                     ┌───────┴──────────────────────┐
┌─────────────┐              │                              │
│ Audit Logs  │              ▼                              ▼
│             │      ┌─────────────┐              ┌─────────────┐
│ - action    │      │ Properties  │              │  Tenants    │
│ - entity    │      │             │              │             │
│ - changes   │      │ - reference │              │ - first_name│
│ - ip        │      │ - type      │              │ - last_name │
└─────────────┘      │ - status    │              │ - phone     │
                     │ - rent_ref  │              │ - email     │
                     └──────┬──────┘              └──────┬──────┘
                            │                            │
                            │ 1:N                   1:N  │
                            │                            │
                            ▼                            ▼
                     ┌──────────────────────────────────────┐
                     │              Leases                   │
                     │                                       │
                     │ - start_date    - rent_amount         │
                     │ - end_date      - charges_amount      │
                     │ - status        - deposit_amount      │
                     │ - due_day       - penalty_rate        │
                     └───────────────────┬──────────────────┘
                                         │
                                         │ 1:N
                                         ▼
                     ┌──────────────────────────────────────┐
                     │         Lease Monthlies               │
                     │                                       │
                     │ - month          - total_due          │
                     │ - rent_due       - paid_amount        │
                     │ - charges_due    - remaining_amount   │
                     │ - penalty_due    - status             │
                     │ - due_date                            │
                     └──────────┬──────────────┬────────────┘
                                │              │
                           1:N  │              │  1:N
                                ▼              ▼
                     ┌─────────────┐  ┌─────────────┐
                     │  Payments   │  │  Reminders  │
                     │             │  │             │
                     │ - amount    │  │ - channel   │
                     │ - paid_at   │  │ - message   │
                     │ - method    │  │ - status    │
                     │ - reference │  │ - sent_at   │
                     └─────────────┘  └─────────────┘

                     ┌─────────────────────────────────┐
                     │           Documents              │
                     │                                  │
                     │ - type         - path            │
                     │ - related      - meta            │
                     │   (polymorphe)                   │
                     └─────────────────────────────────┘
```

---

## Annexe : Commandes utiles

```bash
# Lancer le serveur de developpement
php artisan serve

# Compiler les assets (developpement)
npm run dev

# Compiler les assets (production)
npm run build

# Executer les migrations
php artisan migrate

# Executer les seeders
php artisan db:seed

# Lancer les tests
php artisan test

# Vider les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Lister les routes
php artisan route:list

# Executer le scheduler manuellement
php artisan schedule:run

# Generer les echeances manuellement
php artisan tinker
>>> App\Services\MonthlyGenerationService::generateAllPending()

# Appliquer les penalites manuellement
>>> App\Services\MonthlyGenerationService::applyPenalties()
```
