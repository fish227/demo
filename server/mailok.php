<html>
<body>
<?php
	include 'conn.php';//登入
	$t1=$_GET['email'];
	if(empty($t1)){
		die("Email錯誤");
	}
	date_default_timezone_set("Asia/Taipei");//設定時間台灣 會被這個搞死
	$tt=date("Y-m-d H:i:s");
	$sql = "SELECT * FROM `account` WHERE `email` = '$t1'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

		}
	}else{
		die("Email錯誤");
	}
	$sql = "UPDATE `account` SET  
		`mailck`='$tt'
		WHERE `email` = '$t1'";
	//$sql = "UPDATE MyGuests SET lastname='Doe' WHERE id=2";
	if ($conn->query($sql) === TRUE) {
		echo "驗證成功!";
		//echo $sql ;
	} else {
		echo "Error updating record: " . $conn->error;
	}
?>
<p id="txt">5秒後轉至首頁...</p>
<script>
	timedText();
	function timedText() {
		var x = document.getElementById("txt");
		setTimeout(function(){ x.innerHTML ="4秒後轉至首頁..." }, 1000);
		setTimeout(function(){ x.innerHTML ="3秒後轉至首頁..." }, 2000);
		setTimeout(function(){ x.innerHTML ="2秒後轉至首頁..." }, 3000);
		setTimeout(function(){ x.innerHTML ="1秒後轉至首頁..." }, 4000);
		setTimeout(function(){
			location.replace('https://dacn.com.tw/demo/Shop/login.html');
		}, 5000);
	}
</script>

</body>
</html>