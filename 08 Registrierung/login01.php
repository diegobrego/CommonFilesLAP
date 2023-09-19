<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/conn.inc.php");

function res($c,$in) {
	return $c->real_escape_string($in);
}

ta($_POST);
$msg = "";

if(count($_POST)>0) {
	$sql = "
		SELECT
			IDUser
		FROM tbl_user
		WHERE(
			Emailadresse='" . res($conn,$_POST["E"]) . "' AND
			(
				SELECT
					Passwort
				FROM tbl_passwoerter
				WHERE(
					FIDUser=IDUser
				)
				ORDER BY Nutzungszeitpunkt DESC
				LIMIT 1			
			)='" . res($conn,$_POST["P"]) . "'
		)
	";
	$userliste = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);
	ta($userliste);
	if($userliste->num_rows==1) {
		//diese Emailadresse existiert und das Passwort ist das zuletzt verwendete
		$user = $userliste->fetch_object();
		ta($user);
		$idUser = $user->IDUser;
			
		session_start();
		$_SESSION["eingeloggt"] = true;
		$_SESSION["idUser"] = $idUser;
		header("Location: profil.php");
	}
	else {
		$msg = '<p class="error">Leider waren die eingegebenen Daten nicht korrekt.</p>';
	}
}
?>
<!doctype html>
<html lang="de">
	<head>
		<title>Login</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/common.css">
	</head>
	<body>
		<?php echo($msg); ?>
		<form method="post">
			<label>
				Emailadresse:
				<input type="email" name="E" required>
			</label>
			<label>
				Passwort:
				<input type="password" name="P" required>
			</label>
			<input type="submit" value="login">
		</form>
	</body>
</html>