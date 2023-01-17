# Visualisation d'images à travers une carte Google et une timeline interactive

Ce projet a pour objectif de créer une application web permettant de visualiser des images à travers une carte Google et une timeline interactive. Les images et leurs métadonnées sont stockées dans une base de données. Il permet de visualiser les images en fonction de leur emplacement géographique lorsque les métadonnées de localisation sont disponibles, ou en utilisant la timeline lorsque ces métadonnées ne sont pas disponibles.

## Technologies utilisées
- PHP
- HTML
- CSS
- JavaScript
- API Google Maps
- Librairie vis.js
- Base de données MYSQL
- Librairie exif

## Fonctionnalités
- Upload d'images
- Visualisation des images sur une carte Google en utilisant les métadonnées de localisation
- Visualisation des images sur une timeline interactive en utilisant les métadonnées de date de prise de vue
- Stockage des images et des métadonnées dans une base de données
- Conversion des données GPS en un format lisible pour le stockage

## Utilisation
1. Téléchargez ou clonez ce dépôt sur votre ordinateur
2. Installez les dépendances en utilisant la commande `composer install`
3. Créez une base de données MySQL et importez le fichier `database.sql` pour créer les tables nécessaires
4. Modifiez les informations de connexion à la base de données dans le fichier `config.php`
5. Obtenez une clé API valide pour l'API Google Maps et modifiez l'URL de l'API dans le fichier `index.php`
6. Lancez un serveur PHP local en utilisant la commande `php -S localhost:8000`
7. Accédez à l'application en accédant à l'adresse `http://localhost:8000` sur votre navigateur web
8. Utilisez le formulaire d'upload pour télécharger des images et visualisez-les sur la carte ou la timeline interactive

## Remarques
- Assurez-vous que le fichier `.htaccess` est présent pour éviter les erreurs de routing
- Assurez-vous que vous avez activé l'extension exif sur votre serveur PHP

lien vidéo : https://www.youtube.com/watch?v=WiuJ5Oy2mp0
