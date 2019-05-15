<?php
include 'conn.php';//登入

$mod_sql=$_POST['mod_p'];
$mod_tab=$_POST['mod_tab'];
$sqlt="";

//$mod_tab='add';
switch ($mod_tab) {
   
	case "show_list":
       show_list();
        break;
	case "chg_order":
        chg_order();
        break;
	case "show_deal_a":
        show_deal_a();
        break;
	case "test":
        test();
        break;
	case "orderlist":
        orderlist();
        break;
    default:
        echo "test";
		show_kind();
}

function test(){
	global $conn;
	$json = array();
	if(empty($_POST['id'])){
		$sqlt="SELECT * FROM `prod`";
	}else{
		$sqlt="SELECT * FROM `prod` WHERE `id` = '".$_POST['id']."'";
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
	//$tel=$_POST['tel'];
	$sqlt="SELECT `deal_s`.`deal_m_id`,`name`,`mail` ,`tel`,`d_mode`,`d_addr`,`fee_t`,`deal_s`.`prod_q` as `Q`
	,`sum_p`,`time`,`status`,`msg`, `deal_s`.`prod_p`,`prod`.`prod_name`,`prod`.`prod_pic`,`deal_m`.`status`,`deal_m`.`msg`,`prod_s`.`picsrc`
	FROM `deal_m`,`deal_s`,`prod`,`prod_s`
	WHERE `deal_m`.`id`=`deal_s`.`deal_m_id` &&
    `deal_s`.`prod_id` = `prod`.`prod_id` && `prod_s`.`prodmind` = `prod`.`prod_id`
    GROUP BY `deal_s`.`id`";
	
	
	$sqlt="SELECT `deal_s`.`deal_m_id`,`deal_m`.`id`,`deal_m`.`user_id`,`deal_m`.`name`,`deal_m`.`mail`,`deal_m`.`tel`,`deal_m`.`d_mode`,`deal_m`.`d_addr`,`deal_m`.`fee_t`,
`deal_m`.`item_q`,`deal_m`.`sum_p`,`deal_m`.`time`,`deal_m`.`status`,`deal_m`.`msg`,
`deal_s`.`prod_p`,`deal_s`.`prod_q` as 'Q',
`prod`.`prod_name`,`prod`.`prod_pic`
	FROM `deal_m`,`deal_s`,`prod`
	WHERE `deal_m`.`id`=`deal_s`.`deal_m_id` &&
    `deal_s`.`prod_id` = `prod`.`prod_id`
    GROUP BY `deal_s`.`id`";
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

function chg_order(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$time=date("Y-m-d H:i:s");
	$deal_m_id=$_POST['deal_m_id']; //deal_m_id
	$status=$_POST['t1']; //選擇的項目 1待出貨 2已出貨 3已取貨
	//交易活動表
	$sql = "INSERT INTO `deal_a`(`id`, `deal_m_id`, `time`, `status`) 
					VALUES ('null','$deal_m_id','$time','$status')";
	if ($conn->query($sql) === TRUE) {
	//echo "successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	//交易主檔
	$sql = "UPDATE `deal_m` SET `status`='$status' WHERE `id`='$deal_m_id'";
	if ($conn->query($sql) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
}

function show_deal_a(){
	global $conn;
	$json = array();
	//$tel=$_POST['tel'];
	$sqlt="SELECT * FROM `deal_a` ORDER BY `deal_a`.`deal_m_id` ASC";
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

function orderlist(){
	global $conn;
	$json = array();
	$sqlt="SELECT `deal_s`.`deal_m_id`,`name`,`mail` ,`tel`,`d_mode`,`d_addr`,`fee_t`,`deal_s`.`prod_q` as `Q`
	,`sum_p`,`time`,`status`,`msg`, `deal_s`.`prod_p`,`prod`.`prod_name`,`prod`.`prod_pic`,
    `deal_m`.`status`,`deal_m`.`msg`,`deal_s`.`sel_size`,`prod`.`prod_pic`
	FROM `deal_m`,`deal_s`,`prod`
	WHERE `deal_m`.`id`=`deal_s`.`deal_m_id` &&
    `deal_s`.`prod_id` = `prod`.`prod_id`
    GROUP BY `deal_s`.`id`";
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