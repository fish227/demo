<?php
function postmail($dealdetail,$custmail,$custname){
	include("PHPMailerAutoload.php"); //匯入PHPMailer類別       
	include("conn.php"); //匯入PHPMailer類別       
	
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
	$mail->Host = "vera-korea.com"; //此處請填寫您的郵件伺服器位置,通常是mail.網址。如果您MX指到外地，那這邊填入www.XXX.com 即可
	$mail->Port = 25; //ServerZoo主機的郵件伺服器port為 25 

	// 信件內容的編碼方式       
	$mail->CharSet = "utf-8";

	// 信件處理的編碼方式
	$mail->Encoding = "base64";

	// SMTP 驗證的使用者資訊
	$mail->Username = "server@years.com.tw";  // 此處為驗証電子郵件帳號,就是您在ServerZoo主機上新增的電子郵件帳號，＠後面請務必一定要打。
	$mail->Password = "0919167710";  //此處為上方電子郵件帳號的密碼 (一定要正確不然會無法寄出)
	$mail_from='test@dacn.com.tw';//用誰的帳號寄
	// 信件內容設定  
	$mail->From = "server@years.com.tw"; //此處為寄出後收件者顯示寄件者的電子郵件 (請設成與上方驗証電子郵件一樣的位址)
	$mail->FromName = "Vera系統Mail"; //此處為寄出後收件者顯示寄件者的名稱
	$mail->Subject = "Vera網頁來信"; //此處為寄出後收件者顯示寄件者的電子郵件標題
	$mail->Body = "".$msg;   //信件內容 
	$mail->IsHTML(true);

	// 收件人
	//$mail->AddAddress("q11295@gmail.com", "Dacn系統通知信"); //此處為收件者的電子信箱及顯示名稱
	//$mail->AddAddress("q11295@gmail.com", "Dacn系統通知信"); //此處為收件者的電子信箱及顯示名稱
	//$to_mail='q11295@gmail.com';//寄給誰
	// 顯示訊息
	
	$ssqlt="SELECT * FROM `mail_user`";
	$result = $conn->query($ssqlt);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$mail->AddAddress($row['mail'], $row['name']);  // 收件者郵件及名稱 
			$mail->Body = "$dealdetail"; //設定郵件內容   
			if($mail->Send()) {                             // 郵件寄出
				
			} else {
				echo $mail->ErrorInfo . "<br/>";
				die();
			}
			$mail->ClearAddresses();                        // 清除使用者欄位，為下一封信做準備
		}
	} else {
		// echo "email 0 results";
	}
	$mail->AddAddress($custmail, $row['name']);  // 收件者郵件及名稱 
	$mail->Body = "$dealdetail"; //設定郵件內容   
	if($mail->Send()) {                             // 郵件寄出
		
	} else {
		echo $mail->ErrorInfo . "<br/>";
		die();
	}
	$mail->ClearAddresses();                        // 清除使用者欄位，為下一封信做準備
}
?>