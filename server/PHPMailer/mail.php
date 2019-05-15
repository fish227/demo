<?php
include("PHPMailerAutoload.php"); //匯入PHPMailer類別       
include 'conn.php';//登入

$t1=$_POST['t1'];//name
$t2=$_POST['t2'];//mail
$t3=$_POST['t3'];//phone
$t4=$_POST['t4'];//msg

$msg="";

if (filter_var($t2, FILTER_VALIDATE_EMAIL)) {
    //echo "This ($t3) email address is considered valid.\n";
}else{
	die("Email格式錯誤!!");
}
if(empty($t1)||empty($t2)||empty($t3)||empty($t4)){
	die('無內容');
}else{
	$msg .="聯絡人:$t1<br>";
	$msg .="email：$t2<br>";
	$msg .="電話：$t3<br>";
	$msg .="訊息：$t4<br>";
	//echo "msg=$msg";
}

$mail= new PHPMailer(); //建立新物件        
$mail->IsSMTP(); //設定使用SMTP方式寄信        
$mail->SMTPAuth = true; //設定SMTP需要驗證        
$mail->SMTPSecure = "ssl"; // Gmail的SMTP主機需要使用SSL連線   
$mail->Host = "smtp.gmail.com"; //Gamil的SMTP主機        
$mail->Port = 465;  //Gamil的SMTP主機的SMTP埠位為465埠。        
$mail->CharSet = "utf8"; //設定郵件編碼        

$mail->Username = "q11295@gmail.com"; //設定驗證帳號        
$mail->Password = "love4563"; //設定驗證密碼   
     
$mail_from='q11295@gmail.com';//用誰的帳號寄

$mail->From = 'q11295@gmail.com'; //設定寄件者信箱        
$mail->FromName = "系統發件"; //設定寄件者姓名        

$mail->Subject = "網頁瀏覽者來信"; //設定郵件標題  

$mail->Body = "這是網頁的來信，內容如下<br>$msg"; //設定郵件內容        
$mail->IsHTML(true); //設定郵件內容為HTML        
//$mail->AddAddress("vwx506111@gmail.com", "網頁系統發送"); //設定收件者郵件及名稱        
//$mail->AddAddress("4a190063@stust.edu.tw", "網頁系統發送"); //設定收件者郵件及名稱        
//$to_mail='4a190063@stust.edu.tw';//寄給誰

$ssqlt="SELECT * FROM `mail_user`";
$result = $conn->query($ssqlt);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$mail->AddAddress($row['mail'], $row['name']);  // 收件者郵件及名稱 
        $mail->Body = "這是網頁的來信，內容如下<br>$msg"; //設定郵件內容   
        if($mail->Send()) {                             // 郵件寄出
            
        } else {
            echo $mail->ErrorInfo . "<br/>";
			die();
        }
        $mail->ClearAddresses();                        // 清除使用者欄位，為下一封信做準備
	}
} else {
	echo "0 results";
}

date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
$mail_time=date("Y-m-d H:i:s");
$sqlt="INSERT INTO `mail_msg`( `name`, `email`, `phone`, `msg`, `status`, `post_time`) 
						VALUES ('$t1','$t2','$t3','$t4','1','$mail_time')";
if ($conn->query($sqlt) === TRUE) {
	echo "訊息送出";
	//echo "訊息送出\n";
} else {
	echo "Error: " . $sqlt . "<br>" . $conn->error;
}
//echo "請耐心等候回覆!!\n或是直接來電詢問\n確定後回到網站首頁";     
  
?>