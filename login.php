<?php
session_start();
require_once "pdo.php";
require_once "util.php";
if( isset($_POST['cancel'])){
	header("Location: index.php");
	return;
}
if(isset($_POST['email']) && isset($_POST['pass']) )
{
  $check = hash('md5', 'XyZzy12*_'.$_POST['pass']);


    $sql = "SELECT name, user_id FROM users 
        WHERE email = :em AND password = :pw ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':em' => $_POST['email'], 
        ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    var_dump($row);
   
   if ( $row === FALSE )
    {
       error_log("Login fail ".$pass." $check");
       $_SESSION['error'] = "Incorrect password";
       header("Location: login.php");
       return;
    } 
   else 
   { 
      error_log("Login success ".$email);
      $_SESSION['name'] = $_POST['email'];
      $_SESSION['user_id'] = $row['user_id'];
      header("Location: index.php");
      return;
   }
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Hyung Hoon Song's Login page</title>
   <?php require_once"head.php";?>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
flashMessages();
?>
<form method = "POST" action = "login.php">
<label for = "email">Email</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="pass"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>

<script type="text/javascript">
	function doValidate()
	{
		console.log('Validating...');
		try{
			addr = document.getElementById("email").value;
			pw = document.getElementById("pass").value;
			console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}

	
</script>


</div>

</body>
</html>