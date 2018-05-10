<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>GeoHelp 2017</title>

  <link rel="stylesheet" href="public/stylesheets/main.min.css">
  <link rel="stylesheet" href="public/stylesheets/home.min.css">
</head>

<body>
  <div class="row">
    <div class="cl-10">
      <div class="container">
        <div id="maps">
          <div id="map"></div>
          <div id="edit_map" style="display: none"></div>
          <button id="btn-edit-map" class="btn bg-white btn-circle">+</button>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="cl-4">
      <div class="container">
        <div class="box bg-white">
          <div id="themes-container">
            <div class="header-container">
              <h2 class="header">Temas</h2>
              <button id="btn-add-theme" class="btn bg-white">Adicionar tema</button>
            </div>
            <ul id="themes"></ul>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="box bg-white">
          <div class="header-classifications">
            <div class="header-card">
              <h2>Classificações
                <span id="name-classification"></span>
              </h2>
            </div>
            <div class="card-content">
              <ul id="classifications-theme"></ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="cl-6">
      <div class="container">
        <div class="box bg-white" style="padding: 0">
          <div id="card-blocks">
            <h2 class="container">Quadras</h2>
            <ul id="blocks"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="content-flex"></div>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAF_pe4i4A9UlOu8u3u99GoJLDzhihpyZU&callback&callback&callback&callback=initMap&libraries=drawing"></script>
  <script async defer src="public/javascripts/controlmap.min.js"></script>
  <script src="public/javascripts/jquery-3.2.1.min.js"></script>
  <script src="public/javascripts/home.min.js"></script>
</body>

</html>