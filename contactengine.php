<?php
$sendfrom = "site@slavforest.com.ua";
$sendto = "sha@shadoll.work; info@slavforest.com.ua";

$subject = "📨 Повідомлення з сайту slavforest.com.ua";

$username = !empty($_POST['name'])?trim(stripslashes($_POST['name'])):'';
$userphone = !empty($_POST['phone'])?trim(stripslashes($_POST['phone'])):'';
$usermail = !empty($_POST['email'])?trim(stripslashes($_POST['email'])):'';

$order = !empty($_POST['order'])?trim(stripslashes($_POST['order'])):'';
$subj = !empty($_POST['subject'])?trim(stripslashes($_POST['subject'])):'';
$message = !empty($_POST['message'])?trim(stripslashes($_POST['message'])):'';

$referer = !empty($_POST['referer'])?trim(stripslashes($_POST['referer'])):'';

$ip = $_SERVER['REMOTE_ADDR']; // the IP address to query
$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
if($query && $query['status'] == 'success') {
	$detect = true;
	$country_code = $query['countryCode'];
	$country = $query['country'];
	$region = $query['regionName'];
	$city = $query['city'];
	$provider = $query['isp'];
}
else
	$detect = false;

$headers  = "From:".strip_tags($sendfrom)."\r\n";
if(!empty($usermail))
	$headers .= "Reply-To: ".strip_tags($usermail)."\r\n";
else
	$headers .= "Reply-To: ".strip_tags($sendto)."\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html;charset=utf-8 \r\n";

$msg  = "<html><body style='font-family:Arial,sans-serif;'>";
$msg .= '<img src="https://zebnits.com/images/logo_black.png" alt="logo">';
$msg .= "<h2 style='font-weight:bold;border-bottom:1px dotted #ccc;'>".$subject."</h2>\r\n";
$msg .= "<p><strong>Від кого:</strong> ".$username."</p>\r\n";
if(!empty($userphone))
	$msg .= "<p><strong>Телефон:</strong> ".$userphone."</p>\r\n";
if(!empty($usermail))
	$msg .= "<p><strong>Email:</strong> ".$usermail."</p>\r\n";
$msg .= "<hr>\r\n";
if(!empty($subj))
	$msg .= "<p><strong>Тема:</strong>\r\n".$subj."</p>\r\n";
if(!empty($order))
	$msg .= "<p><strong>Заказ:</strong>\r\n".$order."</p>\r\n";
if(!empty($message))
	$msg .= "<p><strong>Повідомлення:</strong>\r\n".$message."</p>\r\n";
$msg .= "<hr>\r\n";
$msg .= "<p><strong>Реферал:</strong> ".$referer."</p>\r\n";
$msg .= "<p><strong>IP-адреса відправника:</strong> ".$ip."</p>\r\n";
if($detect){
	$msg .= "<p><strong>Провайдер:</strong> ".$provider."</p>\r\n";
	$msg .= "<p><strong>Країна:</strong> ".$country." <img style='height:14px; width:auto' src='http://www.geognos.com/api/en/countries/flag/".$country_code.".png'></p>\r\n";
	$msg .= "<p><strong>Регіон:</strong> ".$region."</p>\r\n";
	$msg .= "<p><strong>Населений пункт:</strong> ".$city."</p>\r\n";
}
$msg .= "</body></html>";

if(empty($username)){
	echo json_encode([
		'message'=>'Please enter your name.',
		'callback'=>'error',
		'focus'=>'name'
	]);
}
elseif(empty($usermail)){
	echo json_encode([
		'message'=>'Please enter valid e-mail.',
		'callback'=>'error',
		'focus'=>'email'
	]);
}
else{
	$success = @mail($sendto, $subject, $msg, $headers);

	echo json_encode([
		'html'=>'our message has been sent. We will reply soon. Thank you!',
		'target'=>".response",
		'callback'=>"message_send"
	]);
}
