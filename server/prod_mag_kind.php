<?php
include 'conn.php';//登入

$mod_sql=$_POST['mod_p'];
$mod_tab=$_POST['mod_tab'];
$sqlt="";

//$mod_tab='add';
switch ($mod_tab) {
    case "show_kind":
       show_kind();
        break;
	case "show_list":
       show_list();
        break;
    case "add":
        add();
        break;
	 case "del":
        del_sql();
        break;
    case "chg":
        chg();
        break;
	case "test":
        test();
        break;
	case "add_kind":
        add_kind();
        break;
	case "add_kind_m":
        add_kind_m();
        break;
	case "show_list_m":
        show_list_m();
        break;
	case "del_m":
        del_sql_m();
        break;
    case "chg_m":
        chg_m();
        break;
    default:
        echo "test";
		show_kind();
}

function test(){
	
	global $conn;
	$json = array();
	$prod_id=$_POST['id'];
	if(empty($_POST['id'])){
		$sqlt="SELECT `prod_id`, `prod_name`, `prod_p`, `prod_pic`, `prod_type`, `prod_html`,`prod_kind_s`.`sec_kindid`, `prod_kind_s`.`sec_name`,`prod_kind_m`.`kind_name`
				FROM `prod`, `prod_kind_s`,`prod_kind_m`
				WHERE `prod_type`=`prod_kind_s`.`id` && `prod_kind_m`.`id` = `prod_kind_s`.`sec_kindid`";
	}else{
		$sqlt="SELECT `prod_id`, `prod_name`, `prod_p`, `prod_pic`, `prod_type`, `prod_html`,`prod_kind_s`.`sec_kindid`, `prod_kind_s`.`sec_name`,`prod_kind_m`.`kind_name`
				FROM `prod`, `prod_kind_s`,`prod_kind_m`
				WHERE `prod_type`=`prod_kind_s`.`id` && `prod_kind_m`.`id` = `prod_kind_s`.`sec_kindid` && `prod_id`=$prod_id";
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

function show_list(){
	global $conn;
	$json = array();
	if(empty($_POST['id'])){
		$sqlt="SELECT prod_kind_s.id, prod_kind_m.kind_name, prod_kind_s.sec_name ,prod_kind_s.sec_kindid
			   FROM prod_kind_s , prod_kind_m
			   WHERE prod_kind_s.sec_kindid = prod_kind_m.id";
	}else{
		$sqlt="SELECT * FROM `prod_kind_s WHERE `id` = '".$_POST['id']."'";
	}
	$sqlt="SELECT * FROM `prod_kind_s`";
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

function show_kind(){
	global $conn;
	$json = array();
	if(empty($_POST['id'])){
		$sqlt="SELECT * FROM `prod_kind_m`";
	}else{
		$sqlt="SELECT * FROM `prod_kind_m` WHERE `id` = '".$_POST['id']."'";
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

function add_kind(){
	global $conn;
	$t1=$_POST['kind']; 
	$t2=$_POST['t1']; 
	//id sec_kindid sec_kindsn sec_name sec_count
	$sqlt="UPDATE `prod_kind_m` SET `kind_num` = `kind_num`+1 WHERE `id` = '$t2'";
	if ($conn->query($sqlt) === TRUE) {
		
	} else {
		die('新增失敗');
	}
	$sqlt="INSERT INTO `prod_kind_s` (`sec_kindid`, `sec_name`,`sec_count`)
						VALUES('$t1','$t2','0')";
	if ($conn->query($sqlt) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function del_sql(){
	global $conn;
	$id=$_POST['id'];
	$sqlt = "SELECT * FROM `prod` WHERE `prod_type` = '$id'";
	$result = $conn->query($sqlt);
	if ($result->num_rows > 0) {
		// output data of each row
		die('項目內還有產品');
	}
	
	$sqlt = "DELETE FROM `prod_kind_s` WHERE `id`=$id";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record deleted successfully";
	} else {
		echo "Error deleting record: " . $conn->error;
	}
}

function chg(){
	global $conn;
	$t1=$_POST['t1'];
	$t2=$_POST['t2'];
	$t3=$_POST['t3'];
	$sqlt = "UPDATE `prod_kind_s` SET `sec_kindid`='$t1',`sec_name`='$t2' WHERE id='$t3'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . $conn->error;
	}
}

function add_kind_m(){
	global $conn;
	$t1=$_POST['t1']; 
	//id sec_kindid sec_kindsn sec_name sec_count

	$sqlt="INSERT INTO `prod_kind_m` (`kind_name`,`kind_num`,`dprod_num`)
						VALUES('$t1','0','0')";
	if ($conn->query($sqlt) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function del_sql_m(){
	global $conn;
	$id=$_POST['id'];
	$sqlt = "SELECT * FROM `prod_kind_m` WHERE `id`=$id";
	$result = $conn->query($sqlt);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			if($row['kind_num'] !=='0' || $row['dprod_num'] !=='0'){
				die('類別內還有類別，清空類別後再嘗試');
			}
		}
	} else {
		echo "0 results";
	}
	
	$sqlt = "DELETE FROM `prod_kind_m` WHERE `id`=$id";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record deleted successfully";
	} else {
		echo "Error deleting record: " . $conn->error;
	}
}

function chg_m(){
	global $conn;
	$t1=$_POST['t1'];
	$t2=$_POST['t2'];
	$sqlt = "UPDATE `prod_kind_m` SET `kind_name`='$t1' WHERE id='$t2'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . $conn->error;
	}
}

function show_list_m(){
	global $conn;
	$json = array();
	if(empty($_POST['id'])){
		$sqlt="SELECT * FROM `prod_kind_m`";
	}else{
		$sqlt="SELECT * FROM `prod_kind_s WHERE `id` = '".$_POST['id']."'";
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

?>