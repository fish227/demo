<?php
include 'conn.php';//登入

$mod_sql=$_POST['mod_p'];
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
    default:
        echo "test";
}

function add(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$ktime=date("Y-m-d H:i:s");
	$t1=$_POST['t1']; //q
	$t2=$_POST['t2']; //a
	$sqlt="INSERT INTO `qa`(`q`,`a`,`time`) VALUES ('$t1','$t2','$ktime')";
	if ($conn->query($sqlt) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function show_list(){
	global $conn;
	$json = array();
	if(empty($_POST['id'])){
		$sqlt="SELECT * FROM `qa`";
	}else{
		$sqlt="SELECT * FROM `qa WHERE `id` = '".$_POST['id']."'";
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

function chg(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$prod_time=date("Y-m-d H:i:s");
	$id=$_POST['id']; //id
	if(empty($id)){
		die('No id');
	}
	$t1=$_POST['t1']; //q
	$t2=$_POST['t2']; //a
	$sqlt="UPDATE `qa` 
	SET `q`='$t1' ,
		`a`='$t2'
	WHERE `id` = '$id'";
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
	$sqlt = "DELETE FROM `qa` WHERE `id`= '$id'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record deleted successfully";
	} else {
		echo "Error deleting record: " . $conn->error;
	}
}

?>