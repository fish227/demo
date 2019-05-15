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
	case "get_prod":
       get_prod();
        break;
	case "add_prod":
        add_prod();
        break;
	case "chg_prod":
        chg_prod();
        break;
	case "del":
        del_sql();
        break;
	case "test":
        test();
        break;
	case "prod_table":
        prod_table();
        break;
	case "chgdetail":
        chgdetail();
        break;
	case "chgdetailq":
        chgdetailq();
        break;
	case "prodlog":
        prodlog();
        break;
	case "prodview":
        prodview();
        break;
	case "prodviewsearch":
        prodviewsearch();
        break;
    default:
        echo "test";
		show_kind();
}

function test(){
	global $conn;
	
	$dog = (array)json_decode($_POST['dog'],true);
	$name = $_POST['name'];
	$sqlt="SELECT * FROM `shopprod` WHERE `prod_name` = '$name'";
	//echo $sqlt;
	$result = $conn->query($sqlt);
	if ($result->num_rows == 1) {
		while($row = $result->fetch_assoc()) {
			$m_id=$row['prod_id'];
		}
	} else {
		die('無產品主檔案 或發生錯誤');
	}
	$insertv = '';
	foreach ($dog as $value) {
		$t1=$value['t1'];
		$t2=$value['t2'];
		$t3=$value['t3'];
		$t4=$value['t4'];
		$t5=$value['t5'];
		$t6=$value['t6'];
		$img=$value['img'];
		$insertv .="('$m_id','$t1','$t2','$t3','$t4','$t5','$t6','$img'),";
		//print_r($value);
	}
	$insertv = substr($insertv,0,strlen($insertv)-1);
	$sqlt="INSERT INTO `prod_s`(`prodmind`, `kindname`, `color`, `size_s`, `size_m`, `size_l`, `size_f`, `picsrc`) VALUES ";
	$sqlt .=$insertv;
	//echo $sqlt;
	if ($conn->query($sqlt) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function chgdetail(){
	global $conn;
	$dog = (array)json_decode($_POST['dog'],true);
	$insertv = '';
	foreach ($dog as $value) {
		$id=$value['id'];//id
		$t1=$value['t1'];//name
		$t2=$value['t2'];//color
		$t3=$value['t3'];//s
		$t4=$value['t4'];//m
		$t5=$value['t5'];//l
		$t6=$value['t6'];//f
		$t7=$value['switchprod'];//switch
		$img=$value['img'];
		//$insertv .="('$m_id','$t1','$t2','$t3','$t4','$t5','$t6','$img'),";
		/*$insertv ="UPDATE `prod_s` SET 
		`kindname`='$t1',`color`='$t2',
		`size_s`='$t3',`size_m`='$t4',`size_l`='$t5',`size_f`='$t6',`picsrc`='$img' WHERE `id` = '$id'";*/
		if(empty($id)){
			
		}else{
		$insertv ="UPDATE `prod_s` SET 
		`kindname`='$t1',`color`='$t2',`picsrc`='$img' ,`switchprod`='$t7'
		WHERE `id` = '$id'";
		}
		if ($conn->query($insertv) === TRUE) {
		//echo "New record created successfully";
		} else {
			echo $insertv;
			die('修改錯誤');
			echo "Error: " . $insertv . "<br>" . $conn->error;
		}
	}
	echo "New record created successfully";
}

function chgdetailq(){
	global $conn;
	$dog = (array)json_decode($_POST['dog'],true);
	$insertv = '';
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d H:i:s");
	foreach ($dog as $value) {
		$t1=$value['id'];//id
		$t2=$value['status'];//mod
		$t3=$value['t3'];//q
		$insertv .="('$t1', '$t2', '$t3', '$tt'),";
		//print_r($value);
	}
	$insertv = substr($insertv,0,strlen($insertv)-1);
	$sqlt="INSERT INTO `prod_log`(`prod_s_id`, `status`, `log_q`, `time`) VALUES ";
	$sqlt .=$insertv;
	//echo $sqlt;
	if ($conn->query($sqlt) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function prodlog(){
	global $conn;
	$id = $_POST['id'];
	if(empty($id)){
		die('error: NO id');
	}
	$json = array();
	$sqlt="SELECT '起初設定' AS 'mod',`shopprod`.`prod_q`,
	`shopprod`.`prod_time`
	FROM `shopprod`
	UNION 
	SELECT `prod_log`.`status`,`prod_log`.`log_q`,
	`prod_log`.`time`
	FROM `prod_log`
	WHERE `prod_log`.`prod_s_id` = $id";
	
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

function show_list(){
	global $conn;
	$jsonm = array();//主類別
	$jsons = array();//次類別
	$jsona = array();//最後輸出JSON
	$age = array();//輔最後輸出JSON
	$sqltm="SELECT * FROM `prod_kind_m`";
	$sqlts="SELECT * FROM `prod_kind_s`";
	$resultm = $conn->query($sqltm);
	if ($resultm->num_rows > 0) {
		while($row = $resultm->fetch_assoc()) {
			array_push($jsonm, $row); 
		}
	} else {
		echo "0 results";
	}
	$results = $conn->query($sqlts);
	if ($results->num_rows > 0) {
		while($row = $results->fetch_assoc()) {
			array_push($jsons, $row); 
		}
	} else {
		die('Error:results');
	}
	foreach ($jsonm as $valuem) {
		$age = array("id"=>$valuem['kind_name'], "kind_name"=>$valuem['kind_name'], "sec_name"=>"");
		array_push($jsona, $age); 
		$kname=$valuem['kind_name'];
		$someid=$valuem['id'];
		foreach ($jsons as $values) {
			if($someid == $values['sec_kindid']){
				$age = array("id"=>$values['id'], "kind_name"=>$kname, "sec_name"=>$values['sec_name']);
				array_push($jsona, $age); 
			}
		}
	}
	echo json_encode( $jsona );
	/*
	$json = array();
	if(empty($_POST['id'])){
		$sqlt="SELECT prod_kind_s.id, prod_kind_m.kind_name, prod_kind_s.sec_name ,prod_kind_s.sec_kindid
			   FROM prod_kind_s , prod_kind_m
			   WHERE prod_kind_s.sec_kindid = prod_kind_m.id";
	}else{
		$sqlt="SELECT * FROM `prod_kind_s WHERE `id` = '".$_POST['id']."'";
	}
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			//echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
			array_push($json, $row); 
		}
	} else {
		echo "0 results";
	}
	*/
	
}

function get_prod(){
	global $conn;
	$json = array();
	$id = $_POST['id'];
	if(!empty($id)){
		$sqlt="SELECT `prod_id`, `prod_name`, `prod_p`, `prod_html`, `prod_pic`,`con`,
			`prod_type`, `prod_status`, `prod_time`,
			IFNULL(`shopprod`.`prod_q`+SUM(`prod_log`.`log_q`),`shopprod`.`prod_q`) AS 'prod_q'
			FROM `shopprod`
			LEFT JOIN `prod_log` ON `shopprod`.`prod_id`=`prod_log`.`prod_s_id`
			WHERE `prod_id` = '$id'
			GROUP BY `shopprod`.`prod_id`";
	}else{
		$sqlt="SELECT `prod_id`, `prod_name`, `prod_p`, `prod_html`, `prod_pic`,`con`,
			`prod_type`, `prod_status`, `prod_time`,
			IFNULL(`shopprod`.`prod_q`+SUM(`prod_log`.`log_q`),`shopprod`.`prod_q`) AS 'prod_q'
			FROM `shopprod`
			LEFT JOIN `prod_log` ON `shopprod`.`prod_id`=`prod_log`.`prod_s_id`
			GROUP BY `shopprod`.`prod_id`";
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

function add_prod(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d H:i:s");
	$t1=$_POST['t1']; //n
	$t2=$_POST['t2']; //p
	$t3=$_POST['t3']; //c
	$pic=$_POST['pic']; //html
	//id sec_kindid sec_kindsn sec_name sec_count
	$sqlt="SELECT * FROM `shopprod` WHERE `prod_name` = '$t1'";
	$result = $conn->query($sqlt);
	if ($result->num_rows > 0) {
		die('產品有相同名稱');
	} else {
		//echo "0 results";
	}
	
	$sqlt="INSERT INTO `shopprod` (`prod_name`, `prod_p`,`con`, `prod_pic`, `prod_status`, `prod_time`)
						VALUES('$t1','$t2','$t3','$pic','1','$tt')";
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
	$prod_id=$_POST['prod_id']; //id
	$pic=$_POST['pic']; //html
	$t1=$_POST['t1']; //n
	$t2=$_POST['t2']; //p
	$t3=$_POST['t3']; //c
	$t4=$_POST['t4']; //s
	//id sec_kindid sec_kindsn sec_name sec_count
	$t2 = str_replace("server/", "", $t2);
	$sqlt="UPDATE `shopprod` SET `prod_name`='$t1',`prod_p`='$t2',`prod_pic`='$pic',
							`prod_status` = '$t4',`con` = '$t3',
							`prod_time`='$tt' WHERE `prod_id` = '$prod_id'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function del_sql(){
	global $conn;
	$id=$_POST['id'];
	$sqlt = "DELETE FROM `prod_kind_s` WHERE `id`=$id";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record deleted successfully";
	} else {
		echo "Error deleting record: " . $conn->error;
	}
}

function prod_table(){
	global $conn;
	$json = array();
	$sql_id=$_POST['id'];
	if(empty($_POST['id'])){
		$sqlt="SELECT `prod_id`, `prod_name`, `prod_p`, `prod_pic`, `prod_type`,`prod_kind_s`.`sec_kindid`, `prod_kind_s`.`sec_name` 
		FROM `shopprod`, `prod_kind_s`
		WHERE `prod_type`=`prod_kind_s`.`id`";
	}else{
		$sqlt="SELECT `prod_id`, `prod_name`, `prod_p`, `prod_pic`, `prod_type`,`prod_kind_s`.`sec_kindid`, `prod_kind_s`.`sec_name` , `prod_html`
		FROM `shopprod`, `prod_kind_s`
		WHERE `prod_type`=`prod_kind_s`.`id` && `prod_id` = $sql_id";
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

function prodview(){
	global $conn;
	$json = array();
	$sqlt="SELECT `prod_id`, `prod_name`, `prod_p`, `prod_q`, `prod_html`, `prod_pic`, 
		`prod_type`, `prod_status`, `prod_time` ,`prod_s`.`picsrc`
		FROM `shopprod` , `prod_s`
		WHERE `shopprod`.`prod_id` = `prod_s`.`prodmind` && `shopprod`.`prod_status` != 0
		GROUP BY `shopprod`.`prod_id`";
	
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

function prodviewsearch(){
	global $conn;
	$json = array();
	$key = $_POST['key'];
	$sqlt="SELECT `prod_id`, `prod_name`, `prod_p`, `prod_q`, `prod_html`, `prod_pic`, 
		`prod_type`, `prod_status`, `prod_time` ,`prod_s`.`picsrc`
		FROM `shopprod` , `prod_s`
		WHERE `shopprod`.`prod_id` = `prod_s`.`prodmind` && `shopprod`.`prod_name` LIKE '%$key%'
		GROUP BY `shopprod`.`prod_id`";
	
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


?>