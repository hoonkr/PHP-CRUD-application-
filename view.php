<?php
require_once "pdo.php";
require_once "util.php";

session_start();
if(isset($_POST['done'])){
	header('Location: index.php');
	return;
}
if(!isset($_GET['profile_id'])){
	$_SESSION['error'] = "Missing profile_id";
	header('Location: index.php');
	return;
}

$stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email, headline, summary, profile_id FROM profile where profile_id =:xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if( $profile === false ){
	$_SESSION['error'] ='Missing profile_id';
	header('Location: index.php');
	return;
}

$positions = loadPos($pdo, $_REQUEST['profile_id']);
$schools = loadEdu($pdo, $_REQUEST['profile_id']);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Hyung Hoon Song's view page</title>
	<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1>Profile information</h1>
<p>First Name:
<?echo (htmlentities($profile['first_name']))?></p>
<p>Last Name:
<?echo (htmlentities($profile['last_name']))?></p>
<p>Email:
<?echo (htmlentities($profile['email']))?></p>
<p>Headline:
<?echo (htmlentities($profile['headline']))?></p>
<p>Summary:
<?echo (htmlentities($profile['summary']))?></p>
<p>Education:
<ul>
<?php
foreach ($schools as $school){
	echo("<li>".htmlentities($school['year']).": ".htmlentities($school['name']). "</li>");
}
?>	
</ul></p>


<p>Position:
<ul>
<?php
foreach ($positions as $position){
	echo("<li>".htmlentities($position['year']).": ".htmlentities($position['description']). "</li>");
}
?>
</ul></p>

<p><a href="index.php">Done</a></p>
</div>	
</body>
</html>