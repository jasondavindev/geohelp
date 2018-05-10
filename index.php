<?php
require_once "autoload.php";
require "configs/database.php";

$router = new Router();

$temp = new Template();
$temp->open("home");
$temp->show();