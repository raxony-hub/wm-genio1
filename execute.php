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
$firstname = strtolower($firstname);
$lastname = strtolower($lastname);
$username = strtolower($username);

//se l'username non è stato impostato, uso il nome e cognome del volantinatore come username.
if($username === "")
{
	$username = $firstname."_".$lastname;
}

header("Content-Type: application/json");

$response = '';

if(strpos($text, "/start") === 0 || $text=="ciao")
{
	$response = "Ciao $firstname, benvenuto nel nuovo WM di Beppe (Tony)! Usa il comando /inserisci per inserire un nuovo fantastico contatto, il comando /elenco per vedere chi hai da richiamare, il comando /esito per mettere o modificare l'esito di una contatto, il comando /ore per registrare il numero di ore che hai volantinato, il comando /analisi per avere il riassunto dell'andamento del tuo volantinaggio, il comando /istru per avere il riassunto del volantinaggio di un POT. :)";

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
	$response = "Bello, un nuovo contatto! Ti farò delle domande per registrare i suoi dati. Se non sai cosa rispondere, scrivi <b>'no'</b>. Dimmi il <b>nome</b> e <b>cognome</b>:";

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
	//$response .= "\n codice cliente =".($row[1] + 1);
	
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
		
	//$response .= "\n codice aggiornato correttamente";
	
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
	$response .= "\ninserisci la <b>data</b> di quendo vuoi l'elenco dei contatti da richiamare nel formato <b>AAAA-MM-GG</b> (Esempio: $data_oggi). Se vuoi i contatti che devi richiamare oggi, scrivi \"oggi\" in minuscolo:";

	
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
	$response .= "\nInserisci il <b>nome</b> e <b>cognome</b> del contatto di cui vuoi modificare l'esito:";

	
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

	$response .= "\nInserisci la <b>data</b> di quando vuoi inserire il numero di <b>ore</b> che hai volantinato (se riguarda la giornata di oggi, scrivi \"oggi\") quindi <b>lasciare uno spazio</b> e inserire il numero di ore in cui hai volantinato nel formato HH,MM (esempio 4,5):";
	
	mysqli_close($link);
}
elseif(strpos($text, "/analisi") === 0)
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
	
	$querry3 = "UPDATE `Utenti` SET `stato` = 'analisi' WHERE `Utenti`.`Nome` = '$username'";
	$Result3 = mysqli_query($link,$querry3);
	if( !$Result3 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($link);
	}

	$response .= "\nInserisci le date di <b>inizio</b> e di <b>fine</b> per l\'analisi del tuo andamento nel volantinaggio, mantenendo sempre il formato YYYY-MM-GG (es. 2019-11-10 2019-11-20):";
	
	mysqli_close($link);
}
elseif(strpos($text, "/istru") === 0)
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
	
	$querry3 = "UPDATE `Utenti` SET `stato` = 'analisi-istru' WHERE `Utenti`.`Nome` = '$username'";
	$Result3 = mysqli_query($link,$querry3);
	if( !$Result3 )
	{
		$response .= "\nerrore query (select): ".mysqli_error($link);
	}

	$response .= "\nInserisci il <b>nome</b> del POT (così come compare su telegram, quindi spazio e le date di <b>inizio</b> e di <b>fine</b> per l\'analisi del tuo andamento nel volantinaggio, mantenendo sempre il formato YYYY-MM-GG (es. $username 2019-11-10 2019-11-20):";
	
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
			
			$response .= "\nnome inserito correttamente! Ora inserisci la <b>data della demo</b> a cui l'hai invitato nel formato AAAA-MM-GG (Esempio: $data_oggi):";
			
			break;
		case "ins_data_demo":
			//modifico stato volantinatore.
			$querry3 = "UPDATE `Utenti` SET `stato` = 'ins_data_ric' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}

			//$response .= "\n codice aggiornato correttamente";
			
			// inserisco i dati se a risoosta non è no
			if( $text === "no"){
				$response .= "\ndata della demo saltata! Ora inserisci la <b>data del richiamo</b> a cui l'hai invitato nel formato AAAA-MM-GG (Esempio: $data_oggi):";

			} else {
			
				$querry3 = "UPDATE `Contatti` SET `data_d` = '$text' WHERE `Contatti`.`N_contatto` = $codice_cliente";
				$Result3 = mysqli_query($link,$querry3);
				if( !$Result3 )
				{
					$response .= "\nerrore query (select): ".mysqli_error($link);
				}

				$response .= "\ndata della demo inserita correttamente! Ora inserisci la <b>data del richiamo</b> a cui l'hai invitato nel formato AAAA-MM-GG (Esempio: $data_oggi):";
			}
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
			if( $text === "no"){
				$response .= "\ndata della demo saltata! Ora inserisci <b>l'ora del richiamo</b> nel formato HH,MM (Esempio: 15,30):";
			
			} else {
			
				$querry3 = "UPDATE `Contatti` SET `data_r` = '$text' WHERE `Contatti`.`N_contatto` = $codice_cliente";
				$Result3 = mysqli_query($link,$querry3);
				if( !$Result3 )
				{
					$response .= "\nerrore query (select): ".mysqli_error($link);
				}

				$response .= "\ndata della demo inserita correttamente! Ora inserisci <b>l'ora del richiamo</b> nel formato HH,MM (Esempio: 15,30):";
			}			
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
			if( $text === "no" ){
				$response .= "\norario della demo saltato! Questo contatto è <b>un'integrazione</b> di un altro contatto? (sì/no)";

			} else {
			
				$text = str_replace(',', '.', $text);

				$querry3 = "UPDATE `Contatti` SET `ora_r` = '$text' WHERE `Contatti`.`N_contatto` = $codice_cliente";
				$Result3 = mysqli_query($link,$querry3);
				if( !$Result3 )
				{
					$response .= "\nerrore query (select): ".mysqli_error($link);
				}

				$response .= "\norario della demo inserito correttamente! Questo contatto è <b>un'integrazione</b> di un altro contatto? (sì/no)";
			}			
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
				case "pa":
					$esito = "porta acconto";
					break;
				case "i":
					$esito = "iscritto";
					break;
				default:
					break;
			}
			
			$codice_cont = $row[0];
			
			$response .= "\nIl contatto $row[3] ha come esito attuale: $esito. Inserisci il <b>nuovo</b> esito (NF - numero falso, NR - nessuna risposta, NI - non interessato, ND - genitore non interessato, R - rimandato app, C - confermato, P - presente alla demo, PA - porta acconto, I - iscritto):";

			
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
				$ore_vol = str_replace(',', '.',substr($text, 5));
			} else {
				$data = substr($text, 0, 10);
				$ore_vol = str_replace(',', '.',substr($text, 11));
			}
			
			$querry = "INSERT INTO `Ore_vol` (`Nome_vol`, `data`, `ore`) VALUES ('$username', '$data', '$ore_vol');";

			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$querry3 = "UPDATE `Utenti` SET `stato` = 'ore_fine' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$response .= "\nOre lavorate inserite correttamente. Digita /start per effettuare una nuova azione o /inserisci per inserire un nuovo contatto.";
			
			break;
		case "analisi":
			$data_inizio = substr($text, 0,10);
			$data_fine = substr($text, 11);
			$ore_volant_tot = 0;
			$tot_cont = 0;
			$tot_nf = 0;
			$tot_nr = 0;
			$tot_ni = 0;
			$tot_nd = 0;
			$tot_r = 0;
			$tot_c = 0;
			$tot_p = 0;
			$tot_i = 0;
			$tot_pa = 0;
			
			//recupero le ore.
			$querry = "SELECT * FROM `Ore_vol` WHERE `Nome_vol` = '$username' AND (`data` BETWEEN '$data_inizio' AND '$data_fine')";
			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			while($row = mysqli_fetch_array($Result3, MYSQLI_NUM))
			{
				$ore_volant_tot = $ore_volant_tot + $row[3];
			}
			
			//recupero gli altri dati.
			$querry = "SELECT * FROM `Contatti` WHERE `utente` = '$username' AND (`data_ins` BETWEEN '$data_inizio' AND '$data_fine') AND `integrazione` = 0";
			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$tot_cont = mysqli_num_rows($Result3);
			
			//recupero gli altri dati.
			$querry = "SELECT * FROM `Contatti` WHERE `utente` = '$username' AND (`data_ins` BETWEEN '$data_inizio' AND '$data_fine')";
			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			while($row = mysqli_fetch_array($Result3, MYSQLI_NUM))
			{
				switch($row[11])
				{
					case "nf":
						$tot_nf = $tot_nf + 1;
						break;
					case "nr":
						$tot_nr = $tot_nr + 1;
						break;
					case "ni":
						$tot_ni = $tot_ni + 1;
						break;
					case "nd":
						$tot_nd = $tot_nd + 1;
						break;
					case "r":
						$tot_r = $tot_r + 1;
						break;
					case "c":
						$tot_c = $tot_c + 1;
						break;
					case "p":
						$tot_p = $tot_p + 1;
						break;
					case "pa":
						$tot_pa = $tot_pa + 1;
						break;
					case "i":
						$tot_i = $tot_i + 1;
						break;
					default:
						break;
				}
			}
			
			$response .= "\nEccoti i tuoi dati per il periodo che va dal $data_inizio al $data_fine:\n- ore volantinate: ".str_replace('.', ',',$ore_volant_tot)."\n- totale volantinati: $tot_cont\n- contatti/ore: ".(floatval($tot_cont) / floatval($ore_volant_to))."\n- tot. n. falso: $tot_nf\n- tot. nr: $tot_nr\n- tot. ni: $tot_ni\n- tot. nd: $tot_nd\n- tot. rimand.: $tot_r\n- tot. conferme demo: $tot_c\n- tot. prese. demo: $tot_p\n- tot. porta acc.: $tot_pa\n- tot. iscritti: $tot_i\n\n";
			
			$querry3 = "UPDATE `Utenti` SET `stato` = 'analisi_fine' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$response .= "\nDigita /start per effettuare una nuova azione o /inserisci per inserire un nuovo contatto.";
			
			break;
		case "analisi-istru":
			
			//trovo il primo spazio per distinguere l'username dalle date.
			$username_pot = strstr($text, " ", true);
			$date_da_lav = strstr($text, " ");
			
			$data_inizio = substr($date_da_lav, 1,10);
			$data_fine = substr($date_da_lav, 12);
			$ore_volant_tot = 0;
			$tot_cont = 0;
			$tot_nf = 0;
			$tot_nr = 0;
			$tot_ni = 0;
			$tot_nd = 0;
			$tot_r = 0;
			$tot_c = 0;
			$tot_p = 0;
			$tot_i = 0;
			$tot_pa = 0;
			
			//recupero le ore.
			$querry = "SELECT * FROM `Ore_vol` WHERE `Nome_vol` = '$username_pot' AND (`data` BETWEEN '$data_inizio' AND '$data_fine')";
			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			while($row = mysqli_fetch_array($Result3, MYSQLI_NUM))
			{
				$ore_volant_tot = $ore_volant_tot + $row[3];
			}
			
			//recupero gli altri dati.
			$querry = "SELECT * FROM `Contatti` WHERE `utente` = '$username_pot' AND (`data_ins` BETWEEN '$data_inizio' AND '$data_fine') AND `integrazione` = 0";
			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$tot_cont = mysqli_num_rows($Result3);
			
			//recupero gli altri dati.
			$querry = "SELECT * FROM `Contatti` WHERE `utente` = '$username_pot' AND (`data_ins` BETWEEN '$data_inizio' AND '$data_fine')";
			
			$Result3 = mysqli_query($link,$querry);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			while($row = mysqli_fetch_array($Result3, MYSQLI_NUM))
			{
				switch($row[11])
				{
					case "nf":
						$tot_nf = $tot_nf + 1;
						break;
					case "nr":
						$tot_nr = $tot_nr + 1;
						break;
					case "ni":
						$tot_ni = $tot_ni + 1;
						break;
					case "nd":
						$tot_nd = $tot_nd + 1;
						break;
					case "r":
						$tot_r = $tot_r + 1;
						break;
					case "c":
						$tot_c = $tot_c + 1;
						break;
					case "p":
						$tot_p = $tot_p + 1;
						break;
					case "pa":
						$tot_pa = $tot_pa + 1;
						break;
					case "i":
						$tot_i = $tot_i + 1;
						break;
					default:
						break;
				}
			}
			
			$response .= "\nEccoti i dati di $username_pot per il periodo che va dal $data_inizio al $data_fine:\n- ore volantinate: ".str_replace('.', ',',$ore_volant_tot)."\n- totale volantinati: $tot_cont\n- contatti/ore: ".(floatval($tot_cont) / floatval($ore_volant_to))."\n- tot. n. falso: $tot_nf\n- tot. nr: $tot_nr\n- tot. ni: $tot_ni\n- tot. nd: $tot_nd\n- tot. rimand.: $tot_r\n- tot. conferme demo: $tot_c\n- tot. prese. demo: $tot_p\n- tot. porta acc.: $tot_pa\n- tot. iscritti: $tot_i\n\n";
			
			$querry3 = "UPDATE `Utenti` SET `stato` = 'analisi_istru_fine' WHERE `Utenti`.`Nome` = '$username'";
			$Result3 = mysqli_query($link,$querry3);
			if( !$Result3 )
			{
				$response .= "\nerrore query (select): ".mysqli_error($link);
			}
			
			$response .= "\nDigita /start per effettuare una nuova azione o /inserisci per inserire un nuovo contatto.";
			
			break;
		default:
			$response .= "\n\nstato utente sconosciuto";
			break;
	}

	mysqli_close($link);
}


$parameters = array('chat_id' => $chatId, "text" => $response);
$parameters["method"] = "sendMessage";
$parameters["parse_mode"] = "html";
echo json_encode($parameters);
