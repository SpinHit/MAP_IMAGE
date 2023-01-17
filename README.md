# MAP_IMAGE

Ce projet consiste en une application web permettant de visualiser des images à travers une carte Google Maps et une timeline interactive. Il utilise les technologies PHP, HTML, CSS, JavaScript, l'API Google Maps, la librairie vis.js et une base de données MySQL pour stocker les images et les métadonnées.

Pour utiliser l'application, il est nécessaire de disposer d'un compte sur Google Cloud Platform et d'obtenir une clé d'API valide pour utiliser l'API Google Maps. Il est également nécessaire d'avoir un serveur web (comme Apache) et PHP installé, ainsi qu'un accès à une base de données MySQL.

Le projet comprend plusieurs fichiers :

Un fichier "index.php" contenant le code HTML, CSS et JavaScript pour l'interface utilisateur
Un fichier "upload.php" contenant le code PHP pour l'upload des images et la gestion des métadonnées
Un fichier "config.php" contenant les informations de connexion à la base de données
Un fichier "gps2Num.php" contenant le code PHP pour convertir les données GPS en un format lisible
Un dossier "js" contenant les fichiers JavaScript nécessaires à l'application
Un dossier "css" contenant les feuilles de style CSS pour l'interface utilisateur
Un dossier "img" contenant les images utilisées dans l'interface utilisateur
Pour utiliser l'application, il est nécessaire de configurer les informations de connexion à la base de données dans le fichier "config.php" et de télécharger les images via le formulaire d'upload. Les images téléchargées seront affichées sur une carte Google Maps lorsque les métadonnées de localisation sont disponibles, ou via la timeline lorsque ces métadonnées ne sont pas disponibles. Il est également possible de basculer entre l'affichage sur la carte et sur la timeline en utilisant les boutons sur l'interface utilisateur.
