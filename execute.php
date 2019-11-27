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
	$response = "Ciao $firstname, benvenuto nel nuovo WM di Beppe (Tony)! Usa il comando /inserisci per inserire un nuovo fantastico contatto, il comando /elenco per vedere chi hai da richiamare, il comando /esito per mettere o modificare l'esito di una contatto, il comando /ore per registrare il numero di ore che hai volantinato, il comando /esito per avere il riassunto dell'andamento del volantinaggio. :)";

	$link = mysqli_connect("remotemysql.com:3306", "bfFvkAb7fr", "WoC7xGtmgK", "bfFvkAb7fr");
	if (mysqli_connect_errno()) {
		$response .= "Connect failed: %s\n".mysqli_connect_error();

	}

	if (mysqli_ping($link)) {
	    //$response .= "\n\nOur connection is ok!\n";
	} else {
	    $response .= "Error: \n".mysqli_error($link);
	}
	
	$querry = "SELECT * FROM `Utenti` WHERE `Nome` = '$username'";
	$Result = mysqli_query($link,$querry);
	if( !$Result )
	{
		$response .= "\nerrore query (select): ".mysqli_error($link);
	}
	
	if (mysqli_num_rows($Result) == 0)
	{
		//creo un nuovo utente;
		$querry = "INSERT INTO `Utenti` (`Nome`, `N_contatto`, `stato`) VALUES ('$username', '0', 'nuovo')";
		
		$Result = mysqli_query($link,$querry);
		if( !$Result )
		{
			$response .= "\nerrore query (insert): ".mysqli_error($link);
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
	    //$response .= "\n\nOur connection is ok!\n";
	} else {
	    $response .= "Error: \n".mysqli_error($link1);
	}
	
	$querry1 = "SELECT * FROM `Utenti` WHERE `Nome` = 'tony'";
	$Result1 = mysqli_query($link1,$querry1);
	if( !$Result1 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($link1);
	}
	
	$row = mysqli_fetch_array($Result1, MYSQLI_NUM);
	$codice_cliente = $row[1] + 1;
	$response .= "\n codice cliente =".($row[1] + 1);
	
	//Aggiorno il codice_utente nuovo nuovo
	$querry2 = "UPDATE `Utenti` SET `N_contatto` = '$codice_cliente' WHERE `Utenti`.`Nome` = 'tony'";
	$Result2 = mysqli_query($link1,$querry2);
	if( !$Result2 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($link1);
	}
	
	$querry3 = "UPDATE `Utenti` SET `N_contatto` = '$codice_cliente', `stato` = 'ins_nome' WHERE `Utenti`.`Nome` = '$username'";
	$Result3 = mysqli_query($link1,$querry3);
	if( !$Result3 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($link1);
	}
		
	$response .= "\n codice aggiornato correttamente";
	
	mysqli_close($link1);

}
elseif(strpos($text, "/elenco") === 0)
{
	//modifico stato volantinatore in "elenco".
	
	$link = mysqli_connect("remotemysql.com:3306", "bfFvkAb7fr", "WoC7xGtmgK", "bfFvkAb7fr");
	if (mysqli_connect_errno()) {
		$response .= "Connect failed: %s\n".mysqli_connect_error();
	}
	if (mysqli_ping($link)) {
	    //$response .= "\n\nOur connection is ok!\n";
	} else {
	    $response .= "Error: \n".mysqli_error($link);
	}
	
	$querry3 = "UPDATE `Utenti` SET `stato` = 'elenco' WHERE `Utenti`.`Nome` = '$username'";
	$Result3 = mysqli_query($link,$querry3);
	if( !$Result3 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($link);
	}

	$data_oggi = date("Y-m-d");
	$response .= "\ninserisci la data di quendo vuoi l'elenco dei contatti da richiamare nel formato AAAA-MM-GG (Esempio: $data_oggi). Se vuoi i contatti che devi richiamare oggi, scrivi \"oggi\" in minuscolo:";

	
	mysqli_close($link);
}
elseif(strpos($text, "/esito") === 0)
{
	//modifico stato volantinatore in "esito".
	
	$link = mysqli_connect("remotemysql.com:3306", "bfFvkAb7fr", "WoC7xGtmgK", "bfFvkAb7fr");
	if (mysqli_connect_errno()) {
		$response .= "Connect failed: %s\n".mysqli_connect_error();
	}
	if (mysqli_ping($link)) {
	    //$response .= "\n\nOur connection is ok!\n";
	} else {
	    $response .= "Error: \n".mysqli_error($link);
	}
	
	$querry3 = "UPDATE `Utenti` SET `stato` = 'esito' WHERE `Utenti`.`Nome` = '$username'";
	$Result3 = mysqli_query($link,$querry3);
	if( !$Result3 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($link);
	}

	$data_oggi = date("Y-m-d");
	$response .= "\nInserisci il nome e cognome del contatto di cui vuoi modificare l'esito:";

	
	mysqli_close($link);
}
elseif(strpos($text, "/ore") === 0)
{
	//modifico stato volantinatore in "esito".
	
	$link = mysqli_connect("remotemysql.com:3306", "bfFvkAb7fr", "WoC7xGtmgK", "bfFvkAb7fr");
	if (mysqli_connect_errno()) {
		$response .= "Connect failed: %s\n".mysqli_connect_error();
	}
	if (mysqli_ping($link)) {
	    //$response .= "\n\nOur connection is ok!\n";
	} else {
	    $response .= "Error: \n".mysqli_error($link);
	}
	
	$querry3 = "UPDATE `Utenti` SET `stato` = 'ore' WHERE `Utenti`.`Nome` = '$username'";
	$Result3 = mysqli_query($link,$querry3);
	if( !$Result3 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($link);
	}

	$response .= "\nInserisci la data di quando vuoi inserire il numero di ore che hai volantinato (se riguarda la giornata di oggi, scrivi \"oggi\") quindi lasciare uno spazio e inserire il numero di ore in cui hai volantinato nel formato HH,MM (esempio 4,5):";
	
	mysqli_close($link);
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
	    //$response .= "\n\nOur connection is ok!\n";
	} else {
	    $response .= "Error: \n".mysqli_error($link);
	}
	
	$stato_volantinatore = "";
	$codice_cliente = 0;
	$querry = "SELECT * FROM `Utenti` WHERE `Nome` = '$username'";
	$Result = mysqli_query($link,$querry);
	if( !$Result )
	{
		$response .= "\nerrore query (select): ".mysqli_error($link);
	}
	
	$row = mysqli_fetch_array($Result, MYSQLI_NUM);
		
	$codice_cliente = $row[1];
	$stato_volantinatore = $row[2];
	$data_oggi = date("Y-m-d");
	
	switch($stato_volantinatore)
	{
		case "ins_nome":
			$text = strtolower($text);
			$querry = "INSERT INTO `Contatti` (`N_contatto`, `utente`, `data_ins`, `nom_cogn`, `numerone`, `note_v`, `data_d`, `data_r`, `ora_r`, `note_r`, `integrazione`, `esito`) VALUES ('$codice_cliente', '$username', '$data_oggi', '$text', NULL, '', NULL, NULL, NULL, '', NULL, '');";
			$Result = mysqli_query($link,$querry);
			if( !$Result )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$querry3 = "UPDATE `Utenti` SET `stato` = 'ins_data_demo' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$response .= "\nnome inserito correttamente! Ora inserisci la data della demo a cui l'hai invitato nel formato AAAA-MM-GG (Esempio: $data_oggi):";
			
			break;
		case "ins_data_demo":
			//modifico stato volantinatore.
			$querry3 = "UPDATE `Utenti` SET `stato` = 'ins_data_ric' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}

			$response .= "\n codice aggiornato correttamente";
			
			// inserisco i dati.
			
			$querry3 = "UPDATE `Contatti` SET `data_d` = '$text' WHERE `Contatti`.`N_contatto` = $codice_cliente";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$response .= "\ndata della demo inserita correttamente! Ora inserisci la data del richiamo a cui l'hai invitato nel formato AAAA-MM-GG (Esempio: $data_oggi):";
			
			break;
		case "ins_data_ric":
			//modifico stato volantinatore.
			$querry3 = "UPDATE `Utenti` SET `stato` = 'ins_ora_ric' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			// inserisco i dati.
			
			$querry3 = "UPDATE `Contatti` SET `data_r` = '$text' WHERE `Contatti`.`N_contatto` = $codice_cliente";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}

			$response .= "\ndata della demo inserita correttamente! Ora inserisci l'ora del richiamo nel formato HH,MM (Esempio: 15,30):";
						
			break;
		case "ins_ora_ric":
			//modifico stato volantinatore.
			$querry3 = "UPDATE `Utenti` SET `stato` = 'ins_integr' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			// inserisco i dati.
			
			$text = str_replace(',', '.', $text);
			
			$querry3 = "UPDATE `Contatti` SET `ora_r` = '$text' WHERE `Contatti`.`N_contatto` = $codice_cliente";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}

			$response .= "\norario della demo inserito correttamente! Questo contatto è un'integrazione di un altro contatto? (sì/no)";
						
			break;
		case "ins_integr":
			//modifico stato volantinatore.
			$querry3 = "UPDATE `Utenti` SET `stato` = 'ins_fine' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			// inserisco i dati.
			if(strtolower($text) === "sì"|| strtolower($text) === "si")
			{
				$querry3 = "UPDATE `Contatti` SET `integrazione` = '1' WHERE `Contatti`.`N_contatto` = $codice_cliente";
			} else {
				$querry3 = "UPDATE `Contatti` SET `integrazione` = '0' WHERE `Contatti`.`N_contatto` = $codice_cliente";

			}
			
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}

			$response .= "\nContatto inserito correttamente. Digita /start per effettuare una nuova azione o /inserisci per inserire un nuovo contatto.";
						
			break;
		case "elenco":
			if(strtolower($text) === "oggi")
			{
				$querry = "SELECT * FROM `Contatti` WHERE `utente` = '$username' AND `data_r` = '$data_oggi'";
			} else {
				$querry = "SELECT * FROM `Contatti` WHERE `utente` = '$username' AND `data_r` = '$text'";
			}
			
			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$response .= "\nPersone da richiamare:\n";
			while( $row = mysqli_fetch_array($Result3, MYSQLI_NUM) )
			{
				$ora_r = str_replace('.', ':', $row[8]);
				$response .= "\n$row[3]\ndata demo: $row[6]\ndata richiamo: $row[7]\nora richiamo: $ora_r\n";
			}
			
			$querry3 = "UPDATE `Utenti` SET `stato` = 'elenco_fine' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$response .= "\nDigita /start per effettuare una nuova azione o /inserisci per inserire un nuovo contatto.";
			
			break;
		case "esito":
			$text = strtolower($text);
			$querry = "SELECT * FROM `Contatti` WHERE `utente` = '$username' AND `nom_cogn` = '$text'";
			
			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				//$response .= "\nerrore query (select): ".mysqli_error($link);
				$response .= "\nquesto utente non sembra esistere, riprova con un altro nome e cognome:";
			}
			
			$row = mysqli_fetch_array($Result3, MYSQLI_NUM);
			$esito = "";
			switch($row[11])
			{
				case "nf":
					$esito = "numero falso";
					break;
				case "nr":
					$esito = "nessuna risposta";
					break;
				case "ni":
					$esito = "non interessato";
					break;
				case "nd":
					$esito = "genitore non interessato";
					break;
				case "r":
					$esito = "rimandato app";
					break;
				case "c":
					$esito = "confermato";
					break;
				case "p":
					$esito = "presente demo";
					break;
				default:
					break;
			}
			
			$codice_cont = $row[0];
			
			$response .= "\nIl contatto $row[3] ha come esito attuale: $esito. Inserisci il nuovo esito (NF - numero falso, NR - nessuna risposta, NI - non interessato, ND - genitore non interessato, R - rimandato app, C - confermato, P - presente alla demo):";

			
			$querry3 = "UPDATE `Utenti` SET `N_contatto` = '$codice_cont', `stato` = 'esito_nuovo' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			break;
			
		case "esito_nuovo":
			//recupero il codice del contatto da modificare.
			
			$querry = "SELECT * FROM `Utenti` WHERE `Nome` = '$username'";
			
			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$row = mysqli_fetch_array($Result3, MYSQLI_NUM);
			$codice_cont = $row[1];
			
			$text = strtolower($text);
			
			$querry3 = "UPDATE `Contatti` SET `esito` = '$text' WHERE `Contatti`.`N_contatto` = $codice_cont";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$querry3 = "UPDATE `Utenti` SET `stato` = 'esito_fine' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$response .= "\nEsito contatto modificato correttamente! Digita /start per effettuare una nuova azione o /inserisci per inserire un nuovo contatto.";
			
			break;
		case "ore":
			$data = "";
			$ore_vol = "0.0";
			if(strpos(strtolower($text), "oggi") === 0)
			{
				$data = date("Y-m-d");
				$ore_vol = str_replace(',', '.',substr($text, 4));
			} else {
				$data = substr($text, 0, 10);
				$ore_vol = str_replace(',', '.',substr($text, 10));
			}
			
			$querry = "INSERT INTO `Ore_vol` (`Nome_vol`, `data`, `ore`) VALUES ('$username', '$data', '$ore_vol');";
			$response .= "\n$querry";
			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$response .= "\nOre lavorate inserite correttamente. Digita /start per effettuare una nuova azione o /inserisci per inserire un nuovo contatto.";
			
			break;
		default:
			$response .= "\n\nstato utente sconosciuto";
			break;
	}

	mysqli_close($link);
}


$parameters = array('chat_id' => $chatId, "text" => $response);
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
