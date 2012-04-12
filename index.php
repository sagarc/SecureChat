<?
session_start();

if(isset($_GET['logout'])){	
	
	//Simple exit message
	$fp = fopen("log.html", 'a');
	fwrite($fp, "<div class='msgln'><i>User ". $_SESSION['name'] ." has left the chat session.</i><br></div>");
	fclose($fp);
	
	session_destroy();
	header("Location: index.php"); //Redirect the user
}

function loginForm(){
	echo'
	<div id="loginform">
	<form action="index.php" method="post">
		<p>Please enter your name to continue:</p>
		<label for="name">Name:</label>
		<input type="text" name="name" id="name" />
		<input type="submit" name="enter" id="enter" value="Enter" />
	</form>
	</div>
	';
}


//check authentication code here
if(isset($_POST['enter'])){
	if($_POST['name'] != ""){
		$_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
	}
	else{
		echo '<span class="error">Please type in a name</span>';
	}
}
?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chat - Customer Module</title>
<link type="text/css" rel="stylesheet" href="style.css" />
</head>
<?php
if(!isset($_SESSION['name'])){
	loginForm();
}
else{
?>

<body>



 <div id="wrapper">
	
   <div id="menu">     
		<p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
		<p class="logout"><a id="exit" href="#">Exit Chat</a></p>
		<div style="clear:both"></div>
	</div>	
	
   <div id="chatbox">
       <?php //chat will be loaded by jquery?>
   </div>
	
	<form name="message" action="" >
		<input name="usermsg" type="text" id="usermsg" size="63" />
		<input name="submitmsg" type="submit"  id="submitmsg" value="Send" />
	</form>
   
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script src="aes.js">/* AES JavaScript implementation */</script>
<script src="aes-ctr.js">/* AES Counter Mode implementation */</script>
<script src="base64.js">/* Base64 encoding */</script>
<script src="utf8.js">/* UTF-8 encoding */</script>
<script type="text/javascript">
var cckey = "7";

function decodeAES(cipherhtml){
      
      var tokens = cipherhtml.split("<msg>");
      var temp;
      var plainhtml="";     
      var isEncrypt = 0;
      var len = tokens.length;     
      var i=0;
      var token;
      while(i!=len)
      {
        token=tokens[i];        
        
        if(isEncrypt == 1){          
          temp = Aes.Ctr.decrypt(token, cckey, 256);          
          plainhtml = plainhtml.concat(temp);
          isEncrypt = 0;
        }
        else{
          plainhtml = plainhtml.concat(token);          
          isEncrypt = 1;
        }
        i=i+1;        
      }      
      return plainhtml;
    }
    
function encodeAES(plainhtml){
      //decode html using KsAB key                  
      var cipherhtml= Aes.Ctr.encrypt(plainhtml, cckey, 256);            
      return cipherhtml;
    }

// jQuery Document
$(document).ready(function(){
	//If user submits the form
	
    $("#submitmsg").click(function(){	
      //encode this message with KsAB and its MAC
		var clientmsg = $("#usermsg").val();
        var receiver = "server";
        var encryptMsg = encodeAES(clientmsg);       
        
        
		$.post("post.php", {text: encryptMsg});				
		$("#usermsg").attr("value", "");
		return false;
	});
    
	
    
    
	//Load the file containing the chat log
	function loadLog(){		
		var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
        var key = 123;
		$.ajax({
			url: "log.html",
			cache: false,
			success: function(html){		              
				$("#chatbox").html(decodeAES(html)); //Insert chat log into the #chatbox div				                
				var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
				if(newscrollHeight > oldscrollHeight){
					$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
				}				
		  	},
		});
	}
	setInterval (loadLog, 2500);	//Reload file every 2.5 seconds
	
	//If user wants to end session
	
    $("#exit").click(function(){
		var exit = confirm("Are you sure you want to end the session?");
		if(exit==true){window.location = 'index.php?logout=true';}		
	});
});
</script>
<?php
}
?>
</body>
</html>
