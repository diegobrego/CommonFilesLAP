<?php
session_start();
if(!(isset($_SESSION["eingeloggt"]) && $_SESSION["eingeloggt"])) {
	header("Location: login.php");
}

require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/conn.inc.php");
?>
<!doctype html>
<html lang="de">
	<head>
		<title>Profil</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/common.css">
	</head>
	<body>
		<h1>Mein Profil</h1>
	</body>
</html>