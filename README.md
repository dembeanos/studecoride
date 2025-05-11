# Nom du projet : Ecoride

> Plateforme de covoiturage écologique avec géolocalisation et espace utilisateur.

---

## Table des matières

- [Description] 
- [Fonctionnalités]
- [Technologies utilisées]
- [Installation] 
- [Configuration] 
- [Exemples d’utilisation] 
- [Contribution] 
- [Licence]

---

## Description

Ecoride est une plateforme web permettant à des conducteurs et passagers de partager des trajets écologiques entre villes.  
L’application propose :
- Recherche précise de trajets grâce à PostgreSQL + PostGIS  
- Saisie semi-automatique des villes  
- Scan antivirus des photos via ClamAV  
- Espace utilisateur (profil, réservations, messages)  
- Interface responsive (desktop & mobile)

---

## Fonctionnalités

- Recherche de trajets par ville et date, avec filtres (passager/​conducteur)  
- Inscription, connexion et gestion de compte  
- Suggestions de villes basées sur PostGIS  
- Ajout, modification, suppression de trajets  
- Scan ClamAV des fichiers uploadés (photos de profil)  
- Messagerie interne et historique des logs utilisateur  
- Dashboard admin (statistiques, modération)  
- Responsive design pour tous les écrans  

---

## Technologies utilisées

- **Frontend** : HTML5, CSS3, JavaScript (vanilla)  
- **Backend** : PHP 8.x, PDO  
- **Bases de données** :  
  - PostgreSQL (+ PostGIS) pour les trajets géospatiaux  
  - MongoDB pour les logs et la messagerie  
- **Sécurité** : ClamAV intégré au backend PHP  
- **Outils** : VS Code, Notion (Kanban)  

---

## Installation

### Prérequis

- PHP ≥ 8.0  
- Composer  
- PostgreSQL ≥ 13 (avec PostGIS)  
- MongoDB ≥ 4.x  
- ClamAV  
- Apache ou Nginx  
- Git (pour cloner le dépôt)

### Étapes

1. **Cloner le dépôt**  
   ```bash
   git clone https://github.com/tonNomUtilisateur/ecoride.git
   cd ecoride
