# API CRUD Démo Students

Ce projet est une API REST en PHP basée sur un modèle MVC simple, conçue pour une démonstration pédagogique.
L’objectif est de montrer aux étudiants comment construire une API sécurisée avec PDO, JWT et des routes CRUD.

## Structure du projet

- `index.php` : routeur principal
- `config/config.php` : configuration MySQL et JWT
- `lib/Database.php` : connexion PDO singleton
- `lib/JwtHandler.php` : génération / validation de tokens JWT
- `models/Student.php` : modèle étudiant (CRUD)
- `controllers/AuthController.php` : authentification et génération de token
- `controllers/StudentController.php` : gestion des routes étudiant
- `.htaccess` : redirection Apache vers `index.php`

## Installation

1. Place le dossier `api_crud` dans ton répertoire WAMP, par exemple `C:\wamp64\www\api_crud`.
2. Assure-toi que `mod_rewrite` est activé et que Apache autorise les `.htaccess`.
3. Crée la base de données MySQL `api_demo`.
4. Exécute la requête SQL suivante :

```sql
CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL
);
```

5. Ouvre `config/config.php` et mets à jour les paramètres de connexion si nécessaire.

## Authentification

L’API utilise JWT. Le secret se trouve dans `config/config.php` :

```php
'secret' => 'v2e8s3Zp9QmR4xYcL7wN1uD5FjH0kB2t'
```

### Connexion de démonstration

L’utilisateur de démonstration est codé en dur dans `AuthController.php` :

- username : `admin`
- password : `password123`

## Routes

### 1) Connexion

`POST http://localhost/api_crud/login`

Body JSON :

```json
{
  "username": "admin",
  "password": "password123"
}
```

Réponse :

```json
{
  "token": "<JWT token>"
}
```

### 2) Étudiants

Toutes les routes `/students` nécessitent le header HTTP :

```
Authorization: Bearer <token>
```

- `GET http://localhost/api_crud/students`
  - Liste tous les étudiants
- `GET http://localhost/api_crud/students/{id}`
  - Récupère un étudiant par ID
- `POST http://localhost/api_crud/students`
  - Crée un étudiant
  - Body JSON :

```json
{
  "first_name": "Jean",
  "last_name": "Dupont",
  "email": "jean.dupont@example.com"
}
```
- `PUT http://localhost/api_crud/students/{id}`
  - Met à jour un étudiant
- `DELETE http://localhost/api_crud/students/{id}`
  - Supprime un étudiant

## Test avec Postman

1. Appelle `POST /api_crud/login` avec les identifiants.
2. Récupère le token JWT de la réponse.
3. Ajoute le header `Authorization: Bearer <token>` sur les requêtes vers `/students`.

## Notes pédagogiques

- `PDO` est utilisé pour sécuriser les requêtes SQL.
- Les routes sont gérées dans `index.php`.
- Le contrôleur `StudentController` orchestre le modèle `Student`.
- Le token JWT est généré uniquement après authentification.
- C’est un exemple simple : pour une application réelle, il faut stocker les utilisateurs en base et hacher les mots de passe.

