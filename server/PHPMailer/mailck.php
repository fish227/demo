<?php
include("PHPMailerAutoload.php"); //匯入PHPMailer類別       
include 'conn.php';//登入

$t1=$_POST['t1'];//name
$t2=$_POST['t2'];//mail

$msg="";

if (filter_var($t2, FILTER_VALIDATE_EMAIL)) {
    //echo "This ($t3) email address is considered valid.\n";
}else{
	die("Email格式錯誤!!");
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
$mail->AddAddress($t2, "Email驗證信"); //設定收件者郵件及名稱        
//$to_mail='4a190063@stust.edu.tw';//寄給誰
$mail->Body = '您好：
<br>
感謝您註冊DACN帳戶。若要完成帳戶啟用，請按下列連結。
<br>
<a href="https://dacn.com.tw\demo\Shop\mag\server\mailok.php" target="_blank" data-saferedirecturl="https://dacn.com.tw\demo\Shop\mag\server\mailok.php">https://dacn.com.tw\demo\Shop\mag\server\mailok.php</a>
'; //設定郵件內容   

if($mail->Send()) {                             // 郵件寄出
    
} else {
    echo $mail->ErrorInfo . "<br/>";
	die();
}

echo "驗證信已寄出";


?>