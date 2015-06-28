<?php
//Generic php function to send GCM push notification
  function sendMessageThroughGCM($registation_ids, $message) {
		// hacky regId here:
    $registration_ids = "INSERT CODE HERE";
    
    //Google cloud messaging GCM-API url
    $url = 'https://android.googleapis.com/gcm/send';
    $fields = array(
      'registration_ids' => $registation_ids,
      'data' => $message,
    );
		// Update your Google Cloud Messaging API Key
		define("GOOGLE_API_KEY", "INSERT API CODE HERE"); 		
    $headers = array(
      'Authorization: key=' . GOOGLE_API_KEY,
      'Content-Type: application/json'
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);	
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);				
    if ($result === FALSE) {
      die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
  }
	
//Post message to GCM when submitted
	$pushStatus = "GCM Status Message will appear here";	
	if(!empty($_GET["push"])) {	
		$gcmRegID  = file_get_contents("GCMRegId.txt");
		$pushMessage = $_POST["message"];
    
    echo "<br />pushMessage: " . $pushMessage . "<br />";
    
		if (isset($gcmRegID) && isset($pushMessage)) {
			$gcmRegIds = array($gcmRegID);
			$message = array("data" => $pushMessage,
                       "title" => "Update Time"
      );	
			$pushStatus = sendMessageThroughGCM($gcmRegIds, $message);
		}		
	}
	
	//Get Reg ID sent from Android App and store it in text file
	if(!empty($_GET["shareRegId"])) {
		$gcmRegID  = $_POST["regId"]; 
		file_put_contents("GCMRegId.txt",$gcmRegID);
		exit;
	}	
?>
<html>
  <head>
  <title>GCM localhost test</title>
  <style>
    div#formdiv, p#status{
      text-align: center;
      background-color: #e1e1e1;
      border: 2px solid #000000;
      padding: 10px;
    }
		textarea{
			border: 2px solid #333333;
			margin-bottom: 10px;			
			text-align: center;
			padding: 10px;
			font-size: 25px;
			font-weight: bold;
		}
		input{
			background-color: #999999;
			border: 5px solid #666666;
			padding: 10px;
			cursor: pointer;
			color: #fff;
			font-weight: bold;
		}			 
		</style>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script>
		$(function(){
			$("textarea").val("");
		});
		function checkTextAreaLen(){
			var msgLength = $.trim($("textarea").val()).length;
			if(msgLength == 0){
				alert("Please enter message before hitting submit button");
				return false;
			}else{
				return true;
			}
		}
	</script>
  </head>
	<body>
		<div id="formdiv">
      <h1>GCM localhost test for push API access</h1>	
      <form method="post" action="/test/gcm.php/?push=true" onsubmit="return checkTextAreaLen()">					                                                      
				<textarea rows="5" name="message" cols="45" placeholder="Message to send via GCM"></textarea> 
        <br/>
				<input type="submit"  value="Send Push Notification through GCM" />
      </form>
		</div>
		<p id="status">
      <?php echo $pushStatus; ?>
		</p>        
  </body>
</html>