<?php
$Page_Request = strtolower(basename($_SERVER['REQUEST_URI']));
$File_Request = strtolower(basename(__FILE__));

if ($Page_Request == $File_Request) {
  exit("");
}

if (!class_exists("Connection")) {
  class Connection
  {
    private $con = null;

    protected function connect() {
      try {
        $this->con = new PDO("mysql:host=" . HOST . ";dbname=" . DATABASE, USER, PASSWORD);
      } catch (PDOException $e) {
        exit(MYSQL_ERROR_CONNECT);
      }
      return $this->con;
    }

    protected function execute($sql) {
      return $sql->execute();
    }

    protected function prepare($stmt) {
      return $this->con->prepare($stmt);
    }

    protected function fetch($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function numRow($stmt) {
      return $stmt->rowCount();
    }

    public function close() {
      try {
        $this->con = null;
      } catch (PDOException $e) {
        exit(MYSQL_ERROR_CLOSE_CONNECT);
      }
    }
  }
}
