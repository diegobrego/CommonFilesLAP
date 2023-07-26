<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/conn.inc.php");

function geschlechter_show():void {
	global $conn;
	
	echo('<select name="IDGeschlecht">');
	
	$sql = "
		SELECT * FROM tbl_geschlechter
		ORDER BY Geschlecht ASC
	";
	$geschlechter = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);
	while($geschlecht = $geschlechter->fetch_object()) {
		echo('
			<option value="' . $geschlecht->IDGeschlecht . '">' . $geschlecht->Geschlecht . '</option>
		');
	}
	
	echo('</select>');
}

function pruefe_svnr(int $in):bool {
	$arr = str_split($in);
	ta($arr);
	
	$pruefziffer = (
		$arr[0]*3 +
		$arr[1]*7 +
		$arr[2]*9 +
		$arr[4]*5 +
		$arr[5]*8 +
		$arr[6]*4 +
		$arr[7]*2 +
		$arr[8]+
		$arr[9]*6
		) % 11;
	
	return $pruefziffer==$arr[3];
}

$msg = '';
$ok = false; //gibt an, ob die Daten des Users erfolgreich eingetragen wurden; wenn ja, soll auf Basis dieser Info ein QR-Code generiert werden
ta($_POST);

if(count($_POST)>0) {
	if(pruefe_svnr(intval($_POST["SVNr"]))) {
		$sql = "
			SELECT
				COUNT(*) AS cnt
			FROM tbl_user
			WHERE(
				SVNr=" . $_POST["SVNr"] . "
			)
		";
		$userliste = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);
		$user = $userliste->fetch_object();
		if($user->cnt==0) {
			//diese SVNr existiert noch nicht
			//FEHLT: Überprüfung der SVNr
			
			$sql = "
				INSERT INTO tbl_user
					(Vorname, Nachname, SVNr, FIDGeschlecht)
				VALUES (
					'" . $_POST["VN"] . "',
					'" . $_POST["NN"] . "',
					" . $_POST["SVNr"] . ",
					" . $_POST["IDGeschlecht"] . "
				)
			";
			ta($sql);
			$ok = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);
			
			$msg = '<p class="success">Vielen Dank. Sie wurden registriert.</p>';
		}
		else {
			$msg = '<p class="error">Diese SVNr existiert bereits.</p>';
		}
	}
	else {
		$msg = '<p class="error">Bitte prüfen Sie die eingegebene Sozialversicherungsnummer.</p>';
	}
}
?>
<!doctype html>
<html lang="de">
	<head>
		<title>QR-Code</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
		<link rel="stylesheet" href="css/common.css">
		<script src="js/qrcode.min.js"></script>
		<style>
			#qrcode {
				margin:auto;
				padding:0.5em;
				border-radius:0.2em;
				border:1px solid #eee;
				width:fit-content;
			}
		</style>
	</head>
	<body>
		<?php echo($msg); ?>
		<form method="post">
			<label>
				Vorname:
				<input type="text" name="VN" required>
			</label>
			<label>
				Nachname:
				<input type="text" name="NN" required>
			</label>
			<label>
				SVNr:
				<input type="number" name="SVNr" required min="0001010100" max="9999311299" step="1">
			</label>
			<label>
				Geschlecht:
				<?php geschlechter_show(); ?>
			</label>
			<input type="submit" value="speichern">
		</form>
		
		<?php
		if($ok) {
		?>
			<div id="qrcode"></div>
			<script>
			var qrcode = new QRCode(document.querySelector("#qrcode"), {
			text: "<?php echo($_POST['SVNr'].', '.$_POST['VN'].' '.$_POST['NN']); ?>",
				width: 128,
				height: 128,
				colorDark : "#333333",
				colorLight : "#ffffff",
				correctLevel : QRCode.CorrectLevel.H
			});
			</script>
		<?php } ?>
	</body>
</html>