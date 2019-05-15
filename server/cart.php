<?php
include 'conn.php';//登入
include("PHPMailer/maildeal.php");
session_start();
$mod_sql=$_POST['mod_p'];
$mod_tab=$_POST['mod_tab'];
$sqlt="";

//$mod_tab='add';
switch ($mod_tab) {
	case "cart_add":
        cart_add();
        break;
	case "favorite_add":
        favorite_add();
        break;
	case "favorite_show":
        favorite_show();
        break;
	case "cart_del":
        cart_del();
        break;
	case "cart_q":
        cart_q();
        break;
	case "show_cart":
        show_cart();
        break;
	case "older_cart":
        older_cart();
        break;
	case "mail_search":
        mail_search();
        break;
    default:
        //echo "test";
		test();
}

function test(){
	global $conn;
//echo count($_SESSION["cart"]);
echo json_encode( $_SESSION["cart"] );
$total=0;
$sql_val='';
$sql = "SELECT * FROM `deal_m` WHERE 1 ORDER BY `id` DESC LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$deal_m_id = $row['id'];
	}
}else{
	die('bug');
}
for($i=0;$i<=count($_SESSION["cart"])-1;$i++){
	$item_p = $_SESSION["cart"][$i]['prod_p'];
	$item_q = $_SESSION["cart"][$i]['Q'];
	$item_id = $_SESSION["cart"][$i]['prod_id'];
	$sum_p += $item_p*$item_q;
	$str_qctrl .="UPDATE `prod` SET `prod_q`=`prod_q`- $item_q WHERE `prod_id`= $item_id;";
	
}

echo "<br>".$total;
//echo "<br>".$sql_val;
echo "<br>".$str_qctrl;
}

function cart_add(){
	global $conn;
	$cart_id=$_POST['cart_id'];
	$size=$_POST['size'];
	$aq=$_POST['aq'];
	if(empty ($_SESSION["cart"])){
		$_SESSION["cart"] = array();
	}else{
		for($i=0;$i<=count($_SESSION["cart"])-1;$i++){
			$chk_id = $_SESSION["cart"][$i]['prod_id'];
			if($chk_id == $cart_id){
				$_SESSION["cart"][$i]['Q'] +=$aq;
				echo json_encode( $_SESSION["cart"] );
				die();
			}
		}
	}
	$sql = "SELECT *
		    FROM `prod`
		    INNER JOIN (
		    		SELECT `prod_s`.`id` as `pid`,`prod_s`.`kindname`, `prod_s`.`prodmind`,`prod_s`.`color`,`prod_s`.`picsrc`,`switchprod`,
		    		IFNULL(`prod_s`.`size_s`+SUM(`prod_log`.`size_s`),`prod_s`.`size_s`) AS 'size_s',
		    		IFNULL(`prod_s`.`size_m`+SUM(`prod_log`.`size_m`),`prod_s`.`size_m`) AS 'size_m', 
		    		IFNULL(`prod_s`.`size_l`+SUM(`prod_log`.`size_l`),`prod_s`.`size_l`) AS 'size_l', 
		    		IFNULL(`prod_s`.`size_f`+SUM(`prod_log`.`size_f`),`prod_s`.`size_f`) AS 'size_f' 
		    		FROM `prod_s`
		    		LEFT JOIN (`prod_log`)
		    		ON (`prod_s`.`id` =`prod_log`.`prod_s_id`)
					WHERE `prod_s`.`id` = '$cart_id'
		    		GROUP BY `prod_s`.`id`
		    		) AS B
		    ON `prod`.`prod_id` = `B`.`prodmind`
            INNER JOIN `prod_kind_s`
            ON `prod_kind_s`.`id` = `prod`.`prod_type`
			ORDER BY `B`.`prodmind` ASC";
	$sql = "SELECT * FROM `prod` WHERE `prod_id` = '$cart_id'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$Q = array('Q'=>$aq);
			$size = array('selsize'=>$size);
			$arr3 = $row + $Q + $size;
			array_push($_SESSION["cart"], $arr3); 
			//$_SESSION["cart"]=$_SESSION["cart"]+$arr3;
		}
	} else {
		echo "0 results";
	}
	echo json_encode( $_SESSION["cart"] );
}

function favorite_add(){
	global $conn;
	$cart_id=$_POST['cart_id'];
	$email = $_SESSION["user_mail"];
	if(empty($email)){
		die('no login');
	}
	$favoritearr = array();
	$favoriteo='';
	$sql = "SELECT `id`, `email`,`favorite` FROM `account` WHERE `email` = '$email'";
	// echo $sql;
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$favoriteo=$row['favorite'];
			$favoritearr = explode(",", $row['favorite']);
		}
	} else {
		echo "0 results";
	}
	foreach ($favoritearr as $value) {
		if($value == $cart_id){
			die('added');
		}
	} 
	if($favoritearr[0]==""){
		$favoriteo ="$cart_id";
	}else{
		$favoriteo .=",$cart_id";
	}
	echo $favoriteo;
	$sql = "UPDATE `account` SET `favorite`='$favoriteo' WHERE `email` = '$email'";
	if ($conn->query($sql) === TRUE) {
		echo "add OK";
		echo "list= $favoriteo";
	} else {
		echo "Error updating record: " . $conn->error;
	}
}
function favorite_show(){
	global $conn;
	$json = array();
	$cart_id=$_POST['cart_id'];
	$email = $_SESSION["user_mail"];
	if(empty($email)){
		die('no login');
	}
	$favoritearr = array();
	$favoriteo='';
	$sql = "SELECT `id`, `email`,`favorite` FROM `account` WHERE `email` = '$email'";
	// echo $sql;
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$favoriteo=$row['favorite'];
			$favoritearr = explode(",", $row['favorite']);
		}
	} else {
		echo "0 results";
	}
	$selfav = '';
	foreach ($favoritearr as $value) {
		 $selfav .=" `prod`.`prod_id` = '$value' OR";
	}
	$selfav = substr($selfav,0,strlen($selfav)-3);
		$sqlt="SELECT `prod_id`, `prod_name`, `prod_p`, `prod_q`, `prod_html`, `prod_pic`, 
		`prod_type`, `prod_status`, `prod_time` ,`prod_s`.`picsrc`
		FROM `prod`
        LEFT JOIN `prod_s` ON `prod`.`prod_id` = `prod_s`.`prodmind`
		WHERE $selfav
		GROUP BY `prod`.`prod_id`;";
	// echo $sqlt;
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

function cart_del(){
	global $conn;
	$cart_id=$_POST['cart_id'];
	$new_cart = array();
	for($i=0;$i<=count($_SESSION["cart"])-1;$i++){
		$chk_id = $_SESSION["cart"][$i]['prod_id'];
		if($chk_id == $cart_id){
			continue;
		}
		array_push($new_cart,$_SESSION["cart"][$i]);
	}
	$_SESSION["cart"]=$new_cart;
}

function cart_q(){
	global $conn;
	$cart_id=$_POST['cart_id'];
	$cart_q=$_POST['cart_q'];
	echo $size;
	for($i=0;$i<=count($_SESSION["cart"])-1;$i++){
		$chk_id = $_SESSION["cart"][$i]['prod_id'];
		if($chk_id == $cart_id){
			$_SESSION["cart"][$i]['Q']=$cart_q;
		}
	}
}

function show_cart(){
echo json_encode( $_SESSION["cart"] );
}



function older_cart(){
	global $conn;
	$t1=$_POST['t1'];//name
	$t2=$_POST['t2'];//email
	$t3=$_POST['t3'];//tel
	$t4=$_POST['t4'];//msg
	$t5=$_POST['t5'];//d_addrnum
	$t6=$_POST['t6'];//drive
	$t6 = "貨到付款";
	$fee=$_POST['fee'];//drive
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$time=date("Y-m-d H:i:s");
	$item_count=count( $_SESSION["cart"] );
	if (filter_var($t2, FILTER_VALIDATE_EMAIL)) {
		
	}else{
		die("Email格式錯誤!!".$t2);
	}
	$user_id=$t2;
	$sum_p=0;
	$str_qctrl='';
	//庫存控制
	$status = "網路銷售";
	$shopdetail='';
	for($i=0;$i<=count($_SESSION["cart"])-1;$i++){
		$item_p = $_SESSION["cart"][$i]['prod_p'];
		$item_q = $_SESSION["cart"][$i]['Q'];
		$item_id = $_SESSION["cart"][$i]['prod_id'];
		$item_q=$item_q;
		$sum_p += $item_p*$item_q;
		$sum_pb = $item_p*$item_q;
		
		$item_n = $_SESSION["cart"][$i]['prod_name'];
		$shopdetail.="$item_n X $item_q :$sum_pb <br>";
		
		$str_qctrl ="INSERT INTO `prod_log`(`prod_s_id`, `status`,`log_q` , `time`) VALUES 
					('$item_id','$status','-$item_q','$time');";
		if ($conn->query($str_qctrl) === TRUE) {
			
		} else {
			echo "Error: " . $str_qctrl . "<br>" . $conn->error;
			die('庫存錯誤!!');
		}
	}
	
	$sql = "INSERT INTO `deal_m`( `user_id`, `name`, `mail`, `tel`,
                     `d_mode`, `d_addr`, `fee_t`, `item_q`, `sum_p`, `time`, `status`, `msg`) 
			VALUES ('$user_id','$t1','$t2','$t3',
					'$t6','$t5','$fee','$item_count','$sum_p','$time','貨到付款-未出貨','$t4')";
	if ($conn->query($sql) === TRUE) {
		//echo "註冊成功";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	//交易主檔最後一筆
	$sql = "SELECT * FROM `deal_m` WHERE 1 ORDER BY `id` DESC LIMIT 1";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$deal_m_id = $row['id'];
		}
	}else{
		die('bug!!');
	}
	//交易活動表
	$sql = "INSERT INTO `deal_a`(`id`, `deal_m_id`, `time`, `status`) 
					VALUES ('null','$deal_m_id','$time','貨到付款-未出貨')";
	if ($conn->query($sql) === TRUE) {
		//echo "successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$sql_val='';
	for($i=0;$i<=count($_SESSION["cart"])-1;$i++){
		$prod_id=$_SESSION["cart"][$i]['prod_id'];
		$prod_p=$_SESSION["cart"][$i]['prod_p'];
		$prod_q=$_SESSION["cart"][$i]['Q'];
		$item_sel = $_SESSION["cart"][$i]['selsize'];
		$item_id = $_SESSION["cart"][$i]['pid'];
		
		if($_SESSION["cart"][$i+1]!==null){
			$sql_val .= "('null','$prod_id','$prod_p','$prod_q','$deal_m_id','$item_sel'),";
		}else{
			$sql_val .= "('null','$prod_id','$prod_p','$prod_q','$deal_m_id','$item_sel');";
		}
	}
	$sql = "INSERT INTO `deal_s`(`id`, `prod_id`, `prod_p`, `prod_q`, `deal_m_id`, `sel_size`) VALUES 
								$sql_val";
	if ($conn->query($sql) === TRUE) {
		echo "successfully";
		unset($_SESSION["cart"]);//清空購物車
		$_SESSION["cart"] = array();
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	
	$t1=$_POST['t1'];//name
	$t2=$_POST['t2'];//email
	$t3=$_POST['t3'];//tel
	$t4=$_POST['t4'];//msg
	$t5=$_POST['t5'];//d_addrnum
	$t6=$_POST['t6'];//drive
	$sum_p +=$fee;
	$shopstr="
		網路銷售<br>
		客戶:$t1<br>
		Email:$t2<br>
		連絡電話:$t3<br>
		購物訊息:$t4<br>
		配送地址:$t5<br>
		購物明細:$shopdetail<br>
		運費:$fee<br>
		總費用:$sum_p<br>
		交易時間:$time<br>
	";
	
	postmail($shopstr,$t2,$t1);
}

function mail_search(){
	global $conn;
	$json = array();
	$mail=$_POST['mail'];
	//$tel=$_POST['tel'];
	//echo $tel;
	$sqlt="SELECT `deal_s`.`deal_m_id`,`name`,`mail` ,`tel`,`d_mode`,`d_addr`,`fee_t`,`deal_s`.`prod_q` as `Q`
	,`sum_p`,`time`,`status`,`msg`, `deal_s`.`prod_p`,`prod`.`prod_name`,`prod`.`prod_pic`,
    `deal_m`.`status`,`deal_m`.`msg`,`prod_s`.`picsrc`,`deal_s`.`sel_size`,`prod_s`.`kindname`
	FROM `deal_m`,`deal_s`,`prod`,`prod_s`
	WHERE `deal_m`.`mail` = '$mail' && `deal_m`.`id`=`deal_s`.`deal_m_id` &&
    `deal_s`.`prod_id` = `prod`.`prod_id` && `prod_s`.`prodmind` = `prod`.`prod_id`
    GROUP BY `deal_s`.`id`";
	
	$sqlt="SELECT `deal_s`.`deal_m_id`,`name`,`mail` ,`tel`,`d_mode`,`d_addr`,`fee_t`,`deal_s`.`prod_q` as `Q`
	,`sum_p`,`time`,`status`,`msg`, `deal_s`.`prod_p`,`prod`.`prod_name`,`prod`.`prod_pic`,
    `deal_m`.`status`,`deal_m`.`msg`,`deal_s`.`sel_size`,`prod`.`prod_pic`
	FROM `deal_m`,`deal_s`,`prod`
	WHERE `deal_m`.`mail` = '$mail' && `deal_m`.`id`=`deal_s`.`deal_m_id` &&
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

?>