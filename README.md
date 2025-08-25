# DEAG - Élevage Pro Gabon

Site web officiel de DEAG (Dépôt d'Entreprise d'Agriculture du Gabon), dédié à la promotion et au développement de l'élevage professionnel au Gabon.

## 📋 Description

Cette application web permet de :
- Gérer un catalogue de produits agricoles
- Administrer le contenu du site
- Mettre en avant les actualités du secteur
- Faciliter la prise de contact avec les clients

## 🚀 Fonctionnalités Principales

### Côté Public
- **Catalogue des Produits**
  - Affichage des produits par catégorie
  - Filtrage avancé (type d'animal, catégorie, prix)
  - Fiches produits détaillées
  - Gestion des promotions et nouveautés
  
- **Actualités**
  - Articles récents
  - Catégorisation
  - Partage sur les réseaux sociaux

- **Pages Informatives**
  - À propos
  - Contact avec formulaire
  - Conditions générales

### Administration
- **Gestion des Produits**
  - Ajout/Modification/Suppression
  - Gestion des stocks
  - Mise en avant des produits
  
- **Gestion du Contenu**
  - Actualités
  - Pages d'information
  - Images et médias

## 🛠️ Technologies Utilisées

### Frontend
- HTML5, CSS3, JavaScript (ES6+)
- Framework CSS personnalisé
- Font Awesome 6.0+
- Google Fonts (Raleway, Playfair Display)

### Backend
- PHP 8.0+
- MySQL/MariaDB
- PDO pour la gestion des bases de données
- Architecture MVC (Modèle-Vue-Contrôleur)

### Outils de Développement
- Git pour le contrôle de version
- Composer (pour les dépendances PHP)
- VS Code avec extensions recommandées

## 📦 Installation

### Prérequis
- Serveur web (Apache/Nginx)
- PHP 8.0 ou supérieur
- MySQL 5.7+ ou MariaDB 10.3+
- Composer (pour les dépendances PHP)

### Étapes d'Installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/votre-username/elevage-pro-gabon-2.git
   cd elevage-pro-gabon-2
   ```

2. **Configurer la base de données**
   - Créer une base de données MySQL
   - Importer la structure depuis `database/structure.sql`
   - Configurer les accès dans `config.php`

3. **Installer les dépendances**
   ```bash
   composer install
   ```

4. **Configurer l'environnement**
   - Copier `.env.example` vers `.env`
   - Modifier les variables selon votre configuration

5. **Droits d'accès**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 img/
   ```

6. **Accès administration**
   - URL : `/admin.php`
   - Identifiants par défaut : admin / motdepasse (à changer après la première connexion)

## 📁 Structure des Fichiers

```
/
├── admin/               # Panneau d'administration
├── css/                 # Feuilles de style
│   ├── components/      # Composants réutilisables
│   └── pages/           # Styles spécifiques aux pages
├── img/                 # Images du site
│   ├── products/        # Images des produits
│   └── news/            # Images des actualités
├── includes/            # Fichiers d'inclusion PHP
├── js/                  # Scripts JavaScript
├── uploads/             # Fichiers téléchargés
├── config.php           # Configuration de la base de données
├── .htaccess           # Configuration Apache
└── README.md           # Ce fichier
```

## 🔒 Sécurité

- Protection contre les injections SQL avec PDO
- Validation des entrées utilisateur
- Protection CSRF sur les formulaires
- Gestion des sessions sécurisées
- Mots de passe hashés avec bcrypt

## 📱 Design Responsive

Le site s'adapte à tous les appareils :
- Mobile-first approach
- Grille flexible
- Images responsives
- Navigation optimisée pour mobile

## 🐛 Dépannage

### Problèmes courants
1. **Images non chargées**
   - Vérifier les permissions du dossier `img/`
   - S'assurer que les chemins dans la base de données sont corrects

2. **Erreurs de base de données**
   - Vérifier les identifiants dans `config.php`
   - S'assurer que la base de données est importée

3. **Problèmes de session**
   - Vérifier les permissions du dossier de session PHP
   - S'assurer que les cookies sont activés

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :
1. Forkez le projet
2. Créez une branche pour votre fonctionnalité
3. Committez vos changements
4. Poussez vers la branche
5. Ouvrez une Pull Request

## 📞 Contact

Pour toute question ou support :
- 📧 Email : support@elevage-pro-gabon.com
- 📱 Téléphone : +241 74 00 00 00
- 🌍 Site : www.elevage-pro-gabon.com

## 📄 Licence

© 2025 DEAG. Tous droits réservés.

---
*Documentation générée le 24/07/2025*