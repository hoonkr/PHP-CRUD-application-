<?php
session_start();

require_once "pdo.php";
require_once "util.php";

?>

<!DOCTYPE html>
<html>
<head>
	<title>Hyung Hoon Song's index page</title>
    <?php require_once"head.php";?>
</head>
<body>
<div class="container">
	<h1>Hyung Hoon Song's Resume Registry</h1>
<?php
if(!isset($_SESSION['name']))
	{
		echo('<p><a href ="login.php">Please log in</a><p>');
	}
?>


<?php
if(isset($_SESSION['name']))
{
  flashMessages();
    
	$stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM profile ");
	$result_table = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($result_table)) { 
     echo ('<p>No rows found</p>');
}
else{
    echo('<table border="1">
        <thead><tr>
        <th>Name</th>
        <th>Headline</th>
        <th>Action</th>
        </tr></thead>');
    foreach( $result_table as $row ) {
        echo "<tr><td>";
        echo("<a href='view.php?profile_id=".$row['profile_id']."'>".htmlentities($row['first_name'])." ".htmlentities($row['last_name']). "</a>");
        echo "</td><td>";
        echo(htmlentities($row['headline']));
        echo("</td><td>");
        echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
        echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
        echo("</td></tr>\n");
    }
    echo("</table>\n");
}

	echo('<div>');
	echo ('<p><a href="add.php">Add New Entry</a></p>');
	echo "</div>";

	echo ('<div>');
	echo ('<p><a href="logout.php">Logout</a></p>');
	echo "</div>";


}
?>	
</div>
</body>
</html>