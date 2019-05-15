<?php
include 'conn.php';//登入
session_start();
$mod_sql=$_POST['mod_p'];
$mod_tab=$_POST['mod_tab'];
$sqlt="";

//$mod_tab='add';
switch ($mod_tab) {
	case "add":
        add();
        break;
    case "login":
       login();
	   break;
	case "fblogin":
       fblogin();
        break;
	case "login_status":
       login_status();
        break;
	case "test":
        test();
        break;
	case "logout":
		logout();
		break;
	case "show_detail":
		show_detail();
		break;
	case "chg":
		chg();
		break;
    default:
        echo "test";
		test();
		break;
}

function test(){
	$t1=$_POST['t1'];
	$t2=$_POST['t2'];
	$t3=$_POST['t3'];
	$t4=$_POST['t4'];
	echo 't1='.$t1.',t2='.$t2.',t3='.$t3.',t4='.$t4;
}

function chg(){
	global $conn;
	$user_email = $_SESSION["user_mail"];
	$t1 = $_POST['t1'];
	$t2 = $_POST['t2'];
	$t3 = $_POST['t3'];
	//	id email user_name user_pass user_fb reg_time addr phone
	$sql = "UPDATE `account` SET  
			`user_name`='$t1',
			`phone`='$t2',
			`addr`='$t3'
			WHERE `email` = '$user_email'";
	//$sql = "UPDATE MyGuests SET lastname='Doe' WHERE id=2";
	if ($conn->query($sql) === TRUE) {
		echo "修改成功";
		$_SESSION["user_name"]=$t1;
		$_SESSION["phone"]=$t2;
		$_SESSION["addr"]=$t3;
		//echo $sql ;
	} else {
		echo "Error updating record: " . $conn->error;
	}

	//echo $sql;
}

function show_detail(){
	global $conn;
	$t1 = $_SESSION["user_mail"];
	$_SESSION["info"]=array();
	//	id email user_name user_pass user_fb reg_time addr phone
	$sql = "SELECT id, email, user_name, user_fb, reg_time, addr, phone, by_mod, mailck FROM `account` WHERE `email` = '$t1'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
				array_push($_SESSION["info"], $row);
				//print_r ($row);
		}
	}
	//echo $sql;
	echo json_encode( $_SESSION['info'] );
}

function logout(){
	// remove all session variables
	unset($_SESSION["user_mail"]);
	unset($_SESSION["user_name"]);
	unset($_SESSION["addr"]);
	unset($_SESSION["by_mod"]);
	unset($_SESSION["mailck"]);
	echo "登出成功";
}

function login_status(){
	if(!empty($_SESSION["user_mail"])|| !empty($_SESSION["user_name"])){
		$json = array(
		"user_mail"=>$_SESSION["user_mail"],
		"user_name"=>$_SESSION["user_name"],
		"phone"=>$_SESSION["phone"],
		"addr"=>$_SESSION["addr"]
		);
		echo json_encode( $json );
	}else{
		echo '0';
	}
}

function login(){
	global $conn;
	$t1=$_POST['t1'];//email
	$t2=$_POST['t2'];//password
	//檢查Email
	if (filter_var($t1, FILTER_VALIDATE_EMAIL)) {

	}else{
		die("Email格式錯誤!!");
	}
	//	id email user_name user_pass user_fb reg_time addr phone
	$sql = "SELECT * FROM `account` WHERE `email` = '$t1'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			if($row["user_pass"]==$t2){
				$_SESSION["user_mail"] = $row["email"];
				$_SESSION["user_name"] = $row["user_name"];
				$_SESSION["addr"] = $row["addr"];
				$_SESSION["phone"] = $row["phone"];
				echo "登入成功";
				//最後登入時間
				date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
				$last_time=date("Y-m-d H:i:s");
				$id=$row["id"];
				$sqlt = "UPDATE `account` SET `last_time` = '$last_time' WHERE `id` = '$id'";
				$conn->query($sqlt);
			}else{
				die("帳號或密碼錯誤");
			}
		}
	} else {
		die("帳號或密碼錯誤");
	}
}

function fblogin(){
	global $conn;
	$t1=$_POST['t1'];//name
	$t2=$_POST['t2'];//email
	$t3=$_POST['t3'];//fbID
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	//	id email user_name user_pass user_fb reg_time addr phone
	$sql = "SELECT * FROM `account` WHERE `user_fb` = '$t3'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$_SESSION["user_mail"] = $row["email"];
			$_SESSION["user_name"] = $row["user_name"];
			$_SESSION["phone"] = $row["phone"];
			echo "登入成功，".$_SESSION["user_name"]."你好~";
			//最後登入時間
			
			$last_time=date("Y-m-d H:i:s");
			$id=$row["id"];
			$sqlt = "UPDATE `account` SET `last_time` = '$last_time' WHERE `id` = '$id'";
			$conn->query($sqlt);
		}
	} else {
		$sqle = "SELECT * FROM `account` WHERE `email` = '$t2'";
		$result2 = $conn->query($sqle);
		if ($result2->num_rows > 0) {
			while($row = $result2->fetch_assoc()) {
					$_SESSION["user_mail"] = $row["email"];
					$_SESSION["user_name"] = $row["user_name"];
					$_SESSION["phone"] = $row["phone"];
					echo "同步Email帳號，登入成功，".$_SESSION["user_name"]."你好~";
					//最後登入時間
					date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
					$last_time=date("Y-m-d H:i:s");
					$id=$row["id"];
					$sqlt = "UPDATE `account` SET `user_fb`= '$t3', `last_time` = '$last_time' WHERE `id` = '$id'";
					$conn->query($sqlt);
				
			}
		}else{
			echo "首次FB登入~";
			$reg_time=date("Y-m-d H:i:s");
			$sqlt="INSERT INTO `account` (`email`, `user_name`, `user_fb`, `reg_time`,`last_time`)
									VALUES('$t2', '$t1','$t3','$reg_time','$reg_time')";
			if ($conn->query($sqlt) === TRUE) {
				echo "註冊成功";
				$_SESSION["user_name"] = $t1;
				$_SESSION["user_mail"] = $t2;
			} else {
				echo "Error: " . $sqlt . "<br>" . $conn->error;
			}
		}
	}
}

function add(){
	global $conn;
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$ck=$_POST['ck'];//ck
	$t1=$_POST['t1'];//email
	$t2=$_POST['t2'];//password
	$t3=$_POST['t3'];//password againg
	$t4=$_POST['t4'];//addr
	$t5=$_POST['t5'];//tel
	$reg_time=date("Y-m-d H:i:s");
	if (filter_var($t1, FILTER_VALIDATE_EMAIL)) {
		
	}else{
		die("Email格式錯誤!!");
	}
	if($ck!=="5566"){
		die("邀請碼錯誤!");
	}
	if($t2!==$t3){
		die("密碼不一致!!");
	}
	$sql = "SELECT `email` FROM account WHERE `email` = '$t1'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		die("Email已存在!!");
	} else {
		//echo "0 results";
	}
	$sqlt="INSERT INTO `account` (`email`, `user_pass`,`addr`,`phone`, `reg_time`,`last_time`)
						VALUES('$t1','$t2','$t4','$t5','$reg_time','$reg_time')";
	if ($conn->query($sqlt) === TRUE) {
		echo "註冊成功";
		$_SESSION["user_mail"] = $t1;
		$_SESSION["addr"] = $t4;
		$_SESSION["phone"] = $t5;
		
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

?>