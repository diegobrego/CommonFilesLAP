----- COMMON FILE -----

<?php
function ta($in) {
	if(TESTMODUS) {
		echo('<pre class="ta">');
		print_r($in);
		echo('</pre>');
	}
}
?>

----- CONN FILE -----

<?php
$conn = new MySQLi(DB["host"],DB["user"],DB["pwd"],DB["name"]);
if($conn->connect_errno>0) {
	if(TESTMODUS) {
		die("Fehler im Verbindungsaufbau: " . $conn->connect_error);
	}
	else {
		header("Location: errors/db_connect.html");
	}
}
$conn->set_charset("utf8mb4");
?>

----- CONFIG FILE -----

<?php
define("TESTMODUS",true);
define("DB",[
	"host" => "localhost",
	"user" => "root",
	"pwd" => "",
	"name" => "db_musikverwaltung"
]);

if(TESTMODUS) {
	error_reporting(E_ALL);
	ini_set("display_errors",1);
}
else {
	error_reporting(0);
	ini_set("display_errors",0);
}
?>

