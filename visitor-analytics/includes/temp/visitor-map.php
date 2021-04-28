<?php

$lati = $_GET['lati'];
$longi = $_GET['longi'];

 ?>

 <!DOCTYPE html>
 <html>
      <head>
           <meta charset="utf-8">
           <title>Location Map</title>
           <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
     		<script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js" integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA==" crossorigin=""></script>
      </head>
      <body>
            <div id="visitor-map" style="height: 455px;"></div>
      </body>
 </html>
<script>
var y = document.getElementById("visitor-map");
 var lati = <?php echo $lati; ?>;
 var longi = <?php echo $longi; ?>;

  myMap1(lati, longi);

  function myMap1(lati, longi){
       var mymap1 = L.map('visitor-map').setView([lati, longi], 10);
       var circle = L.circle([lati, longi], {
           color: 'red',
           fillColor: '#f03',
           fillOpacity: 0.3,
           radius: 1500
       }).addTo(mymap1);
       L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoicHJvc3B0ZWFtMjAxOCIsImEiOiJjanBkZWlyMnoybHo0M3dxczh4ZGxtZG5kIn0.ycZHWnaVd_Lb2ceqYjYAqw', {
           attribution: 'Map data &copy; 2018',
           maxZoom: 18,
           id: 'mapbox.streets',
           accessToken: 'your.mapbox.access.token'
       }).addTo(mymap1);
  }
</script>
