# Nom du projet : Ecoride

> Plateforme de covoiturage écologique avec géolocalisation et espace utilisateur.

---

## Table des matières

- [Description](#description)
- [Fonctionnalités](#fonctionnalités)
- [Technologies utilisées](#technologies-utilisées)
- [Installation](#installation)

---

## Description

Ecoride est une plateforme web permettant à des conducteurs et passagers de partager des trajets écologiques entre villes.  
L’application propose :
- Recherche précise de trajets grâce à PostgreSQL + PostGIS  
- Saisie semi-automatique des villes  
- Scan antivirus des photos via ClamAV  
- Espace utilisateur (profil, réservations, messages)  
- Interface responsive (desktop & mobile)

---

## Fonctionnalités

### Recherche de trajets
- Saisie semi-automatique des villes (sans accents)  
- Filtrage par date, prix, nombre de places restantes…  
- Affichage dynamique de la carte

### Gestion des comptes
- Inscription et authentification sécurisée (mots de passe hashés)  
- Profil utilisateur avec photo scannée par ClamAV  
- Réservation / annulation de place(s) avec nombre de places réalloué automatiquement  

### Messagerie interne & notifications
- Envoi de messages privés entre utilisateurs  
- Notifications instantanées  
- Historique des messages stocké dans MongoDB  

### Administration
- Dashboard admin pour consulter les logs d’erreur et activités  
- Modération des photos (scan antivirus)  
- Statistiques d’utilisation (nombre de trajets, réservations, etc.)

### Sécurité & performances
- Scan antivirus ClamAV avant toute sauvegarde de photo  
- Requêtes optimisées pour PostgreSQL + PostGIS  
- Logs d’erreurs asynchrones stockés dans MongoDB pour ne pas bloquer l’UX

---

## Technologies utilisées

- **Frontend** : HTML5, CSS3, JavaScript (vanilla)  
- **Backend** : PHP 8.x, PDO  
- **Bases de données** :  
  - PostgreSQL (+ PostGIS) pour les trajets géospatiaux  
  - MongoDB pour les logs et la messagerie  
- **Sécurité** : ClamAV intégré au backend PHP, PDO  
- **Outils** : VS Code, Notion (Kanban), Docker  

---

## Installation

### Prérequis

- PHP ≥ 8.0  
- Composer  
- PostgreSQL ≥ 13 (avec PostGIS)  
- MongoDB ≥ 4.x  
- ClamAV  
- Chart.js  
- Apache ou Nginx  
- Git (pour cloner le dépôt)  
- Docker & Docker Compose  

### Étapes

> Toute la configuration (PHP, Composer, ClamAV, extensions PostGIS, etc.) est gérée automatiquement par Docker.  
> Vous devez simplement installer PostgreSQL (+ PostGIS) et MongoDB, créer les bases et importer le schéma, puis lancer Docker pour construire et démarrer le conteneur.

1. **Vérifier Docker**  
   ```bash
   docker --version
