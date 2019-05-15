<?php
include 'conn.php';//登入
session_start();
$mod_sql=$_POST['mod_p'];
$mod_tab=$_POST['mod_tab'];
$sqlt="";

//$mod_tab='add';
switch ($mod_tab) {
    case "login":
       login();
	   break;
	case "login_status":
       login_status();
        break;
	case "logout":
		logout();
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
	if($t1 !==$t2){
		die("密碼兩次輸入不一樣");
	}
	//	id email user_name user_pass user_fb reg_time addr phone
	$sql = "UPDATE `accountg` SET  
			`user_pass`='$t1'
			WHERE `email` = '$user_email'";
	//$sql = "UPDATE MyGuests SET lastname='Doe' WHERE id=2";
	if ($conn->query($sql) === TRUE) {
		echo "修改成功";
		//echo $sql ;
	} else {
		echo "Error updating record: " . $conn->error;
	}

	//echo $sql;
}

function logout(){
	// remove all session variables
	unset($_SESSION["user_mail"]);
	echo "登出成功";
}

function login_status(){
	if(!empty($_SESSION["user_mail"])){
		$json = array(
		"user_mail"=>$_SESSION["user_mail"]
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
	$sql = "SELECT * FROM `accountg` WHERE `email` = '$t1'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			if($row["user_pass"]==$t2){
				$_SESSION["user_mail"] = $row["email"];
				echo "登入成功，你好~ 歡迎使用後端";
				//最後登入時間
				date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
				$last_time=date("Y-m-d H:i:s");
				$id=$row["id"];
				$sqlt = "UPDATE `accountg` SET `last_time` = '$last_time' WHERE `id` = '$id'";
				$conn->query($sqlt);
			}else{
				die("帳號或密碼錯誤!!");
			}
		}
	} else {
		die("帳號或密碼錯誤!");
	}
}


?>