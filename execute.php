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
	$response = $response."/nprova prova prova";
	/*if(@mysql_ping()) $response.'true';
	else $response.'false';*/
	$link = mysqli_connect("remotemysql.com:3306", "bfFvkAb7fr", "WoC7xGtmgK", "bfFvkAb7fr");
	if (mysqli_connect_errno()) {
    		$response = $response."Connect failed: %s\n".mysqli_connect_error();
    	
	}
	
	if (mysqli_ping($link)) {
	    $response = $response."Our connection is ok!\n";
	} else {
	    $response = $response."Error: %s\n".mysqli_error();
	}
	
	$response = $response."/nprova prova prova";
	
	/*$DBsel = mysqli_select_db("bfFvkAb7fr", $link);
	if(!$DBsel)
	{
		$response."impossibile selezionare la connessione " . mysqli_error();
	}*/
	
	$querry = "SELECT *  FROM Utenti";
	$Result = mysqli_query($querry);
	if( !$Result )
	{
		$response = $response."errore query: ".mysqli_error();
	}
	
	while($row = mysqli_fetch_array($Result))
	{
		$response = $response."/n"."Nome utente".$row[1]."/n"."codice".$row[1]."/n"."stato".$row[1];
	}
	
	mysqli_close($link);
	
	$response = $response."/nprova prova prova";
	
	/*$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

	$server = $url["host"];
	$username = $url["user"];
	$password = $url["pass"];
	$db = substr($url["path"], 1);

	$conn = mysqli_connect($server, $username, $password, $db);
	
	if (mysqli_connect_errno()) {
    		$response."Connect failed: %s\n".mysqli_connect_error();
    	
	}
	
	if (mysqli_ping($conn)) {
	    $response."Our connection is ok!\n";
	} else {
	    $response."Error: %s\n".mysqli_error();
	}
	
	mysqli_close($conn)*/
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
