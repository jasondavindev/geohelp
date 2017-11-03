<!DOCTYPE html>
<html>
<head>
	<title>Mapa tematico</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta charset="UTF-8"/>

	<link href="index.style.css" type="text/css" rel="stylesheet"/>
	<link href="https://fonts.googleapis.com/css?lang=pt-BR&family=Product+Sans|Roboto:400,700" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body>
<div class="row">
	<div class="col-12" style="background-color: #dfdfdf;">
		<div style="width: 100%; height: 400px; position: relative">
			<div id="map" class="map"></div>
			<div id="map_create_polygon" class="map"></div>
			<button id="edit_map" class="material-icons">add_location</button>
		</div>
	</div>
</div>
<div class="row">
<div class="col-4">
		<?php
		$con = new mysqli("localhost","root","","maps");

		/*
		* Exibicao temas
		*/
		if(!$con->connect_error > 0)
		{
			$sql = "SHOW TABLES";

			$str = "";

			if($result = $con->query($sql))
			{
				$str .= "<ul class=\"list-temas\" id=\"temas\"><div class=\"header\"><h2>Temas</h2><button class=\"material-icons\" id=\"add-theme\">add</button></div>";
				while($row = $result->fetch_assoc())
				{
					$name = $row["Tables_in_maps"];

					if(substr_count($name, "tp_") > 0)
					{
						$nameAs = str_replace("tp_","",$name);

						$str .= "<li id=\"".$name."\">$nameAs</li>";
					}
				}

				$str .= "</ul>";

				if($result = $con->query("SELECT * FROM quadras"))
				{
					while($row = $result->fetch_assoc())
					{

						foreach($row as $name => $value)
						{
							if(substr_count($name, "id_") > 0)
							{
								$valueRow = !empty($value) ? $value : 0;
							}
						}
					}
				}
			}

			print $str;

			print "<ul class=\"list-classificacoes\" id=\"classificacao\"><h2>Classificações <span id=\"nome-classificacao\"></span></h2><div id=\"content-classificaco\"></div></ul>";

			
		}
		?>
	</div>
	<div class="col-8">
	<?php
	if($result = $con->query("SELECT * FROM quadras"))
	{
		if($result->num_rows > 0)
		{
			print "<div class=\"display-blocks\">";

			while($row = $result->fetch_assoc())
			{
				$idquadra = $row["idquadra"];

				print "<div class=\"block-item\">";

				print "<div class=\"block-header\">";

				print "<div class=\"block-name\">Quadra ".$idquadra."</div>";

				print "<div class=\"block-action\">";

				print "<div class=\"block-edit\"><button class=\"material-icons edit-block\" data-action=\"delete-block\"data-block-id=\"".$idquadra."\">delete</button></div>";

				//print "<div class=\"block-edit\"><button class=\"material-icons\" id=\"del_".$idquadra."\">delete</button></div>";

				print "</div>"; // fecha div block-action

				print "</div>"; // fecha div block-header

				print "<div class=\"block-content\">";

				print "<a href='#' class=\"view_in_map\" data-pol=\"".substr($row["poligono"],0,strlen($row["poligono"]) - 1)."\">Ver no mapa</a>";

				foreach($row as $name => $value)
				{
					if(substr_count($name, "id_") > 0)
					{
						$name_t = str_replace("id_",null,$name);
						$value = ($value == null || empty($value)) ? 0 : $value;

						print "<div class=\"name-description\">".$name_t."</div>";

						print "<div class=\"description-value\"><p id=\"".$row["idquadra"]."-".$name."\">".$value."</p>".
							"<div class=\"dropdown-value\">";

						if($stmt = $con->query("SELECT * FROM ".str_replace("id_","tp_",$name)))
						{
							while($row_c = $stmt->fetch_assoc())
							{
								print "<a data-block-id=\"".$row["idquadra"]."\" data-item-session=\"".$name."\" data-item-value=\"".$row_c["id"]."\" href=\"#\">".$row_c["id"]."<i class=\"color\" style=\"background-color: #".$row_c["cor"]."\"></i></a>";
							}
						}

						print "</div></div>"; // fecha div description-value e dropdown-value

						//print "<div class=\"description-value\">".$value."</div>";
					}
				}

				print "</div>"; // fecha div block-content

				print "</div>"; // fecha div block-item
			}
		}
		print "</div>"; // fecha div display-blocks
	}
	?>
	</div>
</div>

<div class="modal" id="modal-create-theme">
	<div class="box">
		<button class="material-icons close-modal">close</button>
		<h2>Adicionar tema</h2>

		<form class="form-style" id="frm-create-theme">
			<input type="text" name="nome_tema" placeholder="Nome tema" class="input_text" autocomplete="off" required>
			<div class="classificacoes">
				<div class="item">
					<input type="text" name="classificacao[]" placeholder="Nome classificação" class="classification" required>
					<input type="color" name="cor_tema[]" placeholder="Cor" class="input-color" required>
				</div>
			</div>
			<div style="display: block; text-align: right;">
				<button class="material-icons" id="add-classification">add</button>
			</div>
			<button class="button-raised">Criar</button>
		</form>
	</div>
</div>

<div id="snack-message">
	<div id="content-snack">
		<span id="message"></span>
	</div>
</div>

<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAF_pe4i4A9UlOu8u3u99GoJLDzhihpyZU&callback&callback&callback&callback=initMap&libraries=drawing">
</script>
<script type="text/javascript" src="jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="main.js"></script>
<script type="text/javascript">
	function initMap() {
		var map = new google.maps.Map(document.getElementById('map'), {
		zoom: 15,
		center: {lat: -23.235203, lng: -45.915197},
		mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		<?php
		if($result = $con->query("SHOW TABLES"))
		{
			$temas = array();

			while($row = $result->fetch_assoc())
			{
				$temas[] = $row["Tables_in_maps"];

				print "var ".$row["Tables_in_maps"]." = [];";
			}

			if($result = $con->query("SELECT * FROM quadras"))
			{
				while($row = $result->fetch_assoc())
				{
					foreach($row as $name => $value)
					{
						if(substr_count($name, "id_") > 0)
						{
							if($value != null && $value != 0)
							{
								$tema = str_replace("id_","tp_",$name);
								$tema_alert = str_replace("id_",null,$name);

								if($rs = $con->query("SELECT * FROM $tema WHERE id = $value"))
								{
									$row_t = $rs->fetch_assoc();

									print "var coord_".$row["idquadra"]."_$tema = [";

									$arryPol = explode(",",$row["poligono"]);

									$i = 1;
									foreach($arryPol as $name => $value)
									{
										if(!empty($value))
										{
											if($i%2==0) print ", lng: $value},";
											else print "{lat: $value";

											$i++;
										}
									}
									print "];";

									print "var quadra_".$row["idquadra"]."_$tema = new google.maps.Polygon({
											paths: coord_".$row["idquadra"]."_$tema,
											strokeColor: '#".$row_t["cor"]."',
											strokeOpacity: 0.8,
											strokeWeight: 2,
											fillColor: '#".$row_t["cor"]."',
											fillOpacity: 0.65
											});
											
											google.maps.event.addListener(quadra_".$row["idquadra"]."_$tema, 'click', function () {
											alert('Quadra: ".$row["idquadra"]."\\n".$tema_alert.": ".$row_t["intervalo"]."');
											});";

									print "$tema.push(quadra_".$row["idquadra"]."_$tema);";
								}
							}
						}
					}
				}
			}

			if(count($temas) > 1) {
				print "for(var i in ".$temas[1].") {".$temas[1]."[i].setMap(map);}";
			}
		}
		?>

		$('.block-header').click(function() {
			$(this).next().slideToggle('fast');
		});

		$('a.view_in_map').click(function(e) {
			e.preventDefault();
			e.stopPropagation();

			var coord = $(this).attr('data-pol');

			var arry_coord = coord.split(',');

			var b = 1;
			var coords = '';
			var str = 'var map_view_coord = [';

			for(var i in arry_coord)
			{
				if(b%2==0)
				{
					str += ', lng: '+arry_coord[i]+'},';
					coords += arry_coord[i]+',';
				}
				else
				{
					str += '{lat: '+arry_coord[i];
					coords += arry_coord[i]+',';
				}
				b++;
			}

			str += '];';

			eval(str);

			var map_view_pol = new google.maps.Polygon({
				paths: map_view_coord,
				strokeColor: '#000000',
				strokeOpacity: 1.0,
				strokeWeight: 2,
				fillColor: '#ffffff',
				fillOpacity: 1.00
			});

			var coords_map = coords.split(',');

			map.setOptions({center: {lat: parseFloat(coords_map[0]), lng: parseFloat(coords_map[1])}, zoom: 17});

			map_view_pol.setMap(map);
			$('html, body').animate({scrollTop: $("#map").offset().top}, 500);

			setTimeout(function() {
				map_view_pol.setMap(null);
			}, 5000);
		})

		$('#temas li').on('click', function() {

			var tema = $(this).attr('id');
			var temas_dom = $('#temas li');

			$('#temas li').removeClass('active');
			$(this).addClass('active');

			var name = $(this).attr('id');
			
			for(var i = 0; i < temas_dom.length; i++)
			{
				var id = $((temas_dom)[i]).attr('id');

				if(id == tema)
				{
					eval('for(var b in '+id+') {'+id+'[b].setMap(map);}');
				}
				else
				{
					eval('for(var b in '+id+') {'+id+'[b].setMap(null);}');
				}
			}

			$.ajax({
				url: 'carregar_infos.php?info=classificacao',
				type: 'POST',
				dataType: 'json',
				data: {tema: tema},
			})
			.done(function(data)
			{
				var obj,
				json = JSON.stringify(data);

				if(obj = $.parseJSON(json))
				{
					$('#content-classificaco').html('');
					$('#nome-classificacao').text('('+tema.replace('tp_','')+')');
					
					for(var i in obj)
					{
						$('#content-classificaco').append('<li>'+
							'<div class="cell">'+obj[i].id+'</div>'+
							'<div class="cell">'+obj[i].classificacao+'</div>'+
							'<div class="cell color-theme" style="background-color: #'+obj[i].cor+'"></div>'+
							'</li>');
					}
				}
			})
			.fail(function() {
				console.log("error");
			});
		});

		$('#edit_map').on('click',function() {
			$('#map').toggle();
			$('#map_create_polygon').toggle();

			if($('#map').is(':visible'))
			{
				$(this).text('add_location');
				$('#snack-message').hide();
				$('#snack-message').show();
				$('#message').text('Modo criar quadra desativado.');
				setTimeout(function() { $('#snack-message').hide(); },3000);

			}
			else {
				$(this).text('place');
				$('#snack-message').hide();
				$('#snack-message').show();
				$('#message').text('Modo criar quadra ativado.');
				setTimeout(function() { $('#snack-message').hide(); },3000);
			}

			var map_edit = new google.maps.Map(document.getElementById('map_create_polygon'), {
				zoom: 15,
				center: {lat: -23.235203, lng: -45.915197},
				mapTypeId: google.maps.MapTypeId.ROADMAP
			});

			var drawingManager = new google.maps.drawing.DrawingManager({
				drawingMode: google.maps.drawing.OverlayType.POLYLINE,
				drawingControl: true,
				drawingControlOptions: {
					position: google.maps.ControlPosition.TOP_CENTER,
					drawingModes: [
						google.maps.drawing.OverlayType.POLYLINE
					]
				}
			});

			google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
				if (event.type == google.maps.drawing.OverlayType.POLYLINE) {

					var drawn = event.overlay;
					drawingManager.setOptions({drawingMode: null});

					var len = drawn.getPath().getLength();

					var str = '';

					for(var i = 0; i < len; i++)
					{
						str += drawn.getPath().getAt(i).toUrlValue(5)+',';
					}

					$.ajax({
						url: 'carregar_infos.php?info=get_last_block',
						type: 'POST',
						dataType: 'json',
					})
					.done(function(data)
					{
						var obj;
						var json = JSON.stringify(data);

						if(obj = $.parseJSON(json))
						{
							var last_id = obj[0].last_id != null ? obj[0].last_id : 0;

							$.ajax({
								url: 'carregar_infos.php?info=set_block',
								type: 'POST',
								dataType: 'json',
								data: {last_id_sv: last_id, coords: str},
							})
							.done(function(data)
							{
								var obj;
								var json = JSON.stringify(data);

								if(obj = $.parseJSON(json))
								{
									if(obj[0].mesg)
									{
										alert(obj[0].mesg);
									}
								}
							})
							.fail(function() {
								alert('Error');
							});
						}
					})
					.fail(function() {
						alert('Error');
					});
				}
			});

			drawingManager.setMap(map_edit);
		});

		if($('#temas li').length > 0) {
			$('#temas li')[0].click();
		}
	}
</script>
</body>
</html>