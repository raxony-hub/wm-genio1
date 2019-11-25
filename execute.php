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
	
	$querry = "SELECT * FROM `Utenti` WHERE `Nome` = '$username'";
	$Result = mysqli_query($link,$querry);
	if( !$Result )
	{
		$response .= "\nerrore query (select): ".mysqli_error($Result);
	}
	
	if (mysqli_num_rows($Result) == 0)
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
	$response = "Bello, un nuovo contatto! Ti farò delle domande per registrare i suoi dati. Se non sai cosa rispondere, scrivi 'no'. Dimmi il nome e cognome:";

	//recupero il codice univoco del "cliente" che poi userò per registrarlo
	
	$link1 = mysqli_connect("remotemysql.com:3306", "bfFvkAb7fr", "WoC7xGtmgK", "bfFvkAb7fr");
	if (mysqli_connect_errno()) {
		$response .= "Connect failed: %s\n".mysqli_connect_error();

	}

	if (mysqli_ping($link1)) {
	    $response .= "\n\nOur connection is ok!\n";
	} else {
	    $response .= "Error: \n".mysqli_error($link1);
	}
	
	$querry1 = "SELECT * FROM `Utenti` WHERE `Nome` = 'tony'";
	$Result1 = mysqli_query($link1,$querry1);
	if( !$Result1 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($Result1);
	}
	
	$row = mysqli_fetch_array($Result1, MYSQLI_NUM);
	$codice_cliente = $row[1] + 1;
	$response .= "\n codice cliente =".($row[1] + 1);
	
	//Aggiorno il codice_utente nuovo nuovo
	$querry2 = "UPDATE `Utenti` SET `N_contatto` = '$codice_cliente' WHERE `Utenti`.`Nome` = 'tony'";
	$Result2 = mysqli_query($link1,$querry2);
	if( !$Result2 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($Result2);
	}
	
	$querry3 = "UPDATE `Utenti` SET `N_contatto` = '$codice_cliente', `stato` = 'ins_nome' WHERE `Utenti`.`Nome` = '$username'";
	$Result3 = mysqli_query($link1,$querry3);
	if( !$Result3 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($Result3);
	}
		
	$response .= "\n codice aggiornato correttamente";
	
	mysqli_close($link1);

}
elseif(strpos($text, "/elenco") === 0)
{
	$response = "ti dò l'elenco dei da richiamare del giorno".substr($text,8);
}
else
{
	//$response = "Comando non valido!";
	//recupero lo stato del volantinatore per capire csa sta facendo:
	
	$link = mysqli_connect("remotemysql.com:3306", "bfFvkAb7fr", "WoC7xGtmgK", "bfFvkAb7fr");
	if (mysqli_connect_errno()) {
		$response .= "Connect failed: %s\n".mysqli_connect_error();

	}

	if (mysqli_ping($link)) {
	    $response .= "\n\nOur connection is ok!\n";
	} else {
	    $response .= "Error: \n".mysqli_error($link);
	}
	
	$stato_volantinatore = "";
	$codice_cliente = 0;
	$querry = "SELECT * FROM `Utenti` WHERE `Nome` = '$username'";
	$Result = mysqli_query($link,$querry);
	/*if( !$Result )
	{
		$response .= "\nerrore query (select): ".mysqli_error($Result);
		break;
	}
	
	$row = mysqli_fetch_array($Result, MYSQLI_NUM);
		
	$codice_cliente = $row[1];
	$stato_volantinatore = $row[2];
	
	
	/*switch($stato_volantinatore)
	{
		case "ins_nome":
			$querry = "INSERT INTO `Contatti` (`N_contatto`, `utente`, `data_ins`, `nom_cogn`, `numerone`, `note_v`, `data_d`, `data_r`, `ora_r`, `note_r`, `integrazione`) VALUES ('$codice_cliente', '$text', 'date(\"Y-m-d\")', '', NULL, '', NULL, NULL, NULL, '', NULL);";
			$Result = mysqli_query($link,$querry);
			if( !$Result )
			{
				$response .= "\nerrore query (select): ".mysqli_error($Result);
			}
			
			$response .= "\nnome inserito correttamente! Ora inserisci la data della demo a cui l'hai invitato nel formato AAAA-MM-GG (Esempio: date(\"Y-m-d\")):";
			
			break;
		default:
			$response .= "\n\nstato utente sconosciuto";
			break;
	}*/

	mysqli_close($link);
}


$parameters = array('chat_id' => $chatId, "text" => $response);
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
