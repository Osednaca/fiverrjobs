<?php

include("connect.php");

$student_id = $_REQUEST["student_id"];
$name 		= $_REQUEST["name"];
$gender 	= $_REQUEST["gender"];
$birthday 	= $_REQUEST["birthday"];
$phone 		= $_REQUEST["phone"];
$email 		= $_REQUEST["email"];
$request_type = $_REQUEST["request_type"];

switch ($request_type) {
	case 'save_student':
		$sql = "INSERT INTO student(student_id, name, gender, birthday, phone, email) VALUES ('$student_id','$name','$gender','$birthday','$phone','$email')";
		$result = mysqli_query($link,$sql);
		if($result){
			echo json_encode(array("result"=>true,"msg"=>"Student register successfuly"));
		}else{
			echo json_encode(array("result"=>false,"msg"=>"Error."));
		}
		break;
	case 'edit_student':
		$id = $_REQUEST["id"];
		$sql = "UPDATE student SET student_id='$student_id',name='$name',gender='$gender',birthday='$birthday',phone='$phone',email='$email'WHERE id=$id";
		$result = mysqli_query($link,$sql);
		if($result){
			echo json_encode(array("result"=>true,"msg"=>"Student modified successfuly"));
		}else{
			echo json_encode(array("result"=>false,"msg"=>"Error."));
		}
		break;

	case 'delete_student':
		$id = $_REQUEST["id"];
		$sql = "DELETE FROM student WHERE id=$id";
		$result = mysqli_query($link,$sql);
		if($result){
			echo json_encode(array("result"=>true,"msg"=>"Student deleted successfully"));
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