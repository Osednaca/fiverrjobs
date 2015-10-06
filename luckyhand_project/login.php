<?php

/**
 * Decrypting password
 * returns hash string
 */
function checkhashSSHA($salt, $password) {
    $hash = base64_encode(sha1($password . $salt, true) . $salt);
    return $hash;
}

session_start();
include("connect.php");

if(!empty($_REQUEST))
{
	$data = json_decode($_POST["data"]);
	$username = trim($data->username);
	$password = trim($data->password);
	$sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($link,$sql);
	if($result){
		$row = mysqli_fetch_assoc($result);
   		$salt = $row['salt'];
        $encrypted_password = $row['password'];
        $hash = checkhashSSHA($salt, $password);
        //echo $encrypted_password." == ". $hash;
        //die();
		if($encrypted_password == $hash){
			$SKey = uniqid(mt_rand(), true);
			$timestamp = date("Y-m-d H:m:i");
     		$_SESSION["userid"]  = $row["id"];
			$_SESSION["username"] 		= $row['username'];
         	$_SESSION['userAgent'] 	= sha1($_SERVER['HTTP_USER_AGENT']);
			$_SESSION['SKey'] 		= $SKey;
			$_SESSION['IPaddress'] 	= $_SERVER["REMOTE_ADDR"];
			$_SESSION['LastActivity'] = $_SERVER['REQUEST_TIME'];
			echo json_encode(array('result' => true,'msg' => "Login successfully."));
		}else{
			echo json_encode(array('result' => false,'msg' => "Error."));
		}
	}else{
			echo json_encode(array('result' => false,'msg' => "User or Password incorrect"));
	}
}
?>