<?php
session_start();

require_once "pdo.php";
require_once "util.php";

if(!isset($_SESSION['name'])){
	die('ACCESS DENIED');
}

if(isset($_POST['cancel'])){
	header('Location: index.php');
	return;
}

if( ! isset($_REQUEST['profile_id']) ){
	$_SESSION['error'] = "Missing profile_id";
	header('Location: index.php');
	return;
}
$stmt = $pdo-> prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt-> execute(array(":xyz" => $_GET['profile_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $profile === false ) {
    $_SESSION["error"] = 'Could not load profile';
    header( 'Location: index.php' ) ;
    return;
}
$fn = htmlentities($profile['first_name']);
$ln = htmlentities($profile['last_name']);
$em = htmlentities($profile['email']);
$hl = htmlentities($profile['headline']);
$sm = htmlentities($profile['summary']);
$uid = htmlentities($profile['user_id']);
$pid = htmlentities($profile['profile_id']);

if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']))
{
	$msg = validateProfile();
	if( is_string($msg) ){
		$_SESSION['error'] = $msg;
		header("Location:edit.php?profile_id=".$_REQUEST["profile_id"]);
		return;
	}

	$msg = validatePos();
	if( is_string($msg) ){
		$_SESSION['error'] = $msg; 
		header("Location: edit.php?profile_id=". $_REQUEST["profile_id"]);
		return;
	}

	$msg = validateEdu();
	if( is_string($msg) ) {
		$_SESSION['error'] = $msg;
		header("Location: edit.php?profile_id=". $_REQUEST["profile_id"]);
		return;
	}

	 
    $sql = "UPDATE profile SET first_name = :first_name,
    last_name = :last_name, email = :email,
    headline = :headline, summary = :summary
    WHERE profile_id = :pid AND user_id= :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':pid' => $_REQUEST['profile_id'],
        ':uid' => $_SESSION['user_id'])
	);

	$stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
	$stmt->execute(array(':pid' => $_REQUEST['profile_id']));

	insertPositions($pdo, $_REQUEST['profile_id']);

	$stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id =:pid');
	$stmt->execute(array(':pid' => $_REQUEST['profile_id']));

	insertEducations($pdo, $_REQUEST['profile_id']);

    $_SESSION['success'] = 'Profile updated';
    header( 'Location: index.php' ) ;
    return;
	       
}
$positions = loadPos($pdo, $_REQUEST['profile_id']);
$schools = loadEdu($pdo, $_REQUEST['profile_id']);



?>

<!DOCTYPE html>
<html>
<head>
	<title>Hyung Hoon Song's edit page</title>
	<?php require_once "head.php";?>
</head>
<body>
	<div = class="container">
		<h1>Editing Profile for <? echo $_SESSION['name']?></h1>

		<?php
		flashMessages(); 
		?>

		<form method="post">
		<p>First Name:
		<input type="text" name="first_name" id="first_name" size="60" value="<?= $fn ?>">
		</p>
		<p>Last Name:
		<input   type="text" name="last_name" id="last_name" size="60" value="<?= $ln?>"></p>
		<p>Email:
		<input   type="text" name="email" id="email" size="30" value="<?= $em ?>"></p>
		<p>Headline:<br/>
		<input   type="text" name="headline" id="headline" size="80" value="<?= $hl ?>"></p>
		<p>Summary:<br/>
		<textarea  name="summary" id="summary" rows="8" cols="80" ><?echo $sm ?></textarea> 
		</p> 
		<?php
		$countEdu = 0;
		echo('<p>Education: <input type="submit" id ="addEdu" value= "+">'."\n");
		echo('<div id="edu_fields">'."\n");
		if( count($schools)>0){
			foreach( $schools as $school ){
				$countEdu++;
				echo('<div id="edu'.$countEdu.'">');
				echo '<p> Year: <input type="text" name="edu_year'.$countEdu.'"value="'.$school['year'].'"/>
					<input type="button" value="-" onclick="$(\'#edu'.$countEdu.'\').remove();return flase;"></p>
					<p> School: <input type="text" size="80" name="edu_school'.$countEdu.'"class="school" value ="'.htmlentities($school['name']).'"/>';
				echo "\n</div>\n";
			}
		}


		echo("</div></p>\n");
		

		$countPos = 0;
		echo('<p>Position: <input type= "submit" id="addPos" value="+">'."\n");
		echo('<div id="position_fields">'."\n");
		foreach( $positions as $position ){
			$countPos++;
			echo('<div id="position'.$countPos.'">'."\n");
			echo('<p>Year: <input type ="text" name ="year'.$countPos.'"');
			echo(' value="'.$position['year'].'"/>'."\n");
			echo('<input type="button" value="-" ');
			echo('onclick="$(\'#position'.$countPos.'\').remove();return false;">'."\n");
			echo("</p>\n");
			echo('<textarea name="desc'.$countPos.'" rows="8" cols="80">'."\n");
			echo(htmlentities($position['description'])."\n");
			echo("\n</textarea>\n</div>\n");
		}
		echo("</div></p>\n");
		 ?>
		<input type="hidden" name="profile_id" value="<?= htmlentities($_GET ['profile_id']); ?>">
		<p>
		<input type="submit" value="Save">
		<input type="submit" name="cancel" value="Cancel">
		</p>
		</form>
		<script type="text/javascript">
		countPos = <?= $countPos ?>;
		countEdu = <?= $countEdu ?>; 

		$(document).ready(function(){
			window.console && console.log('Document ready called');
			$('#addPos').click(function(event){
				event.preventDefault();
				if(countPos >= 9){
					alert("Maximum of nine position entries exceeded");
					return;
				}
				countPos++;
				window.console && console.log("Adding position "+ countPos);
				$('#position_fields').append(
					'<div id="position' + countPos +'">\
					<p> Year: <input type="text" name="year' + countPos+'" value="" />\
					<input type="button" value="-"\
					onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
					<textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
					</div>');
			});


			$('#addEdu').click(function(event){
				event.preventDefault();
				if(countEdu >= 9){
					alert("Maximum of nine education entries exceeded");
					return;
				}
				countEdu++;
				window.console && console.log("Adding education "+ countEdu);

				var source = $("#edu-template").html();
				$('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

				$('.school').autocomplete({
					source: "school.php"
				}); 
			})

			$('.school').autocomplete({
				source: "school.php"
			});

		});	
		</script>

		<script id="edu-template" type="text">
			<div id ="edu@COUNT@">
				<p>Year: <input type= "text" name="edu_year@COUNT@" value=""/>
				<input type="button" value="-" onclick="$('#edu@COUNT@').remove(); return false;"><br>
				<p>School: <input type="text" size="80" name="edu_school@COUNT@" class = "school" value=""/>
			</div>
		</script>

	</div>
	</body>
</html>