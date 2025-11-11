<?php
session_start();
date_default_timezone_set('America/Mexico_City');

if (!isset($_SESSION['usuario'])) {
    header("Location: /proyectophp/index.php");
    exit();
}

ob_start();

$url = isset($_GET['url']) ? rtrim($_GET['url'], "/") : "dashboardh";
$url = preg_replace('/[^a-zA-Z0-9_-]/', '', $url);


$archivo = __DIR__ . "/" . $url . ".php";


if (!file_exists($archivo)) {
    $archivo = __DIR__ . "/dashboardh.php";
}


include __DIR__ . "/header.php";
include $archivo;
include __DIR__ . "/footer.php";

ob_end_flush();
?>