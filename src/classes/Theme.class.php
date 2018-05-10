<?php
$Page_Request = strtolower(basename($_SERVER['REQUEST_URI']));
$File_Request = strtolower(basename(__FILE__));

if ($Page_Request == $File_Request) {
  exit("");
}

if (!class_exists("Theme")) {
  class Theme extends Connection
  {
    public function __construct()
    {
      $this->connect();
    }

    public function getThemes()
    {
      $stmt = $this->prepare("SHOW TABLES");

      $response = array();

      if ($stmt->execute()) {
        while ($row = $this->fetch($stmt)) {
          $name = $row["Tables_in_maps"];

          if (preg_match("/^tp_/", $name)) {
            $response[] = array(
              "old" => $name,
              "new" => str_replace("tp_", "", $name),
            );
          }
        }
      }
      $json = json_encode($response);
      exit($json);
    }

    public function getClassifications($theme)
    {
      if (preg_match("/^tp_/", $theme)) {
        $stmt = $this->prepare("SELECT * FROM $theme");

        $response = array();

        if ($stmt->execute()) {
          while ($row = $this->fetch($stmt)) {
            $response[] = array(
              "id" => $row["id"],
              "interval" => $row["intervalo"],
              "color" => $row["cor"]
            );
          }
        }
        $json = json_encode($response);
        exit($json);
      }
    }

    public function createTheme() {
      $name = strtolower($_POST["theme-name"]);

      $response = array(
        "saved" => false,
      );

      $stmt = $this->prepare("CREATE TABLE tp_{$name} (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        intervalo VARCHAR(30) NOT NULL,
        cor VARCHAR(6) NOT NULL
      ) ENGINE = InnoDB");

      if ($stmt->execute()) {
        for ($i = 0; $i < count($_POST["name-classification"]); $i++) {
          
          $stmt = $this->prepare("INSERT INTO tp_{$name} (intervalo,cor) VALUES (?,?)");
          $stmt->bindValue(1, $_POST["name-classification"][$i], PDO::PARAM_STR);
          $stmt->bindValue(2, str_replace("#","",$_POST["color-classification"][$i]), PDO::PARAM_STR);

          $stmt2 = $this->prepare("ALTER TABLE quadras ADD id_{$name} INT DEFAULT 1");

          if ($stmt->execute() && $stmt2->execute()) {
            $response["saved"] = true;
          }
        }
      }
      $json = json_encode($response);
      exit($json);
    }
  }
}
