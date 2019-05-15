<?php
include 'conn.php';//登入
$mod_tab=$_POST['mod_tab'];
$sqlt="";

//$mod_tab='add';
switch ($mod_tab) {
	case "get_prod":
       get_prod();
        break;
    case "get_count":
       get_count();
        break;
	case "add_prod":
        add_prod();
        break;
	case "chg_prod":
        chg_prod();
        break;
    case "chg_star":
        chg_star();
        break;
	case "del":
        del();
        break;
	case "test":
        test();
        break;
	
    default:
    	test();
    	break;
        echo "test";
}

function test(){
	global $conn;
	echo 5 % 3;
	/*
	$sqlt="SELECT COUNT(id) as 'cid' FROM `prod` WHERE `vip` != 'vip'";
	$result = $conn->query($sqlt)->fetch_assoc();
	print_r($result[cid]);*/
}


function get_prod(){
	global $conn;
	$json = array();
	$sqlt="SELECT COUNT(id) as 'cid' FROM `prod` WHERE `vip` != 'vip'";
	$result = $conn->query($sqlt)->fetch_assoc();
	$pagecount = $result[cid];
	// $_POST['need'] = 3;
	$pageitemq = 6;
	if(!empty($_POST['need'])){
		if($_POST['need'] == "vip"){
			$sqlt="SELECT * FROM `prod` WHERE `vip` = 'vip'";
		}
		if(is_numeric($_POST['need'])){
			$items = $_POST['need'] * $pageitemq;
			$limitpage = $pageitemq;
			if($items > $pagecount){
				$limitpage = $pagecount % $pageitemq;
			}
			$sqlt="SELECT * FROM prod
				INNER JOIN(SELECT * FROM `prod` WHERE `vip` != 'vip' ORDER BY `prod`.`id`  ASC LIMIT $items) AS table1
				ON prod.id = table1.id
				ORDER BY `prod`.`id`  DESC LIMIT $limitpage
				";
		}
	}else{
		$sqlt="SELECT * FROM `prod`";
	}
	$result = $conn->query($sqlt);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($json, $row); 
		}
	} else {
		echo "0 results";
	}
	array_multisort($json, SORT_ASC, $json);

	//print_r($json);
	//die();
	echo json_encode( $json );
}
function get_count(){
	global $conn;
	$json = array();
	$sqlt="SELECT COUNT(id) as 'cid' FROM `prod` WHERE `vip` != 'vip'";
	$result = $conn->query($sqlt);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($json, $row); 
		}
	} else {
	}
	echo json_encode( $json );
}

function cmp($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}

function add_prod(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$prod_time=date("Y-m-d H:i:s");
	$t1 =$_POST['t1'];  //name
	$t2 =$_POST['t2'];  //position
	$t3 =$_POST['t3'];  //line
	$t4 =$_POST['t4'];  //wechat
	$t5 =$_POST['t5'];  //a
	$t6 =$_POST['t6'];  //b
	$t7 =$_POST['t7'];  //c
	$t8 =$_POST['t8'];  //c
	$t9 =$_POST['t9'];  //c
	$t10=$_POST['t10']; //c
	$t11=$_POST['t11']; 
	$t12=$_POST['t12'];//c
	$t13=$_POST['t13'];//c
	$pic=$_POST['pic']; //pic
	//id sec_kindid sec_kindsn sec_name sec_count
	/*
	$sqlt="SELECT * FROM `prod` WHERE `prod_name` = '$t3'";
	$result = $conn->query($sqlt);
	if ($result->num_rows > 0) {
		die('產品有相同名稱');
	} else {
		//echo "0 results";
	}*/
	$sqlt="INSERT INTO `prod`(`name`, `position`, `line`, `wechat`, `body_a`, `body_b`, `body_c`, `con`, `star`, `starq`, `ontime`, `clotime`, `light`, `pic` ) VALUES 
							('$t1','$t2','$t3','$t4','$t5','$t6','$t7','$t8','$t9','$t10','$t11','$t12','$t13','$pic')";
	if ($conn->query($sqlt) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function chg_prod(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$prod_time=date("Y-m-d H:i:s");
	$id =$_POST['id'];   //name
	$t1 =$_POST['t1'];   //name
	$t2 =$_POST['t2'];   //position
	$t3 =$_POST['t3'];   //line
	$t4 =$_POST['t4'];   //wechat
	$t5 =$_POST['t5'];   //a
	$t6 =$_POST['t6'];   //b
	$t7 =$_POST['t7'];   //c
	$t8 =$_POST['t8'];   //c
	$t9 =$_POST['t9'];   //c
	$t10=$_POST['t10'];  //c
	$t11=$_POST['t11']; 
	$t12=$_POST['t12'];
	$t13=$_POST['t13']; //c
	$pic=$_POST['pic'];  //pic

	$sqlt="UPDATE `prod` SET 
	`name`     ='$t1',
	`position` ='$t2',
	`line`     ='$t3',
	`wechat`   ='$t4',
	`body_a`   ='$t5',
	`body_b`   ='$t6',
	`body_c`   ='$t7',
	`con`      ='$t8',
	`star`     ='$t9',
	`starq`    ='$t10',
	`ontime`   ='$t11',
	`clotime`  ='$t12',
	`light`    ='$t13',
	`pic`      ='$pic',
	`time`     ='CURRENT_TIMESTAMP' WHERE `id` = '$id'";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function chg_star(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$prod_time=date("Y-m-d H:i:s");
	
	//$id=$_POST['id'];  //name
	//$t1=$_POST['t1'];  //name
	$aka = $_POST['fishstar'];
	$startt = "";
	//print_r($aka);
	foreach ($aka as $value) {
	    $startt .=' id = '.$value.' OR' ;
	}
	$startt = substr($startt, 0, $startt.length -2);
	//echo $startt;

	$sqlt="UPDATE `prod` SET `vip` = '' WHERE 1;";
	$conn->query($sqlt);

	$sqlt="UPDATE `prod` SET `vip` = 'vip' WHERE $startt;";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function del(){
	global $conn;
	$id=$_POST['id'];
	$sqlt = "DELETE FROM `prod` WHERE `id`=$id";
	if ($conn->query($sqlt) === TRUE) {
		echo "Record deleted successfully";
	} else {
		echo "Error deleting record: " . $conn->error;
	}
}

?>