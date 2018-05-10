<?php
$Page_Request = strtolower(basename($_SERVER['REQUEST_URI']));
$File_Request = strtolower(basename(__FILE__));

if ($Page_Request == $File_Request) {
    exit("");
}

if (!class_exists("Block")) {
	class Block extends Connection
	{
    public function __construct() {
      $this->connect();
    }

    public function saveBlock($path) {
      $stmt = $this->prepare("INSERT INTO quadras SET poligono = ?");
      $stmt->bindValue(1, $path, PDO::PARAM_STR);

      $response = array(
        "saved" => false
      );

      if ($stmt->execute()) {
        $response["saved"] = true;
      }
      $json = json_encode($response);
      exit($json);
    }

    public function getBlocks() {
      $stmt = $this->prepare("SELECT * FROM quadras");

      $response = array();

      if ($stmt->execute()) {
        $fields = array();
        
        while ($row = $this->fetch($stmt)) {
          if (count($fields) === 0) {
            foreach($row as $name => $value) {
              if (preg_match("/^id_/", $name)) {
                $fields[] = $name;
              }
            }
          }

          $block = array(
            "id" => $row["idquadra"],
            "poligon" => $row["poligono"]
          );
          foreach ($fields as $value) {
            $block[$value] = $row[$value];
          }
          $response[] = $block;
        }
      }
      $json = json_encode($response);
      exit($json);
    }

    public function editBlock() {
      $id = $_POST["id"];
      $value = $_POST["value"];
      $field = $_POST["field"];

      $response = array(
        "saved" => false
      );

      $stmt = $this->prepare("SELECT id FROM tp_{$field} WHERE id = ?");
      $stmt->bindValue(1, $value, PDO::PARAM_INT);

      if ($stmt->execute() && $this->numRow($stmt) > 0) {
        $stmt = $this->prepare("UPDATE quadras SET id_{$field} = ? WHERE idquadra = ?");
        $stmt->bindValue(1, $value, PDO::PARAM_INT);
        $stmt->bindValue(2, $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
          $response["saved"] = true;
        }
      }
      $json = json_encode($response);
      exit($json);
    }
  }
}