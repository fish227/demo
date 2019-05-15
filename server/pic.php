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
	case "addtt":
        addtt();
		 break;
	case "addprodpic":
        addprodpic();
        break;
	case "show":
       show();
        break;
	case "test":
		test();
		break;
}
$json = array();

function diverse_array($vector) { 
    $result = array(); 
    foreach($vector as $key1 => $value1) 
        foreach($value1 as $key2 => $value2) 
            $result[$key2][$key1] = $value2; 
    return $result; 
} 



function pic_num(){
	global $conn;
	global $json;
	$json = array();
	$sqlt="SELECT COUNT(`pic_id`) as `all` FROM `pic_upload`";
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
	//echo json_encode( $json );
	//echo $json[0][all];
}

function add(){
		global $conn;
	global $json;
	$FILE=$_FILES["fileToUpload"];
	$ext = end(explode('.', $FILE['name']));
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$t1=$_POST['add_text'];
	$tt=date("Y-m-d H_i_s");
	if(empty($FILE)){// upload file is null
		$prod_src="";
		die('無照片或傳送失敗，請聯絡工程師!!!');
	}else{
		$prod_src="upload_img/".$tt.".".$ext;// name+time 系統辨識用
		upload_pic($prod_src);
	}
	// `pic_id`, `pic_src`, `pic_backtext`, `pic_time`
	$tt=date("Y-m-d H:i:s");
	$sqlt="INSERT INTO `pic_upload` (`pic_src`, `pic_backtext`, `pic_time`)
						VALUES('$prod_src','$t1','$tt')";
	if ($conn->query($sqlt) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function addtt(){
	global $conn;
	global $json;
	define('UPLOAD_PATH', 'upload_img/');
	// 接收 POST 進來的 base64 DtatURI String
	$img = $_POST['pictt'];
	// 轉檔 & 存檔
	$img = str_replace('data:image/jpeg;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d_H-i-s");
	//$file = UPLOAD_PATH . uniqid() . '.png';
	$file = UPLOAD_PATH . $tt . '.jpg';
	$success = file_put_contents($file, $data);
	// `pic_id`, `pic_src`, `pic_backtext`, `pic_time`
	$tt=date("Y-m-d H:i:s");
	$sqlt="INSERT INTO `pic_upload` (`pic_src`, `pic_backtext`, `pic_time`,`pickind`)
						VALUES('$file','','$tt','')";
	if ($conn->query($sqlt) === TRUE) {
		//echo "New record created successfully";
		echo $file;
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function addprodpic(){
	global $conn;
	global $json;
	$FILE=$_FILES["fileToUpload"];
	//print_r(substr($FILE['name'] ,-3));
	//die();
	$exta = explode('.', $FILE['name']);
	$ext = end($exta);
	// $ext = substr($FILE['name'] ,-3);
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$t1=$_POST['add_text'];
	$t2=$_POST['pickind'];
	$tt=date("Y-m-d_H-i-s");
	if(empty($FILE)){// upload file is null
		$prod_src="";
		die('無照片或傳送失敗，請聯絡工程師!!!');
	}else{
		$prod_src="upload_img/".$tt.".".$ext;// name+time 系統辨識用
		upload_pic($prod_src);
	}
	// `pic_id`, `pic_src`, `pic_backtext`, `pic_time`
	$tt=date("Y-m-d H:i:s");
	$sqlt="INSERT INTO `pic_upload` (`pic_src`, `pic_backtext`, `pic_time`,`pickind`)
						VALUES('$prod_src','$t1','$tt','$t2')";
	if ($conn->query($sqlt) === TRUE) {
		//echo "New record created successfully";
		echo $prod_src;
	} else {
		echo "Error: " . $sqlt . "<br>" . $conn->error;
	}
}

function show(){
	global $conn;
	$json = array();
	if(empty($_POST['id'])){
		$sqlt="SELECT * FROM `pic_upload` WHERE NOT `pickind` = 'prod' ORDER BY `pic_upload`.`pic_id` DESC";
	}else{
		$sqlt="SELECT * FROM `pic_upload` WHERE `prod_id` = '".$_POST['id']."'";
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

function upload_pic($file_name){
	$FILE=$_FILES["fileToUpload"];
	//檢查有無檔案
	if(empty($FILE)){
		die("無檔案");
	}
	//是否存在
	if (file_exists($position . $FILE["name"])){
		echo "檔案已經存在，請勿重覆上傳相同檔案!!<br>";
		/*
		echo "檔案如下<br>";
		echo "<img src='upload/".$FILE["name"]."'>";
		*/
		
	}else{
		if(move_uploaded_file($FILE["tmp_name"],iconv("UTF-8", "big5", "".$file_name ))) {
		//echo "檔案：". $FILE['name'] . " 上傳成功!";
		//echo "上傳成功";
		echo "";
		} else{
		die( "檔案上傳失敗，請再試一次!");
		}
	}
}



?>