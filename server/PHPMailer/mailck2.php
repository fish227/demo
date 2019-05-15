<?php
include("PHPMailerAutoload.php"); //匯入PHPMailer類別       
include 'conn.php';//登入
header("Content-Type:text/html; charset=utf-8");
$t1=$_POST['t1'];//name
$t2=$_POST['t2'];//mail

$msg="";

if (filter_var($t2, FILTER_VALIDATE_EMAIL)) {
    //echo "This ($t3) email address is considered valid.\n";
}else{
	die("Email格式錯誤!!");
}

// 產生 Mailer 實體
$mail = new PHPMailer();

// 讓phpmailer 不要自動使用SSL連線(適用於PHP 5.6以上，非5.6可不用這段)
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

// 設定為 SMTP 方式寄信
$mail->IsSMTP();

// SMTP 伺服器的設定，以及驗證資訊
$mail->SMTPAuth = true;      
$mail->Host = "www.dacn.com.tw"; //此處請填寫您的郵件伺服器位置,通常是mail.網址。如果您MX指到外地，那這邊填入www.XXX.com 即可
$mail->Port = 25; //ServerZoo主機的郵件伺服器port為 25 

// 信件內容的編碼方式       
$mail->CharSet = "UTF-8";

// 信件處理的編碼方式
$mail->Encoding = "base64";

// SMTP 驗證的使用者資訊
$mail->Username = "test@dacn.com.tw";  // 此處為驗証電子郵件帳號,就是您在ServerZoo主機上新增的電子郵件帳號，＠後面請務必一定要打。
$mail->Password = "0919167710";  //此處為上方電子郵件帳號的密碼 (一定要正確不然會無法寄出)
$mail_from='test@dacn.com.tw';//用誰的帳號寄
// 信件內容設定  
$mail->From = "test@dacn.com.tw"; //此處為寄出後收件者顯示寄件者的電子郵件 (請設成與上方驗証電子郵件一樣的位址)
$mail->FromName = "系統Mail"; //此處為寄出後收件者顯示寄件者的名稱
$mail->Subject = "Email驗證信"; //此處為寄出後收件者顯示寄件者的電子郵件標題

$mail->IsHTML(true);


$mail->AddAddress($t2, "Email驗證信"); //設定收件者郵件及名稱        
//$to_mail='4a190063@stust.edu.tw';//寄給誰
$mail->Body = '
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>PHPMailer Examples</title>
</head>
<body>
您好：
<br>
感謝您註冊DACN帳戶。若要完成帳戶啟用，請按下列連結。
<br>
<a href="https://dacn.com.tw/demo/Shop/mag/server/mailok.php?email='.$t2.'" target="_blank" data-saferedirecturl="https://dacn.com.tw\demo\Shop\mag\server\mailok.php?email='.$t2.'">https://dacn.com.tw\demo\Shop\mag\server\mailok.php?email='.$t2.'</a>
<br>
如果沒有反應，您可以將連結複製到瀏覽器視窗或直接輸入連結網址。
</body>
</html>'; //設定郵件內容   

if($mail->Send()) {                             // 郵件寄出
    
} else {
    echo $mail->ErrorInfo . "<br/>";
	die();
}

echo "驗證信已寄出";

  
?>