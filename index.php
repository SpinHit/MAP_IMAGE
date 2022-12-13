<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
  <meta charset="utf-8">
  <script src="exif.js"></script>
  <script src="jquery-3.6.1.min.js"></script>

  <title>Projet Visualisation</title>
  <style>
    @import url('main.css');
  </style>
</head>

<body>

  <!-- On créer un deuxième champ pour importer une image et l'insserrer dans une base de données a l'aide d'un bouton -->
  <form action="upload.php" method="post" enctype="multipart/form-data">
    <label for="image">Sélectionnez une image à télécharger :</label>
    <input type="file" name="image" id="image">
    <input type="submit" value="Télécharger l'image" name="submit">
</form>

  <?php 
  ini_set('memory_limit', '512M');
  // on importe upload.php pour pouvoir utiliser la base de données
  include 'upload.php';

  ?>

  <div id="map">

    <script>

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
          console.log(listgps);
          console.log(listdate);

          // on remplit locations avec les données de listgps
          var locations = [];
          for (var i = 0; i < listgps.length; i++) {
            // on met pas sous le format string les données de latitude et longitude
            
            var lat = listgps[i].gps_position_lat;
            var lng = listgps[i].gps_position_long;
            console.log(lat);
            console.log(lng);
            locations.push({ lat, lng });
          }
          console.log("locations :");
          console.log(locations);

          function initMap() {
            // on crée la carte google maps
            console.log(locations[0]);
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

            console.log("labelimage :");
            console.log(labelimage);
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
            console.log("markers :");
            console.log(markers);

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

    <div class="container">
      <div class="row">
        <div class="col-md-12">

          <div style="display:inline-block;width:100%;overflow-y:auto;">
            <p>Courbe du temps :</p>

            <ul class="timeline timeline-horizontal">

            </ul>
          </div>
        </div>
      </div>

    </div>
    <script>
      // on lance le script que quand la liste est remplie
      var interval2 = setInterval(function () {
        // on met une condition pour ne pas lancer le script si la liste est vide
        if (listdate.length > 0) {
          // on clear l'interval pour ne pas le relancer à chaque fois que la liste est remplie
          clearInterval(interval2);
          // on ajoute les images de la liste listdate à la timeline, pour chaque image on créer une carte avec les informations de l'image
          for (var i = 0; i < listdate.length; i++) {
            // https://codepen.io/ftrujilloh/pen/PpMYgJ/
            // on créer la timeline avec le code ci-dessus et on a modifié le code pour qu'il corresponde à nos besoins 
            var li = document.createElement('li');
            li.className = 'timeline-item';
            var div1 = document.createElement('div');
            div1.className = 'timeline-badge primary';
            var i1 = document.createElement('i');
            i1.className = 'glyphicon glyphicon-check';
            div1.appendChild(i1);
            var div2 = document.createElement('div');
            div2.className = 'timeline-panel';
            var div3 = document.createElement('div');
            div3.className = 'timeline-heading';
            //we add the name of the image as a title
            var h4 = document.createElement('h4');
            h4.className = 'timeline-title';
            h4.innerHTML = listdate[i].name;
            div3.appendChild(h4);
            // we add the date, the brand and the model of the camera and the size of the image and the location of the image as a subtitle
            var p = document.createElement('p');
            p.className = 'timeline-subtitle';
            p.innerHTML = 'Date : ' + listdate[i].created_at + ' <br> Marque : ' + listdate[i].brand + ' <br> Modèle : ' + listdate[i].camera_model + ' <br> Taille : ' + listdate[i].size + ' <br> Localisation : ' + listdate[i].gps_position_lat + ' ' + listdate[i].gps_position_long + ' <br> ' + 'Poid :  ' + listdate[i].weight;

            div3.appendChild(p);


            var div4 = document.createElement('div');
            div4.className = 'timeline-body';
            var image = document.createElement('img');
            // l'image est sous forme base64 pour pouvoir l'afficher dans la timeline il faut la convertir en url pour pouvoir l'afficher
            image.src = 'data:image/jpeg;base64,' + listdate[i].image;
            image.style.width = '100%';
            div4.appendChild(image);
            div2.appendChild(div3);
            div2.appendChild(div4);
            li.appendChild(div1);
            li.appendChild(div2);
            document.querySelector('#timeline ul').appendChild(li);
          }

          // au survol on agrandit la taille de l'image dans la timeline et on pousse les autres images vers la droite
          /*           var timeline = document.querySelectorAll('.timeline li');
                    for (var i = 0; i < timeline.length; i++) {
                      timeline[i].addEventListener('mouseover', function () {
                        this.style.width = '200px';
                        this.style.marginRight = '20px';
                        this.style.zIndex = '1000';
                        this.style.transition = 'all 0.5s';
                        this.style.transform = 'scale(1.3)';
                        this.style.transformOrigin = 'right';
                      });
                      timeline[i].addEventListener('mouseout', function () {
                        this.style.width = '100px';
                        this.style.marginRight = '0';
                        this.style.zIndex = '0';
                        this.style.transition = 'all 0.5s';
                        this.style.transform = 'scale(1)';
                        this.style.transformOrigin = 'right';
                      });
                    } */





        }

      }, 1000);





    </script>





</body>



</body>


</html>