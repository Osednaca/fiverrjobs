<?php

$link = mysqli_connect("23.229.163.168", "cre8_webservice", "Whone8=Pr.NP", "cre8chat_wp1");

if (mysqli_connect_errno()) {
    printf("Error: %s\n", mysqli_connect_error());
    exit();
}


$type_request = $_REQUEST["type_request"];

switch ($type_request) {
	case 'register':
		$name 		  = $_REQUEST["name"];
		$image_url    = $_REQUEST["image_url"];
		$phone_number = $_REQUEST["phone_number"];
		$gender       = $_REQUEST["gender"];
		$email 		  = $_REQUEST["email"];
		$status 	  = 0;
		$password 	  = md5($_REQUEST["password"]);
		$isSocialReg  = $_REQUEST["isSocialReg"];
		$socialtype	  = $_REQUEST["socialtype"];
		$socialid	  = $_REQUEST["socialid"];

		$result = mysqli_query($link,"INSERT INTO users(user_login, user_pass, user_nicename, phone, gender, user_email, user_status, display_name, image_url, isfacebooklogin, socialtype, facebookid) 
											VALUES ('$email','$password','$name','$phone_number','$gender','$email','$status','$name','$image_url','$isSocialReg','$socialtype','$socialid')");
		if(!$result):
			echo json_encode(array("result"=>false));
		else:
			$last_id = mysqli_insert_id($link);
			//echo $last_id;
			$sql = "SELECT * FROM users WHERE ID=".$last_id;
			//echo $sql; die;
			$result  = mysqli_query($link,$sql);
			$User 	 = mysqli_fetch_assoc($result); 
			echo json_encode(array("result"=>true,"msg"=>"User registered successfully.","data"=>$User));
		endif;

	break;
	
	case 'login':
		$email 		  = $_REQUEST["email"];
		$password 	  = md5($_REQUEST["password"]);

		$sql  = "SELECT * FROM users WHERE user_email=?";
        $stmt = mysqli_prepare($link,$sql);
        if($stmt){
        	mysqli_stmt_bind_param($stmt, "s", $email);
        	mysqli_stmt_execute($stmt);
        	$meta = $stmt->result_metadata();
	
			while ($field = $meta->fetch_field()) {
  				$parameters[] = &$row[$field->name];
			}
	
			call_user_func_array(array($stmt, 'bind_result'), $parameters);
	
			mysqli_stmt_fetch($stmt);
			//var_dump($row); die();
        	$encrypted_password = $row['user_pass'];
        	//echo $encrypted_password." = ".$password; die();
			if($encrypted_password == $password){
				echo json_encode(array('result' => true,'msg'=>'User logged successfully.','data' => $row));
			}else{
				// Incorrect Password
				echo json_encode(array('result' => false,'msg'=>'Incorrect password or user.'));
			}
		}else{
			// Query or Internal Error
			echo json_encode(array('respuesta' => false,'error' => $db->errorInfo()));
		}


	break;

	case 'upload_file':
		$typeoffile = $_REQUEST["typeoffile"];
		$image 		= $_FILE["file"];
		var_dump($_FILE);
		if($typeoffile==1):
			$target_dir = "uploads_img/";
		elseif($typeoffile==2):
			$target_dir = "upload_video/";
		endif;
		$target_file = $target_dir . basename($_FILES["file"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
    	$check = getimagesize($_FILES["file"]["tmp_name"]);
    	if($check !== false) {
        	$uploadOk = 1;
    	} else {
        	$uploadOk = 0;
    	}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
    		echo json_encode(array("msg" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."));
    		$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
    		echo json_encode(array("msg" => "Sorry, your file was not uploaded."));
		// if everything is ok, try to upload file
		} else {
    		if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        		echo json_encode(array("msg"=>"The file  has been uploaded.", "file"=>$target_file));
    		} else {
        		echo json_encode(array("msg"=>"Sorry, there was an error uploading your file."));
    		}
		}
	break;

	case 'get_users':
		$user_id 	= $_REQUEST["user_id"];
		$filter 	= $_REQUEST["filter"];
		$numofrows  = $_REQUEST["numofrows"];
		$row 		= array();
		$data 		= array();
		if($user_id!=""):
			if($filter!=""):
				$sql = "SELECT ID,user_login,image_url,phone,gender,user_email FROM users WHERE user_nicename LIKE ? LIMIT $numofrows";
			//echo $sql; die();
				$stmt = $link->stmt_init();
				$stmt = $link->prepare($sql);
				if($stmt){
					/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
					$filter = "%".$filter."%";
					$stmt->bind_param('s', $filter);
 	
					$stmt->execute();
					$stmt->bind_result($ID,$user_login,$image_url,$phone,$gender,$user_email);
					while ($stmt->fetch()) {
											    // search in friends table
						$sql    = "SELECT * FROM friends_tbl WHERE user_id = $user_id AND rec_id=".$ID." AND accepted=1";
						$result = mysqli_query($link,$sql);
						if($result):
							$isfriend = 1;
						else:
							$isfriend = 0;
						endif;
						array_push($row, array("user_id"=>$ID,"user_login"=>$user_login,"image_url"=>$image_url,"phone"=>$phone,"gender"=>$gender,"user_email"=>$user_email,"isfriend"=>$isfriend));
					}
					echo json_encode(array('result' => true,'data' => $row));
				}else{
					// Query or Internal Error
					echo json_encode(array('respuesta' => false,'error' => $db->errorInfo()));
				}
			else:
				$sql = "SELECT * FROM users LIMIT $numofrows";
				//echo $sql; die();
				$result = mysqli_query($link,$sql);
				$i = 0;
				while($row = mysqli_fetch_assoc($result)){
					$data[$i]   = $row;
					$sql    = "SELECT * FROM friends_tbl WHERE user_id = $user_id AND rec_id=".$row["ID"]." AND accepted=1";
					//echo $sql;
					$result1 = mysqli_query($link,$sql);
					if($result1):
						$data[$i]["isfriend"] = 1;
					else:
						$data[$i]["isfriend"] = 0;
					endif;
					$i++;
				}
				echo json_encode(array('result' => true,'data' => $data));
			endif;
		else:
			echo json_encode(array('result' => false,'data' => "You must specify the user id."));
		endif;
	break;

	case 'add_friend':
		$user_id 	= $_REQUEST["user_id"];
		$friend_id  = $_REQUEST["friend_id"];
		$accepted   = 0;
		$sql = "INSERT INTO friends_tbl(sender_id, rec_id, accepted) VALUES ('$user_id','$friend_id',$accepted)";
		$result = mysqli_query($link,$sql);
		if(!$result):
			echo json_encode(array("result"=>false));
		else:
			$last_id = mysqli_insert_id($link);
			$result  = mysqli_query($link,"SELECT * FROM friends_tbl WHERE id=".$last_id);
			$User 	 = mysqli_fetch_assoc($result); 
			echo json_encode(array("result"=>true,"msg"=>"Friend added successfully","data"=>$User));
		endif;
	break;

	case 'remove_friend':
		$user_id 	= $_REQUEST["user_id"];
		$friend_id  = $_REQUEST["friend_id"];
		$sql = "DELETE FROM friends_tbl WHERE sender_id='$user_id' AND rec_id='$friend_id'";
		$result = mysqli_query($link,$sql);
		if(!$result):
			echo json_encode(array("result"=>false));
		else:
			echo json_encode(array("result"=>true,"msg"=>"Friend deleted successfully"));
		endif;
	break;

	case 'add_contest':
		$user_id 	= $_REQUEST["user_id"];
		$image_url  = $_REQUEST["image_url"];
		$text 		= $_REQUEST["text"];
		$result = mysqli_query($link,"INSERT INTO contest_tbl(user_id, status_text, image_url) VALUES ('$user_id','$text','$image_url')");
		if(!$result):
			echo json_encode(array("result"=>false));
		else:
			$last_id = mysqli_insert_id($link);
			$result  = mysqli_query($link,"SELECT * FROM contest_tbl WHERE id=".$last_id);
			$Contest = mysqli_fetch_assoc($result); 
			echo json_encode(array("result"=>true,"msg"=>"Contest registered successfully","data"=>$Contest));
		endif;
	break;

	case 'get_contest':
		$user_id 	= $_REQUEST["user_id"];
		$numofrows  = $_REQUEST["numofrows"];
		$row = array();
		$sql = "SELECT user_id,user_nicename,status_text,users.image_url,like_count,user_image_url FROM contest_tbl INNER JOIN users ON users.ID = contest_tbl.user_id WHERE user_id=? LIMIT $numofrows";
		//echo $sql; die();
		$stmt = $link->stmt_init();
		$stmt = $link->prepare($sql);
		if($stmt){
			/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
			$stmt->bind_param('i', $user_id);
 	
			$stmt->execute();
			$stmt->bind_result($user_id,$username,$status_text,$image_url,$like_count,$user_image_url);
			while ($stmt->fetch()) {
				array_push($row, array("user_id"=>$user_id,"username"=>$username,"status_text"=>$status_text,"image_url"=>$image_url,"like_count"=>$like_count,"user_image_url"=>$user_image_url));
			}
			//var_dump($row); die();
			echo json_encode(array('result' => true,'data' => $row));
		}else{
			echo json_encode(array('result' => false));
		}
	break;

	case 'send_message':
		$user_id 		= $_REQUEST["user_id"];
		$receiver_id 	= $_REQUEST["receiver_id"];
		$message 		= $_REQUEST["message"];
		$image_url 		= $_REQUEST["image_url"];
		$video_url 		= $_REQUEST["video_url"];
		$created_on 	= date("Y-m-d H:i:s");
		$sql = "INSERT INTO message_tbl(sender_id, rec_id, message, image_url, video_url, created_on) VALUES ('$user_id','$receiver_id','$message','$image_url','$video_url','$created_on')";
		$result = mysqli_query($link,$sql);
		if(!$result):
			echo json_encode(array("result"=>false));
		else:
			$last_id 	= mysqli_insert_id($link);
			//echo $last_id; die();
			$result  	= mysqli_query($link,"SELECT * FROM message_tbl WHERE id=".$last_id);
			$Message 	= mysqli_fetch_assoc($result); 
			//var_dump($Message);
			echo json_encode(array("result"=>true,"msg"=>"Message sent successfully.","data"=>$Message));
		endif;
	break;

	case 'get_messages':
		$user_id 		= $_REQUEST["user_id"];
		$otheruser_id 	= $_REQUEST["otheruser_id"];
		$numofrows  = $_REQUEST["numofrows"];
		$row = array();
		$sql = "SELECT users.ID,user_nicename,message,message_tbl.image_url,video_url FROM message_tbl INNER JOIN users ON users.ID = message_tbl.rec_id WHERE sender_id=? AND rec_id=? LIMIT $numofrows";
		$stmt = $link->stmt_init();
		$stmt = $link->prepare($sql);
		if($stmt){
			/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
			$stmt->bind_param('ii', $user_id,$otheruser_id);
 	
			$stmt->execute();
			$stmt->bind_result($ID,$user_nicename,$message,$image_url,$video_url);
			while ($stmt->fetch()) {
				array_push($row, array("user_id"=>$ID,"username"=>$user_nicename,"message_text"=>$message,"image_url"=>$image_url,"video_url"=>$video_url));
			}
			echo json_encode(array('result' => true,'data' => $row));
		}else{
			echo json_encode(array('result' => false));
		}
	break;

	case 'send_group_message':
		$user_id 		= $_REQUEST["user_id"];
		$group_id 		= $_REQUEST["group_id"];
		$message 		= $_REQUEST["message"];
		$image_url 		= $_REQUEST["image_url"];
		$video_url 		= $_REQUEST["video_url"];
		$created_on 	= date("Y-m-d H:i:s");
		$sql = "INSERT INTO group_message_tbl(idgroup, iduser, message, image_url, video_url, created_on) VALUES ('$group_id','$user_id','$message','$image_url','$video_url','$created_on')";
		$result = mysqli_query($link,$sql);
		if(!$result):
			echo json_encode(array("result"=>false));
		else:
			$last_id 	= mysqli_insert_id($link);
			//echo $last_id; die();
			$result  	= mysqli_query($link,"SELECT * FROM group_message_tbl WHERE idgroupmessage=".$last_id);
			$Message 	= mysqli_fetch_assoc($result); 
			//var_dump($Message);
			echo json_encode(array("result"=>true,"msg"=>"Message sent successfully.","data"=>$Message));
		endif;
	break;

	case 'get_group_messages':
		$group_id 		= $_REQUEST["group_id"];
		$numofrows  = $_REQUEST["numofrows"];
		$row = array();
		$sql = "SELECT users.ID,user_nicename,message,group_message_tbl.image_url,video_url FROM group_message_tbl INNER JOIN users ON users.ID = group_message_tbl.iduser WHERE idgroup=? LIMIT $numofrows";
		$stmt = $link->stmt_init();
		$stmt = $link->prepare($sql);
		if($stmt){
			/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
			$stmt->bind_param('i', $group_id);
 	
			$stmt->execute();
			$stmt->bind_result($ID,$user_nicename,$message,$image_url,$video_url);
			while ($stmt->fetch()) {
				array_push($row, array("user_id"=>$ID,"username"=>$user_nicename,"message_text"=>$message,"image_url"=>$image_url,"video_url"=>$video_url));
			}
			echo json_encode(array('result' => true,'data' => $row));
		}else{
			echo json_encode(array('result' => false));
		}
	break;

	case 'add_group':
		$group_name 	= $_REQUEST["group_name"];
		$status   		= 1;
		$sql = "INSERT INTO group_tbl(name,status) VALUES ('$group_name','$status')";
		$result = mysqli_query($link,$sql);
		if(!$result):
			echo json_encode(array("result"=>false));
		else:
			$last_id = mysqli_insert_id($link);
			$result  = mysqli_query($link,"SELECT * FROM group_tbl WHERE idgroup=".$last_id);
			$User 	 = mysqli_fetch_assoc($result); 
			echo json_encode(array("result"=>true,"msg"=>"Group added successfully","data"=>$User));
		endif;
	break;

	case 'get_group_list':
		$user_id 		= $_REQUEST["user_id"];
		$numofrows 		= $_REQUEST["numofrows"];
		$row = array();
		$sql = "SELECT idgroup, name,iduser  FROM group_user_tbl INNER JOIN group_tbl USING(idgroup) WHERE iduser=$user_id LIMIT $numofrows";
		$result = mysqli_query($link,$sql);
		if($result){
			$i = 0;
			while($row = mysqli_fetch_assoc($result)){
				$data[$i]   = $row;
				$i++;
			}
			echo json_encode(array('result' => true,'data' => $data));
		}else{
			echo json_encode(array('result' => false));
		}
	break;

	case 'add_skills':
		$skill_name 	= $_REQUEST["skill_name"];
		$sql = "INSERT INTO skills_tbl(skillname) VALUES ('$skill_name')";
		$result = mysqli_query($link,$sql);
		if(!$result):
			echo json_encode(array("result"=>false));
		else:
			$last_id = mysqli_insert_id($link);
			$result  = mysqli_query($link,"SELECT * FROM skills_tbl WHERE idskill=".$last_id);
			$User 	 = mysqli_fetch_assoc($result); 
			echo json_encode(array("result"=>true,"msg"=>"Skill added successfully","data"=>$User));
		endif;
	break;

	case 'add_user_skill':
		$user_id 	= $_REQUEST["user_id"];
		$idskill	= $_REQUEST["idskill"];
		$sql = "INSERT INTO user_skill(idskill,iduser) VALUES ('$idskill','$user_id')";
		$result = mysqli_query($link,$sql);
		if(!$result):
			echo json_encode(array("result"=>false));
		else:
			$last_id = mysqli_insert_id($link);
			$result  = mysqli_query($link,"SELECT * FROM user_skill WHERE iduserskill=".$last_id);
			$User 	 = mysqli_fetch_assoc($result); 
			echo json_encode(array("result"=>true,"msg"=>"User Skill added successfully","data"=>$User));
		endif;
	break;

	case 'get_user_skills':
		$user_id 		= $_REQUEST["user_id"];
		$numofrows 		= $_REQUEST["numofrows"];
		$row = array();
		$sql = "SELECT users.ID, user_nicename,idskill, skillname FROM user_skill INNER JOIN skills_tbl USING(idskill) INNER JOIN users ON users.ID = user_skill.iduser WHERE iduser=$user_id LIMIT $numofrows";
		$result = mysqli_query($link,$sql);
		if($result){
			$i = 0;
			while($row = mysqli_fetch_assoc($result)){
				$data[$i]   = $row;
				$i++;
			}
			echo json_encode(array('result' => true,'data' => $data));
		}else{
			echo json_encode(array('result' => false));
		}
	break;

	case 'get_winners':
		$user_id 		= $_REQUEST["user_id"];
		$row = array();
		$sql = "SELECT users.ID, user_nicename, user_image_url, like_count  FROM contest_tbl INNER JOIN users ON users.ID = contest_tbl.user_id ORDER BY like_count DESC LIMIT 3";
		$result = mysqli_query($link,$sql);
		if($result){
			$i = 0;
			while($row = mysqli_fetch_assoc($result)){
				$data[$i]   = $row;
				$i++;
			}
			echo json_encode(array('result' => true,'data' => $data));
		}else{
			echo json_encode(array('result' => false));
		}
	break;

	case 'social_registration':
		$name 		  = $_REQUEST["name"];
		$image_url    = $_REQUEST["image_url"];
		$phone_number = $_REQUEST["phone_number"];
		$gender       = $_REQUEST["gender"];
		$email 		  = $_REQUEST["email"];
		$status 	  = 0;
		$password 	  = md5($_REQUEST["password"]);
		$isSocialReg  = $_REQUEST["isSocialReg"];
		$socialid	  = $_REQUEST["socialid"];

		$result = mysqli_query($link,"INSERT INTO users(user_login, user_pass, user_nicename, phone, gender, user_email, user_status, display_name, image_url, isfacebooklogin, facebookid) 
											VALUES ('$email','$password','$name','$phone_number','$gender','$email','$status','$name','$image_url','$isSocialReg','$socialid')");
		if(!$result):
			echo json_encode(array("result"=>false));
		else:
			$last_id = mysqli_insert_id($link);
			//echo $last_id;
			$sql = "SELECT * FROM users WHERE ID=".$last_id;
			//echo $sql; die;
			$result  = mysqli_query($link,$sql);
			$User 	 = mysqli_fetch_assoc($result); 
			echo json_encode(array("result"=>true,"msg"=>"User registered successfully.","data"=>$User));
		endif;
	break;

	case 'get_user_facebookid':
		$row = array();
		$socialid	  = $_REQUEST["socialid"];
		$sql = "SELECT ID,user_login,image_url,phone,gender,user_email FROM users WHERE facebookid=?";
		//echo $sql; die();
		$stmt = $link->stmt_init();
		$stmt = $link->prepare($sql);
		if($stmt){
			/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
			$stmt->bind_param('i', $socialid);
 	
			$stmt->execute();
			$stmt->bind_result($user_id,$username,$image_url,$phone,$gender,$user_email);
			while ($stmt->fetch()) {
				array_push($row, array("user_id"=>$user_id,"username"=>$username,"image_url"=>$image_url,"phone"=>$phone,"gender"=>$gender,"user_email"=>$user_email));
			}
			if(count($row)>0)
				echo json_encode(array('result' => true,'data' => $row));
			else
				echo json_encode(array('result' => false));	
		}else{
			echo json_encode(array('result' => false));
		}
	break;

	case 'get_friends':
		$user_id = $_REQUEST["user_id"];
		$numofrows = $_REQUEST["numofrows"];
		$row = array();
		$sql = "SELECT users.ID, user_nicename, phone, gender, user_email FROM friends_tbl INNER JOIN users ON users.ID = friends_tbl.sender_id WHERE sender_id =? AND accepted =1 LIMIT $numofrows";
		$stmt = $link->stmt_init();
		$stmt = $link->prepare($sql);
		if($stmt){
			$stmt->bind_param('i', $user_id);
 	
			$stmt->execute();
			$stmt->bind_result($ID,$user_nicename,$phone,$gender,$user_email);
			while ($stmt->fetch()) {
				array_push($row, array("user_id"=>$ID,"username"=>$user_nicename,"phone"=>$phone,"gender"=>$gender,"user_email"=>$user_email));
			}
			echo json_encode(array('result' => true,'data' => $row));
		}else{
			echo json_encode(array('result' => false));
		}
		break;
	
	case 'forgot_password':
		$user_id = $_REQUEST["user_id"];
		$result  = mysqli_query($link,"SELECT * FROM users WHERE ID=".$user_id);
		if($result):
			$User 	 = mysqli_fetch_assoc($result); 
        	$encrypt = md5(1290*3+$user_id);
        	$sql = "UPDATE users SET keytoken='$encrypt' WHERE ID=".$user_id;
        	mysqli_query($link,$sql);
        	$message = "Your password reset link send to your e-mail address.";
        	$to=$email;
        	$subject="Reset Password";
        	$from = 'info@cre8links.com';
        	$body ='Hi, <br/> <br/>You ask for a password reset. <br><br>Click here to reset your password http://cre8links.com/path/reset.php?encrypt='.$encrypt.'&action=reset   <br/> <br/>--<br>Cre8 Links.';
        	$headers = "From: " . strip_tags($from) . "\r\n";
        	$headers .= "Reply-To: ". strip_tags($from) . "\r\n";
        	$headers .= "MIME-Version: 1.0\r\n";
        	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
 			$to 	=  $User["user_email"];
        	mail($to,$subject,$body,$headers);
        	echo json_encode(array("result"=>true,"msg"=>"Email sended successfully!"));
        else:
        	echo json_encode(array("result"=>false,"msg"=>"Error"));
        endif;
	break;	

	case 'generate_password':
		$user_id = $_REQUEST["user_id"];
		$keytoken= $_REQUEST["keytoken"];
		$result  = mysqli_query($link,"SELECT * FROM users WHERE ID=".$user_id." AND keytoken='".$keytoken."'");
		if($result):
			$User 	 = mysqli_fetch_assoc($result);
			$newpassword = sha1(microtime(true).mt_rand(10000,90000));
        	$message = "New Password.";
        	$to=$email;
        	$subject="Generate Password";
        	$from = 'info@cre8links.com';
        	$body='Hi, <br/> <br/>You new password has beed generated.<br><br>New password: '.$newpassword.'<br/> <br/>--<br>Cre8 Links.';
        	$headers = "From: " . strip_tags($from) . "\r\n";
        	$headers .= "Reply-To: ". strip_tags($from) . "\r\n";
        	$headers .= "MIME-Version: 1.0\r\n";
        	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
 			$to 	=  $User["user_email"];
        	mail($to,$subject,$body,$headers);
        	echo json_encode(array("result"=>true,"msg"=>"Your password has been reseted succesfully"));
        else:
        	echo json_encode(array("result"=>false,"msg"=>"Error"));
        endif;
		break;

	default:
		echo "Web Service.";
		break;
}

?>