<?php

include("connect.php");

$idstudent = $_REQUEST["idstudent"];
$grade1 		= $_REQUEST["grade1"];
$grade2 		= $_REQUEST["grade2"];
$grade3 		= $_REQUEST["grade3"];
$request_type = $_REQUEST["request_type"];
$date = date("Y-m-d H:m:i");

switch ($request_type) {
	case 'save_grade':
		$sql = "INSERT INTO exam_results(idstudent, exam1, exam2, exam3, date) VALUES ('$idstudent','$grade1','$grade2','$grade3','$date')";
		$result = mysqli_query($link,$sql);
		if($result){
			echo json_encode(array("result"=>true,"msg"=>"grade register successfuly"));
		}else{
			echo json_encode(array("result"=>false,"msg"=>"Error."));
		}
		break;
	case 'edit_grade':
		$id = $_REQUEST["id"];
		$sql = "UPDATE exam_results SET idstudent='$idstudent',exam1='$grade1',exam2='$grade2',exam3='$grade3' WHERE id=$id";
		$result = mysqli_query($link,$sql);
		if($result){
			echo json_encode(array("result"=>true,"msg"=>"grade modified successfuly"));
		}else{
			echo json_encode(array("result"=>false,"msg"=>"Error."));
		}
		break;

	case 'delete_grade':
		$id = $_REQUEST["id"];
		$sql = "DELETE FROM exam_results WHERE id=$id";
		$result = mysqli_query($link,$sql);
		if($result){
			echo json_encode(array("result"=>true,"msg"=>"grade deleted successfully"));
		}else{
			echo json_encode(array("result"=>false,"msg"=>"Error."));
		}
		break;
	case 'list_grades':
		$data = array();
		$sql = "SELECT *,student.id idstudent,exam_results.id idgrade FROM exam_results INNER JOIN student ON exam_results.idstudent = student.id";
		$result = mysqli_query($link,$sql);
		while ($student = mysqli_fetch_assoc($result)) {
			$data[] = $student;
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