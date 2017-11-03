<?php
$con = new mysqli("localhost","root","","maps");

if(!$con->connect_error > 0)
{
	if(isset($_GET["info"]))
	{
		$info = $_GET["info"];

		if($info == 'classificacao')
		{
			$dados = array();

			$tema = $_POST["tema"];

			if($result = $con->query("SELECT * FROM $tema"))
			{
				if($result->num_rows > 0)
				{
					while($row = $result->fetch_assoc())
					{
						$dados[] = array("id" => $row["id"], "classificacao" => $row["intervalo"], "cor" => $row["cor"]);
					}
				}
			}

			exit(json_encode($dados));
		}

		else if($info == 'get_last_block')
		{
			$dados = array();

			if($result = $con->query("SELECT MAX(idquadra) AS id FROM quadras"))
			{
				$row = $result->fetch_assoc();

				$dados[] = array("last_id" => $row["id"]);
			}

			exit(json_encode($dados));
		}

		else if($info == 'set_block')
		{
			$dados = array();

			$new_id = $_POST["last_id_sv"] + 1;
			$coords_pol = $_POST["coords"];

			$coords_pol = substr($coords_pol, 0, strlen($coords_pol) - 1);

			$sql = "INSERT INTO quadras SET idquadra = $new_id, descricao = '', poligono = '$coords_pol'";

			if($result = $con->query($sql))
			{
				$dados[] = array("status" => 200, "mesg" => "Quadra criada com sucesso\nId quadra: ".$new_id);
			}
			else
			{
				$dados[] = array("status" => 107, "mesg" => "Erro ao tentar criar quadra.");
			}

			exit(json_encode($dados));
		}

		else if($info == 'delete_block')
		{
			$dados = array();

			$id = $_POST["id_block"];

			if($result = $con->query("DELETE FROM quadras WHERE idquadra = $id"))
			{
				$dados[] = array("status" => 200, "mesg" => "<script type='text/javascript'>setTimeout(function() {location.reload();}, 1500);</script>Quadra removida com sucesso.");
			}
			else {
				$dados[] = array("status" => 107, "mesg" => "Erro ao tentar remover.");
			}

			exit(json_encode($dados));
		}

		else if($info == 'create_theme')
		{
			$dados = array();

			$nome_tema = $_POST["nome_tema"];

			$class = array();

			for($i = 0; $i < count($_POST["classificacao"]); $i++)
			{
				$class[] = array("classificacao" => $_POST["classificacao"][$i], "cor" => str_replace("#",null,$_POST["cor_tema"][$i]));
			}

			$sql = "CREATE TABLE tp_$nome_tema (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, intervalo VARCHAR(30) NULL, cor VARCHAR(6) NULL) ENGINE = InnoDB;";

			if($result = $con->query($sql))
			{
				for($i = 0; $i < count($class); $i++)
				{
					$result = $con->query("INSERT INTO tp_$nome_tema SET intervalo = '".$class[$i]["classificacao"]."', cor = '".$class[$i]["cor"]."'");
				}

				if($result = $con->query("ALTER TABLE quadras ADD id_$nome_tema INT NULL"))
				{
					$dados[] = array("mesg" => "<script type='text/javascript'>setTimeout(function() {location.reload();}, 1500);</script>Tema criado com sucesso.");
				}
				else {
					$dados[] = array("mesg" => "Erro ao tentar criar tema.");
				}
			}
			else {
				$dados[] = array("mesg" => "Erro ao tentar criar tema.");
			}
			
			exit(json_encode($dados));
		}

		else if($info == 'edit_block')
		{
			$quadra = $_POST["quadra"];
			$campo = $_POST["campo"];
			$valor = $_POST["valor"];

			$dados = array();

			$sql = "UPDATE quadras SET $campo = $valor WHERE idquadra = $quadra";

			if($result = $con->query($sql))
			{
				$dados[] = array("status" => 200, "mesg" => "Editado com sucesso.");
			}
			else {
				$dados[] = array("status" => 107, "mesg" => "Erro ao editar.");
			}

			exit(json_encode($dados));
		}
	}
}
?>