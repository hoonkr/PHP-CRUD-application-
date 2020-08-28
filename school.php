<?php
//No need to continue
if( !isset($_GET['term']) ) die('Missing required parameter');

if( ! isset($_COOKIE[session_name()])){
	die("Must be logged in");
}

session_start();

if( !isset($_SESSION['name']) ){
	die("ACCESS DENIED");
}

require_once 'pdo.php';

header("Content-type: application/json; charset=utf-8");
$stmt = $pdo->prepare('SELECT name FROM Institution
    WHERE name LIKE :prefix');
$stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));

$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $retval[] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));
