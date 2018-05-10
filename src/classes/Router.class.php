<?php
$Page_Request = strtolower(basename($_SERVER['REQUEST_URI']));
$File_Request = strtolower(basename(__FILE__));

if ($Page_Request == $File_Request) {
    exit("");
}

if (!class_exists("Router")) {
	class Router extends Connection
	{
    public function __construct() {
      $this->initRoutes();
    }

    private function initRoutes() {
      if (isset($_GET["route"])) {

        switch ($_GET["route"]) {
          case "themes" :
            $theme = new Theme();
            $theme->getThemes();
            break;
          
          case "clfs" : 
            $name = $_GET["name"];
            $theme = new Theme();
            $theme->getClassifications($name);
            break;
          
          case "getblocks" :
            $block = new Block();
            $block->getBlocks();
            break;

          case "saveblock" :
            $block = new Block();
            $block->saveBlock($_POST["path"]);
            break;

          case "createtheme" :
            $theme = new Theme();
            $theme->createTheme();
            break;

          case "editblock" :
            $block = new Block();
            $block->editBlock();
            break;
        }
      }
    }
  }
}