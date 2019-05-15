<?php
include '../config/conn.php';
// print_r($_POST);
if (!isset($_POST['mod_tab'])) {
    exit;
}

switch ($_POST['mod_tab']) {
	case "user_add":
        user_add();
        break;
    case "user_edit":
        user_edit();
        break;
    case "show_list":
        show_list();
        break;
    default:
        //echo "test";
		exit;
}

function user_add() {
    global $conn;
    date_default_timezone_set("Asia/Taipei"); //設定時間台灣 會被這個搞死
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $updated_time = date("Y-m-d H:i:s");
    // print_r($_POST);
    $sqlt="INSERT INTO `user` (`name`, `email`,`phone`,`updated_time`)
						VALUES('$name','$email','$phone','$updated_time')";
	if ($conn->query($sqlt) === TRUE) {
		echo "填寫成功";
    } 
    else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
    }
    header("refresh:1;url=../index.html");
}

function user_edit() {
    global $conn;
    date_default_timezone_set("Asia/Taipei"); //設定時間台灣 會被這個搞死
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $updated_time = date("Y-m-d H:i:s");
    // print_r($_POST);
    $sqlt="UPDATE `user` 
		SET `name`='$name',`email`='$email',`phone`='$phone',`updated_time`='$updated_time' 
		WHERE `id`='$id'";
	if ($conn->query($sqlt) === TRUE) {
		echo "修改成功";
    } 
    else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
    
    header("refresh:1;url=../admin/index.html");
}

function show_list() {
	global $conn;
	$json = array();
	$sqlt = "SELECT * FROM `user` ORDER BY `updated_time` DESC";
	$result = $conn->query($sqlt);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
            ::                                                      
			array_push($json, $row); 
		}
	} else {
		echo "0 results";
	}
	echo json_encode( $json );
}

?>