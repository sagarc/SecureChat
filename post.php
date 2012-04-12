<?
session_start();
if(isset($_SESSION['name'])){
	$text = $_POST['text'];
    $to = $_POST['to'];
    if($to == 'server')echo "Message for server";
	
	$fp = fopen("log.html", 'a');
	//fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
    fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b>:<msg>".$text."<msg><br></div>");
	fclose($fp);
}
?>