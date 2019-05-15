<?php
    
include 'conn.php';//登入

$t1=$_POST['t1'];//name
$t2=$_POST['t2'];//mail
$t3=$_POST['t3'];//phone
$t4=$_POST['t4'];//msg

$msg="";



echo "2.0";
$i=1;
$ssqlt="SELECT * FROM `mail_user`";
$result = $conn->query($ssqlt);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {

echo $i.$row['mail'];
/*		
        if($mail->Send()) {                             // 郵件寄出
            echo $i.$row['mail'];
        } else {
            echo $mail->ErrorInfo . "<br/>";
			die('gg');
        }
		*/
		$i++;
       // $mail->ClearAddresses();                        // 清除使用者欄位，為下一封信做準備
	}
} else {
	echo "0 results";
}

  
?>