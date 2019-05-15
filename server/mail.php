<?php
include 'conn.php';//登入

$mod_sql=$_POST['mod_p'];
$mod_tab=$_POST['mod_tab'];
$sqlt="";

switch ($mod_tab) {
	case "show_list":
        show_list();
		break;
	case "user_list":
        user_list();
        break;
	case "user_add":
        user_add();
        break;
	case "user_chg":
        user_chg();
        break;
	case "user_del":
        user_del();
        break;
}

function show_list(){
	global $conn;
	$json = array();
	if(empty($_POST['id'])){
		$sqlt="SELECT * FROM `mail_msg` ORDER BY `id` DESC";
	}else{
		$sqlt="SELECT * FROM `mail_msg` WHERE `id` = '".$_POST['id']."'";
	}
	
	$result = $conn->query($sqlt);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			//echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
			array_push($json, $row); 
		}
	} else {
		echo "0 results";
	}
	echo json_encode( $json );
}

function user_list(){
	global $conn;
	$json = array();
	if(empty($_POST['id'])){
		$sqlt="SELECT * FROM `mail_user`";
	}else{
		$sqlt="SELECT * FROM `mail_user` WHERE `id` = '".$_POST['id']."'";
	}
	$result = $conn->query($sqlt);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			//echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
			array_push($json, $row); 
		}
	} else {
		echo "0 results";
	}
	echo json_encode( $json );
}

function user_add(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d H:i:s");
	$t1=$_POST['t1']; //name
	$t2=$_POST['t2']; //email
	if (filter_var($t2, FILTER_VALIDATE_EMAIL)) {
		//echo "This ($t3) email address is considered valid.\n";
	}else{
		die("Email格式錯誤!!");
	}
	$sqlt="INSERT INTO `mail_user`( `name`, `mail`, `time`) VALUES ('$t1','$t2','$tt')";
	if ($conn->query($sqlt) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function user_chg(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d H:i:s");
	$id=$_POST['id']; //id
	if(empty($id)){
		die('No id');
	}
	$t1=$_POST['t1']; //name
	$t2=$_POST['t2']; //email
	$sqlt="UPDATE `mail_user` SET `name`='$t1',`mail`='$t2' WHERE `id`='$id'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function user_del(){
	global $conn;
	$id=$_POST['id']; //id
	if(empty($id)){
		die('No id');
	}
	$sqlt = "DELETE FROM `mail_user` WHERE `id`= '$id'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record deleted successfully";
	} else {
		echo "Error deleting record: " . $conn->error;
	}
}

?>