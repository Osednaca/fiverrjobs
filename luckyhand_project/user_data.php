<?php

/**
 * Encrypting password
 * returns salt and encrypted password
 */
function hashSSHA($password) {
    $salt = sha1(rand());
    $salt = substr($salt, 0, 10);
    $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
    $hash = array("salt" => $salt, "encrypted" => $encrypted);
    return $hash;
}

include("connect.php");

$username 		= $_REQUEST["username"];
$password 		= $_REQUEST["password"];
$createdon  	= date("Y-m-d H:m:i");
$request_type = $_REQUEST["request_type"];

$salt = hashSSHA($password);

switch ($request_type) {
	case 'save_user':
		$sql = "INSERT INTO users(username, password, salt, created) VALUES ('$username','".$salt["encrypted"]."','".$salt["salt"]."','$createdon')";
		$result = mysqli_query($link,$sql);
		if($result){
			echo json_encode(array("result"=>true,"msg"=>"User register successfuly"));
		}else{
			echo json_encode(array("result"=>false,"msg"=>"Error."));
		}
		break;
	case 'list_student':
		$data = array();
		$sql = "SELECT *,id idstudent FROM student";
		$result = mysqli_query($link,$sql);
		while ($Estudiante = mysqli_fetch_assoc($result)) {
			$data[] = $Estudiante;
		}
		if($result){
			echo json_encode(array("result"=>true,"data"=>$data));
		}else{
			echo json_encode(array("result"=>false,"msg"=>"Error."));
		}
		break;
	
	default:
		# code...
		break;
}


?>