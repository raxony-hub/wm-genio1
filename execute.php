<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";

$text = trim($text);
$text = strtolower($text);

header("Content-Type: application/json");

$response = '';
if(strpos($text, "/start") === 0 || $text=="ciao")
{
	$response = "Ciao $firstname, benvenuto nel nuovo WM di Beppe (Tony)! Usa il comando /inserisci per inserire un nuovo fantastico contatto, oppure il comando /elenco per vedere chi hai da chiamare oggi. :)";

	$link = mysqli_connect("remotemysql.com:3306", "bfFvkAb7fr", "WoC7xGtmgK", "bfFvkAb7fr");
	if (mysqli_connect_errno()) {
    		$response .= "Connect failed: %s\n".mysqli_connect_error();
    	
	}
	
	if (mysqli_ping($link)) {
	    $response .= "\n\nOur connection is ok!\n";
	} else {
	    $response .= "Error: \n".mysqli_error($link);
	}
	
	$querry = "SELECT * FROM `Utenti` WHERE Nome = $username";
	$Result = mysqli_query($link,$querry);
	if( !$Result )
	{
		$response .= "\nerrore query (select): ".mysqli_error($Result);
	}
	
	if (mysqli_num_rows($Result) > 0)
	{
		//creo un nuovo utente;
		$querry = "INSERT INTO `Utenti` (`Nome`, `N_contatto`, `stato`) VALUES ('$username', '0', 'nuovo')";
		
		$Result = mysqli_query($link,$querry);
		if( !$Result )
		{
			$response .= "\nerrore query (insert): ".mysqli_error($Result);
		}
	}
	
	mysqli_close($link);
}
elseif(strpos($text, "/inserisci") === 0)
{
	$response = "Bello, un nuovo contatto! Se non sai cosa rispondere, scrivi 'no'. Dimmi il nome e cognome:";
}
elseif(strpos($text, "/elenco") === 0)
{
	$response = "ti dò l'elenco dei da richiamare del giorno".substr($text,8);
}
else
{
	$response = "Comando non valido!";
}

$parameters = array('chat_id' => $chatId, "text" => $response);
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
