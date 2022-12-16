<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
  <meta charset="utf-8">
  <script src="exif.js"></script>
  <script src="jquery-3.6.1.min.js"></script>
  <!-- on importe vis.js d'internet -->
  <script type="text/javascript" src="https://unpkg.com/vis-timeline@latest/standalone/umd/vis-timeline-graph2d.min.js"></script>
  <link href="https://unpkg.com/vis-timeline@latest/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />
  
  <!-- on importe bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <title>Projet Visualisation</title>
  <style>
    @import url('main.css');
  </style>
</head>

<body>

<!-- on créer un header navbar avec le logo au centre et un background de couleur #e4f4fe -->
<div class="headernav">
  <img src="logo.png" alt="logo" class="logo">
</div>


  <!-- On créer un deuxième champ pour importer une image et l'insserrer dans une base de données a l'aide d'un bouton -->
  <!-- bootsrap style formulaire d'upload -->
  <form action="upload.php" method="post" enctype="multipart/form-data" id="formupload" class="formupload">
    <label for="image"><h3>Sélectionnez une image à télécharger</h3></label>
    <input type="file" name="image[]" id="fileUpload" multiple class="btn btn-primary text-center ">
    <input type="submit" value="Télécharger l'image" name="submit" id="submitupload" >
  </form>
  
  <?php 
  ini_set('memory_limit', '512M');
  // on importe upload.php pour pouvoir utiliser la base de données
  include 'upload.php';

  ?>
<div id="buttonslidecenter">
  <button onclick="showMap()">Carte</button>
  <button onclick="showTimeline()">Frise chronologique</button>
</div>


  <div id="map">

    <script>

function showMap() {
  var x = document.getElementById("map");
  var y = document.getElementById("timeline");
  if (x.style.display === "none") {
    x.style.display = "block";
    y.style.display = "none";
  } else {
  }
}

function showTimeline() {
  var x = document.getElementById("map");
  var y = document.getElementById("timeline");
  if (x.style.display === "none") {
  } else {
    x.style.display = "none";
    y.style.display = "block";
  }
}

      //fonction pour importer un src comme un async defer pour les scripts js 
      function loadScript(src) {
        return new Promise(function (resolve, reject) {
          var script = document.createElement('script');
          script.src = src;
          script.onload = resolve;
          script.onerror = reject;
          document.head.appendChild(script);
        });
      }

      // dans list on va lister toutes les images récupérées
      var list = [];
      // listGps va contenir les images qui possèdent des coordonnées gps
      var listgps = [];
      // listdate va contenir les images qui possèdent des dates de création
      var listdate = [];


      var files = document.getElementById('files');

     
      <?php

      // on va afficher les images de la base de données
      $pdo = new PDO("mysql:host=$localhost;dbname=$dbname", $dbusername, $dbpassword);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sql = "SELECT * FROM images";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

       // on va dans la base de données et on récupère les images sous format blob et les métadonnées ( name, camera_model, brand, weight, created_at, gps_position_lat,gps_position_long, size )

       // on remplie la liste list avec les images récupérées de la base de données et on les affiche dans la console
      foreach ($images as $image) {
        $name = $image['name'];
        $camera_model = $image['camera_model'];
        $brand = $image['brand'];
        // le poid est un chiffre en octets, on le veut en Mo alors on le divise par 1000000 et on laissera 2 chiffres après la virgule
        $weight =  number_format($image['weight'] / 1000000, 2) . " Mo";
        $created_at = $image['created_at'];
        $gps_position_lat = $image['gps_position_lat'];
        $gps_position_long = $image['gps_position_long'];
        $size = $image['size'];
        $image = $image['image'];
        $image = base64_encode($image);
        echo "list.push({name: '$name', camera_model: '$camera_model', brand: '$brand', weight: '$weight', created_at: '$created_at', gps_position_lat: $gps_position_lat, gps_position_long: $gps_position_long, size: '$size', image: '$image'});";
      }

    


      ?>

      // on va parcourir la liste des images et on va récupérer les images qui possèdent des coordonnées gps
      for (var i = 0; i < list.length; i++) {
        // on vérifie que la latitude et la longitude ne sont pas nulles et que la latitude et la longitude ne sont pas égales à 0
        if (list[i].gps_position_lat != null && list[i].gps_position_long != null && list[i].gps_position_lat != 0 && list[i].gps_position_long != 0) {
          listgps.push(list[i]);
        }
      }

      // on va parcourir la liste des images et on va récupérer les images qui possèdent des dates de création
      for (var i = 0; i < list.length; i++) {
        // on vérifie que la date n'est pas nulle et que la date n'est pas égale à 0000-00-00 00:00:00
        if (list[i].created_at != null && list[i].created_at != "0000-00-00 00:00:00") {
          listdate.push(list[i]);
        }
      }


      


      // 
      // on met en pause le script et on le reprend quand la liste est remplie
      var interval = setInterval(function () {
        if (listgps.length > 0) {
          // on clear l'interval pour ne pas le relancer à chaque fois que la liste est remplie  
          clearInterval(interval);

          // on lances les scripts google maps et markerclusterer
          loadScript("https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js").then(function () {
            loadScript("https://maps.googleapis.com/maps/api/js?key=AIzaSyBtjaD-saUZQ47PbxigOg25cvuO6_SuX3M&callback=initMap").then(function () {
              // on lance la fonction initMap définie plus bas qui va afficher les marqueurs sur la carte google maps
              initMap();
            });
          });

          // on affiche dans la console la liste des images avec les données récupérées
/*           console.log(listgps);
          console.log(listdate); */

          // on remplit locations avec les données de listgps
          var locations = [];
          for (var i = 0; i < listgps.length; i++) {
            // on met pas sous le format string les données de latitude et longitude
            
            var lat = listgps[i].gps_position_lat;
            var lng = listgps[i].gps_position_long;
/*             console.log(lat);
            console.log(lng); */
            locations.push({ lat, lng });
          }
/*           console.log("locations :");
          console.log(locations); */

          function initMap() {
            // on crée la carte google maps
            /* console.log(locations[0]); */
            var map = new google.maps.Map(document.getElementById('map'), {
              zoom: 11,
              // on met le focus de chargement de la carte sur la première image
              
              center: locations[0]
            });

            // on importe les images dans markers
            var labelimage = [];
            for (var i = 0; i < listgps.length; i++) {
              // l'image est sous format base64
              var base64 = listgps[i].image;
              // on met le chemin de l'image dans labelimage
              labelimage.push('data:image/jpeg;base64,' + base64);
            }

  /*           console.log("labelimage :");
            console.log(labelimage); */
            // on crée les marqueurs sur la carte google maps
            var markers = locations.map(function (location, i) {
              return new google.maps.Marker({
                // on met les coordonnées de l'image
                position: location,
                // on met l'image en tant que marqueur sur la carte google maps
                icon: {
                  url: labelimage[i],
                  // on met la taille de l'image
                  scaledSize: new google.maps.Size(100, 100),
                  // on met le nom de l'image
                  name: listgps[i].name,
                  // on met le model de l'appareil photo
                  model: listgps[i].camera_model,
                  // on met la marque de l'appareil photo
                  brand: listgps[i].brand,
                  // on met la taille de l'image
                  taille: listgps[i].weight,
                  // on met la date de création de l'image
                  date: listgps[i].created_at,
                  // on met la localisation de l'image
                  Localisation: listgps[i].gps_position_lat + ' ' + listgps[i].gps_position_long

                },
                title: 'markerpins'
              });
            });
   /*          console.log("markers :");
            console.log(markers); */

             // on fait en sorte que quand on clique sur les marqueurs cela affiche les informations de l'image dans une infobulle
            markers.forEach(function (marker) {
              // on crée l'infobulle avec le listener click sur le marqueur
              marker.addListener('click', function () {
                // on crée l'infobulle avec les informations de l'image
                var infowindow = new google.maps.InfoWindow({
                  content: '<img src="' + marker.icon.url + '" style="width: 100%; height: 100%;">' + '<h4 class="timeline-title">' + marker.icon.name + '</h4>' + '<p>' + marker.icon.model + '</p>' + '<p>' + marker.icon.brand + '</p>' + '<p>' + marker.icon.taille + '</p>' + '<p>' + marker.icon.date + '</p>' + '<p>' + marker.icon.Localisation + '</p>'
                });
                // on affiche l'infobulle
                infowindow.open(map, marker);
              });
            }); 

            // on crée le cluster de marqueurs sur la carte google maps avec le script markerclusterer de google
            var markerCluster = new MarkerClusterer(map, markers,
              { imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m' });
          }
        }
      }, 1000);
    </script>
  </div>

  <div id="timeline">
    <!-- on crée une timeline avec les informations des images -->




          <div style="display:inline-block;width:100%;overflow-y:auto;">

            <div id="visualization"></div>
          </div>
    
    <script>
      // on lance le script que quand la liste est remplie
      var interval2 = setInterval(function () {
  /*       console.log("listdate :");
        console.log(listdate); */
        
        // on met une condition pour ne pas lancer le script si la liste est vide
        if (listdate.length > 0) {
          // on clear l'interval pour ne pas le relancer à chaque fois que la liste est remplie
          clearInterval(interval2);
          // on crée la timeline en en utilisant la librarie vis.js
          var container = document.getElementById("visualization");

// note that months are zero-based in the JavaScript Date object
var items = new vis.DataSet([]);

for (var i = 0; i < listdate.length; i++) {
  var item = listdate[i];
    // created_at est sous le format "YYYY-MM-DD HH:MM:SS" on le transforme en format date pour la timeline 
    // on récupere chaque info en faisant un split sur les espaces
    var date = item.created_at.split(" ");
    // on récupere la date
    var date1 = date[0];
    // on récupere l'heure
    var heure = date[1];
    /* console.log("heure :"); */
    // on récupere l'année
    var annee = date1.split("-")[0];
    // on récupere le mois
    var mois = date1.split("-")[1];
    // on récupere le jour
    var jour = date1.split("-")[2];
    // on récupere l'heure de la date
    var heure1 = heure.split(":")[0];
    // on récupere les minutes de la date
    var minute = heure.split(":")[1];
    // on récupere les secondes de la date
    var seconde = heure.split(":")[2];


    // on crée la date pour la timeline
    var date2 = new Date(annee, mois, jour, heure1, minute, seconde);


  items.add({

    start: date2,
    content:
      '<div>' + item.name + '</div><img style="width: 500px; height: 20px;" src="data:image/jpeg;base64,' + item.image + '">'+ '<div>' + item.model + '</div>' + '<div>' + item.brand + '</div>' + '<div>' + item.weight + '</div>' + '<div>' + item.created_at + '</div>' + '<div>' + item.gps_position_lat + ' ' + item.gps_position_long + '</div>',
  });
}



 // pour définir une taille maximale aux items de la timeline il faut utiliser le margin de la timeline


 
var options = {
  editable: false,
  margin: {
    item: listdate.length,
    axis: 5
  },
  zoomMin: 1000 * 60 * 60 * 24 * 31 * 3,
  zoomMax: 1000 * 60 * 60 * 24 * 31 * 12 * 10*3,
  height: '65vh',
  start: '2000-01-01', // début de la période de temps à afficher
  end: '2030-01-01', // fin de la période de temps à afficher

  
  
  
};

var timeline = new vis.Timeline(container, items, options);
          





        }

      }, 1000);





    </script>
 
  </div>
      <!--  footer sticky bootstap -->
  <footer class="footer">
      <h6> Visualise 2023</h6>  
    </footer>












</body>


</html>