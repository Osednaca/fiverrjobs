<?php

include("connect.php");

$idstudent = $_REQUEST["idstudent"];
$request_type = $_REQUEST["request_type"];
$fil  ="";

switch ($request_type) {
	case 'list_grades':
		if($idstudent!=""):
			$fil = "WHERE idstudent=$idstudent";
		endif;
		$data = array();
		$sql = "SELECT *,student.id idstudent,exam_results.id idcalification FROM exam_results INNER JOIN student ON exam_results.idstudent = student.id $fil";
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