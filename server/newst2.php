<?php
include 'conn.php';//登入
$mod_tab=$_POST['mod_tab'];
$sqlt="";
//$mod_tab='add';
switch ($mod_tab) {
    case "add":
        add();
        break;
	case "show_list":
        show_list();
        break;
	case "chg":
        chg();
        break;
	case "del":
        del();
        break;
	case "clickurl":
        clickurl();
        break;
}

function add(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$t1=$_POST['t1']; //title
	$t2=$_POST['t2']; //simp
	//$t3=$_POST['pic']; //simp
	$tt=date("Y-m-d H:i:s");
	$sqlt="INSERT INTO `newst2`( `title`,`simp`,`pic`,`time`) 
						VALUES ('$t1','$t2','','$tt')";
	
	if ($conn->query($sqlt) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}


function show_list(){
	global $conn;
	$json = array();
	$sqlt="SELECT * FROM `newst2` ORDER BY `id` DESC LIMIT 4";
	$result = $conn->query($sqlt);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($json, $row); 
		}
	} else {
		echo "0 results";
	}
	echo json_encode( $json );
}

function chg(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d H:i:s");
	$id=$_POST['id']; //id
	if(empty($id)){
		die('No id');
	}
	$t1=$_POST['t1']; //title
	$t2=$_POST['t2']; //simp
	//$pic=$_POST['pic']; //pic
	$sqlt="UPDATE `newst2` 
		SET `title`='$t1',`simp`='$t2',`pic`='',`time`='$tt' 
		WHERE `id`='$id'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
	
}

function del(){
	global $conn;
	$id=$_POST['id']; //id
	if(empty($id)){
		die('No id');
	}
	$sqlt = "DELETE FROM `newst2` WHERE `id`= '$id'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record deleted successfully";
	} else {
		echo "Error deleting record: " . $conn->error;
	}
}

function clickurl(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$id=$_POST['id']; //id
	if(empty($id)){
		die('No id');
	}

	$sqlt="UPDATE `newst2` SET `click`=`click`+1 WHERE `id` = '$id'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

?>