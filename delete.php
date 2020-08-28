<?php
require_once "pdo.php";
session_start();
if(isset($_POST['cancel'])){
	header('Location: index.php');
	return;
}

if(isset($_POST['delete']) && isset($_POST['profile_id'])){
	$sql = "DELETE FROM profile WHERE profile_id = :zip";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(':zip' => $_POST['profile_id']));
	$_SESSION['success'] = 'Recored deleted';
	header('Location: index.php');
	return;
}


if(!isset($_GET['profile_id']))
{
	$_SESSION['error'] = "Missing profile_id";
	header('Location: index.php');
	return;
}

$stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email, headline,summary, profile_id FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Hyung Hoon Song's delete page</title>
	<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">
</head>
<body>
<div class="container">
	<h1>Deleting Profile</h1>
	<form method="post">
		<p>First Name:
		<?echo (htmlentities($row['first_name']))?></p>
		<p>Last Name:
		<?echo (htmlentities($row['last_name']))?></p>
		<input type="hidden" name="profile_id" value="<?=$row['profile_id']?>">
		<input type="submit" value="Delete" name="delete">
		<input type="submit" name="cancel" value="Cancel">
	</form>

</div>
</body>
</html>