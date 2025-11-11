<?php
session_start();
session_destroy();
header("Location: /proyectophp/index.php");
exit();
?>