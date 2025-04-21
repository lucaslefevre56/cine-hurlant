# 🎬 Ciné-Hurlant

**Projet de fin de formation développeur web full stack (GRETA - Kercode)**  
Site de découverte et d'analyse des influences croisées entre la bande dessinée française et le cinéma de science-fiction.

## 🌐 Démo en ligne

[https://stagiaires-kercode9.greta-bretagne-sud.org/lucas-lefevre/cine_hurlant/](https://stagiaires-kercode9.greta-bretagne-sud.org/lucas-lefevre/cine_hurlant/)

---

## 📁 Structure du projet

- `app/` : MVC maison (models, views, controllers, core, helpers)
- `public/` : fichiers accessibles depuis le web (CSS, sass, fonts, JS, images, upload, api)
- `config/` : configuration de l’environnement
- `vendor/` : dépendances Composer (Dotenv)
- `docs-soutenance/` : pdf de présentation du candidat et du projet
- `.env` : variables d’environnement (non versionné)
- `.htaccess` : gestion des routes, réécriture d’URL et redirection HTTPS
- `index.php` : point d’entrée unique du site (Front Controller)
- `.gitignore` : exclusions des fichiers sensibles et non pertinents (vendor, .env, node_modules...)
- `cine_hurlant.sql` : script d’import de la base de données (présente du contenu de démonstration pour la soutenance)
- `README.md` : ce fichier !

---

## 🔧 Technologies

- HTML / SCSS / JS
- PHP 8 avec MVC personnalisé
- MySQL (phpMyAdmin)
- AJAX via `fetch()` pour les commentaires, les panels admin/rédacteur, recherche dynamique
- Composer + `vlucas/phpdotenv` pour la gestion sécurisée des variables d’environnement

---

## 🚀 Déploiement (o2switch + FileZilla)

### 📦 Étapes pour mettre en ligne :

1. **Transférer les fichiers**  
   → Envoyer tout le projet via FileZilla dans `/lucas-lefevre/cine_hurlant/`

2. **Adapter les chemins de production**  
   Modifier **3 fichiers** pour pointer vers le bon sous-dossier :

   - **`.env`**
     ```env
     BASE_URL=https://stagiaires-kercode9.greta-bretagne-sud.org/lucas-lefevre/cine_hurlant/
     DB_HOST=localhost
     DB_NAME=tima6358_lucas-lefevre-projet
     DB_USER=tima6358_lucas-lefevre
     DB_PASS=LefevreLU24!
     ```

   - **`app/Core/Config.php`**
     ```php
     return $protocol . '://' . $_SERVER['HTTP_HOST'] . '/lucas-lefevre/cine_hurlant';
     ```

   - **`.htaccess`**
     ```apache
     RewriteBase /lucas-lefevre/cine_hurlant/
     ```

3. **Importer la base de données**  
   → Importer le fichier `.sql` dans phpMyAdmin avec le bon nom de base (`tima6358_lucas-lefevre-projet`)

4. **Vérifier les droits des dossiers**  
   → Le dossier `public/upload/` doit être accessible en écriture si vous utilisez l'upload d'image.

---

## 🧪 Testé avec

- Firefox, Chrome (desktop et mobile)
- PHP 8.2 / MySQL 5.7 (en local et sur o2switch)
- Responsive et compatible mobile-first

---

## 📄 Licence

Projet réalisé dans le cadre d’une formation. Tous les contenus sont fictifs ou cités dans un but pédagogique.
