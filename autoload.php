<?php
function __autoload($classname) {
  $path = "src/classes/{$classname}.class.php";
  if(file_exists($path)) {
    require_once $path;
  }
}