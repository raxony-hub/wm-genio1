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
	$response = "Ciao $firstname, benvenuto nel nuovo WM di Beppe (Tony)! Usa il comando /inserisci per inserire un nuovo fantastico contatto, oppure il comando /elenco per vedere chi hai da chiamare oggi.";
	
	$link = mysql_connect("remotemysql.com:3306", "bfFvkAb7fr", "WoC7xGtmgK");
	if(!$link)
	{
		$response."impossibile stabilire una connessione ". mysql_error();
	} else {
		$response."connesso baby";
	}
	
	//$DBsel = mysql_select_db("bfFvkAb7fr", $link);
	//if(!$DBsel)
	//{
	//	$response."impossibile selezionare la connessione " . mysql_error();
	//}
	
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
