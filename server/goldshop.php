<?php
include 'conn.php';//登入
session_start();
$mod_tab ="";
if(!empty($_POST['mod_tab'])){
	$mod_tab=$_POST['mod_tab'];
}
$sqlt="";

//$mod_tab='add';
switch ($mod_tab) {
	case "get_prod":
       get_prod();
        break;
    case "get_prod_all":
       get_prod_all();
        break;
    case "get_prod_one":
       get_prod_one();
        break;
	case "add_prod":
        add_prod();
        break;
	case "chg_prod":
        chg_prod();
        break;
    case "del_prod":
        del_prod();
        break;
    case "hide_prod":
        hide_prod();
        break;
	case "test":
        test();
        break;
    default:
        echo "test";
		//show_kind();
		break;
}

function test(){
	global $conn;
	
}

function get_prod(){
	global $conn;
	$json = array();
	
	// $sqlt=  "SELECT * FROM `goldshop` WHERE `g_id` ='$gid' && `status` = '1'";
	$id = "";
	$wh = "";
	if(!empty($_POST['id'])){
		$id=$_POST['id'];
		$wh ="AND `id` = '$id'";
	}
	$sqlt=  "SELECT 
			`goldshop`.`id`,
			`goldshop`.`g_id`,
			`goldshop`.`kind`,
			`goldshop`.`title`,
			`goldshop`.`company`,
			`goldshop`.`model`,
			`goldshop`.`years`,
			`goldshop`.`money`,
			`goldshop`.`moneyo`,
			`goldshop`.`pics`,
			`goldshop`.`html`,
			`goldshop`.`time`,
			`goldshop`.`status`
			FROM `goldshop` 
			WHERE`goldshop`.`status` = '1' $wh
			ORDER BY `id` DESC";
	
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

function get_prod_all(){
	global $conn;
	$json = array();
	$kind ="";
	$need ="";
	if(!empty($_POST['need'])){
		$need = 'ORDER BY `goldshop`.`id` DESC LIMIT 5';
	}
	if(!empty($_POST['kind'])){
		$kind = " AND `kind` = '".$_POST['kind']."'";
	}
	$sqlt=  "SELECT `goldshop`.`id`,`goldshop`.`g_id`,`goldshop`.`kind`,`goldshop`.`title`,
			`goldshop`.`company`,`goldshop`.`model`,`goldshop`.`years`,`goldshop`.`money`,`goldshop`.`moneyo`,
			`goldshop`.`pics`,`goldshop`.`html`,`goldshop`.`time`,`goldshop`.`status`,
			`account_gold`.`user_name`,`account_gold`.`phone`
			FROM `goldshop` 
			RIGHT JOIN `account_gold` ON `goldshop`.`g_id` = `account_gold`.`id`
			WHERE `goldshop`.`status` = '1' $kind 
			$need";
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

function get_prod_one(){
	if(!empty($_POST["sid"])){
		$sid = $_POST["sid"];
	}else{
		die('No sid');
	}
	global $conn;
	$json = array();
	$sqlt=  "SELECT `goldshop`.`id`,`goldshop`.`g_id`,`goldshop`.`kind`,`goldshop`.`title`,
			`goldshop`.`company`,`goldshop`.`model`,`goldshop`.`years`,`goldshop`.`money`,`goldshop`.`moneyo`,
			`goldshop`.`pics`,`goldshop`.`html`,`goldshop`.`time`,`goldshop`.`status`,
			`account_gold`.`user_name`,`account_gold`.`phone`
			FROM `goldshop` 
			RIGHT JOIN `account_gold` ON `goldshop`.`g_id` = `account_gold`.`id`
			WHERE `goldshop`.`id` = '$sid'";
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

function add_prod(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d H:i:s");
	$t0=$_POST['t0']; //類別
	$t1=$_POST['t1']; //品名
	$t2=$_POST['t2']; //廠牌
	$t3=$_POST['t3']; //型號
	$t4=$_POST['t4']; //年分
	$t5=$_POST['t5']; //標價
	$t5o=$_POST['t5o']; //標價
	$t6=$_POST['t6']; //圖片
	$t7=$_POST['t7']; //描述
	$pic="null"; //pic
	if(!empty($_SESSION["id-g"])){
		$gid = $_SESSION["id-g"];
	}else{
		//die('No gid');
		$gid = '1';
	}
	$sqlt="INSERT INTO `goldshop` 
	(`g_id`, `kind`, `title`, `company`, `model`, `years`, `money`, `moneyo`, `pics`, `html`,`status`) VALUES 
	( '$gid', '$t0', '$t1', '$t2', '$t3', '$t4', '$t5', '$t5o', '$t6', '$t7','1');";
	if ($conn->query($sqlt) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function chg_prod(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d H:i:s");
	$sid=$_POST['sid']; //id
	$t0=$_POST['t0']; //類別
	$t1=$_POST['t1']; //品名
	$t2=$_POST['t2']; //廠牌
	$t3=$_POST['t3']; //型號
	$t4=$_POST['t4']; //年分
	$t5=$_POST['t5']; //標價
	$t5o=$_POST['t5o']; //標價o
	$t6=$_POST['t6']; //圖片
	$t7=$_POST['t7']; //描述
	$sqlt="UPDATE `goldshop` SET 
	`kind`   ='$t0',
	`title`  ='$t1',
	`company`='$t2',
	`model`  ='$t3',
	`years`  ='$t4',
	`money`  ='$t5',
	`moneyo` ='$t5o',
	`pics`   ='$t6',
	`html`   ='$t7',
	`time`   ='$tt',
	`status` ='1' 
	WHERE `id` = '$sid'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function hide_prod(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d H:i:s");
	$sid=$_POST['sid']; //id
	$sqlt="UPDATE `goldshop` SET 
	`status` ='0' 
	WHERE `id` = '$sid'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function del_prod(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d H:i:s");
	if(empty($_POST['sid'])){
		die("No id");
	}
	$sid=$_POST['sid']; //id

	$sqlt="DELETE FROM `goldshop` WHERE `id` = '$sid'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record deleted successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

?>