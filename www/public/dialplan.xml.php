<?php
$time_start = microtime(true); 

$vars = '';
$GLOBALS['caller_id_num'] = isset($_POST['Caller-Caller-ID-Number']) ? $_POST['Caller-Caller-ID-Number'] : '';
$GLOBALS['destination'] = isset($_POST['Caller-Destination-Number']) ? $_POST['Caller-Destination-Number'] : '';
$GLOBALS['uuid'] = isset($_POST['Unique-ID']) ? substr($_POST['Unique-ID'], 0, 8) : 'uuid';
$GLOBALS['pgsql'] = "host=PGSQL_HOST dbname=switch user=PGSQL_USER password=PGSQL_PASS connect_timeout=5";

////////////////////////////////////////////////////
//
function ContextPublic($xmlw)
{
	//start the context
	$xmlw -> startElement('context');
	$xmlw -> writeAttribute('name', 'public');

        $link = pg_connect($GLOBALS["pgsql"]);
       if (!$link) { 
            Extention503($xmlw);
            $xmlw -> endElement();
            die("");
        }
		//or die('Could not connect: ' . pg_last_error());
	// Выполнение SQL запроса
	$query = 'SELECT
		"global_prefix",
		"id",
		"local_prefix",
		"agent_prefix",
		"del_prefix",
		"name",
		"show_getaways",
		"number_length"
	FROM "Destinations" WHERE "active" = 1 
		AND \''.$GLOBALS['destination'].'\' ~ ("agent_prefix" || "global_prefix" || \'\\d{\' || "number_length" || \'}\' ) 
		AND ( 
			NOT EXISTS (SELECT "b".id FROM "NumberList" "b"
                                        WHERE "b"."callee_id_number" LIKE \''.$GLOBALS['destination'].'\'
                                        AND "b"."destinations" = "Destinations"."id" )
			AND 
			NOT EXISTS (SELECT "a".id FROM "NumberList" "a" 
					WHERE "a"."caller_id_number" LIKE \''.$GLOBALS['caller_id_num'].'\' 
					AND "a"."destinations" = "Destinations"."id" ) 
		)';

	error_log($query, 0);
	$result = pg_query($link, $query) or die('Ошибка запроса: ' . pg_last_error());

	while ($destinations = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		error_log("[".$GLOBALS['uuid']."] Destination: ".$destinations['name'], 0);
		//start an extension
		$xmlw -> startElement('extension');
		$xmlw -> writeAttribute('name', $destinations['name']);
		//write the condition to match on
		$xmlw -> startElement('condition');
		$xmlw -> writeAttribute('field', 'destination_number');
		$xmlw -> writeAttribute('expression', '^'.$destinations["agent_prefix"].'('.$destinations["global_prefix"].'(\d{'.$destinations["number_length"].'}))$');
		//<action>
        	$xmlw -> startElement('action');
                $xmlw -> writeAttribute('application', 'set');
                $xmlw -> writeAttribute('data', 'continue_on_fail=true');
                $xmlw -> endElement();	
		//<action>
                $xmlw -> startElement('action');
                $xmlw -> writeAttribute('application', 'set');
                $xmlw -> writeAttribute('data', 'hangup_after_bridge=true');
                $xmlw -> endElement();
                //<action>
		//<action>
                $xmlw -> startElement('action');
                $xmlw -> writeAttribute('application', 'set');
                $xmlw -> writeAttribute('data', 'call_timeout=30');
                $xmlw -> endElement();
                //<action>

		$sql = 'SELECT
			"id",
			"ip",
			"mask",
			"port",
			"sip_profile",
			"type_id",
			"user_id",
			"delay_rnd",
			"delay_from",
			"delay_to",
			"concurrent",
			"selected",
			"bridge_string",
			"call_timeout"
		FROM vw_gw 
       			WHERE destinations = '.$destinations["id"].'
       			LIMIT '.$destinations["show_getaways"];

		//FROM select_and_update_gw('.$destinations["id"].', '.$destinations["show_getaways"].') ';
		error_log($sql, 0);
		$r = pg_query($link, $sql) or die('Ошибка запроса: ' . pg_last_error());
	// samael patch
	// 2014-09-09
	error_log("[".$GLOBALS['uuid']."] pg_num_rows: ".pg_num_rows($r), 0);
	if (pg_num_rows($r) > 0) {
		while ($gw = pg_fetch_array($r, null, PGSQL_ASSOC)) {
			if ($gw["delay_rnd"] == '1') {
				$uq_gw = ' UPDATE "Getaways" SET';
				if (!$gw["delay_from"] or !$gw["delay_to"]) {
					$uq_gw .= ' "delay"='.rand(20, 400);
				} else {
					$uq_gw .= ' "delay"='.rand($gw["delay_from"], $gw["delay_to"]);
				}
				$uq_gw .= ' WHERE "id" = '.$gw["id"];
				pg_query($link, $uq_gw) or die('Ошибка запроса: ' . pg_last_error());
			}
			//<action>
			$xmlw -> startElement('action');
			$xmlw -> writeAttribute('application', 'set');
			$xmlw -> writeAttribute('data', 'gw_id='.$gw["id"]);
			$xmlw -> endElement();
			//</action>
			//<action>
			$xmlw -> startElement('action');
			$xmlw -> writeAttribute('application', 'set');
			$xmlw -> writeAttribute('data', 'user_id='.$gw['user_id']);
			$xmlw -> endElement();
			//</action>
			//<action>
			$xmlw -> startElement('action');
			$xmlw -> writeAttribute('application', 'set');
			$xmlw -> writeAttribute('data', 'destination_id='.$destinations['id']);
			$xmlw -> endElement();
			//</action>
			if ($gw['call_timeout'] > 0) {
				//<action>
                        	$xmlw -> startElement('action');
                        	$xmlw -> writeAttribute('application', 'set');
                        	$xmlw -> writeAttribute('data', 'call_timeout='.$gw['call_timeout']);
                        	$xmlw -> endElement();
                        	//</action>
			} else {
                                $xmlw -> startElement('action');
                                $xmlw -> writeAttribute('application', 'set');
                                $xmlw -> writeAttribute('data', 'call_timeout=30');
                                $xmlw -> endElement();
                                //</action>
			}
			//<action>
			$xmlw -> startElement('action');
			$xmlw -> writeAttribute('application', 'bridge');
			//$xmlw -> writeAttribute('application', 'limit_execute');
			if ($gw["bridge_string"]) {
				$dest_num = $gw["bridge_string"];
			} else {
				$dest_num = $destinations['del_prefix']=='1' ? '$2' : '+$1'; 
			}
			if ($gw["type_id"] == '3' and $gw["port"] == '5060' ) {
				//$bridgeString = 'sofia/gateway/'.$gw["ip"].'/'.$gw["mask"].$dest_num;
				$bridgeString = 'sofia/'.$gw["sip_profile"].'/'.$gw["mask"].$dest_num.'@'.$gw["ip"];
			} elseif ($gw["type_id"] == '1') {
				//$bridgeString = 'sofia/'.$gw["sip_profile"].'/'.$gw["mask"].$dest_num.'@'.$gw["ip"].':'.$gw["port"];
				$bridgeString = '${regex(${sofia_contact(*/${gw_id}@${dialed_domain})}|(^\w+/\w+)/|%1)}/sip:'.$gw["mask"].$dest_num.'@${regex(${sofia_contact(*/${gw_id}@${dialed_domain})}|(\d+.\d+.\d+.\d+:\d+.*)|%1)}';
			} else {
				$bridgeString = 'sofia/'.$gw["sip_profile"].'/'.$gw["mask"].$dest_num.'@'.$gw["ip"].':'.$gw["port"];
			}
			//$xmlw -> writeAttribute('data', 'db out '.$gw["id"].' '.$gw["concurrent"].' bridge '.$bridgeString);
			$xmlw -> writeAttribute('data', $bridgeString);
			$xmlw -> endElement();
			//</action>
		}
	// samael patch
	// 2014-09-09
	} 

	//<action>
	$xmlw -> startElement('action');
        $xmlw -> writeAttribute('application', 'log');
        $xmlw -> writeAttribute('data', 'INFO: NORMAL_CIRCUIT_CONGESTION');
        $xmlw -> endElement();
	$xmlw -> startElement('action');
	$xmlw -> writeAttribute('application', 'hangup');
	$xmlw -> writeAttribute('data', '503');
        $xmlw -> endElement();
        //</action>

		pg_free_result($r);
		//</condition>
		$xmlw -> endElement();
		// </extension>
		$xmlw -> endElement();
	}

	// 503 extention
	Extention503($xmlw);
	// </context>
	$xmlw -> endElement();

	pg_free_result($result);
	pg_close($link);
}
// FAS
function ContextFas($xmlw)
{
	$xmlw -> startElement('context');
	$xmlw -> writeAttribute('name', 'fas');

	$link = pg_connect($GLOBALS["pgsql"]) 
		or die('Could not connect: ' . pg_last_error());
	// Выполнение SQL запроса
	$query = 'SELECT "id",
		"after_ansfer",
		"before_ansfer",
		"global_prefix",
		"name",
		"number_length",
		"tone_stream",
		"random_pdd",
		"before_ansfer_from",
		"before_ansfer_to",
		"recording_file",
		"tone_stream_duration"
	FROM "fas" WHERE "active" = 1 
		AND \''.$GLOBALS['destination'].'\' ~ ( "global_prefix" || \'\\d{\' || "number_length" || \'}\' ) ';
	$result_fas = pg_query($link, $query) or die('Ошибка запроса: ' . pg_last_error());

	while ($fas = pg_fetch_array($result_fas, null, PGSQL_ASSOC)) {

	if ($fas["random_pdd"]) {
		$before_ansfer = rand($fas["before_ansfer_from"], $fas["before_ansfer_to"]);
	} else {
		$before_ansfer = $fas["before_ansfer"];
	}

	if ($fas["recording_file"]) { 
		$tone_stream = 'tone_stream://L='.$fas["tone_stream_duration"].';'.$fas["tone_stream"];
	} else {
		$tone_stream = 'tone_stream://'.$fas["tone_stream"].';loops=-1';
	}

	//start an extension
	$xmlw -> startElement('extension');
	$xmlw -> writeAttribute('name', $fas['name']);
	//write the condition to match on
	$xmlw -> startElement('condition');
	$xmlw -> writeAttribute('field', 'destination_number');
	$xmlw -> writeAttribute('expression', '^'.$fas["global_prefix"].'(\d{'.$fas["number_length"].'})$');

		//<action>
	$xmlw -> startElement('action');
	$xmlw -> writeAttribute('application', 'set');
	$xmlw -> writeAttribute('data', 'destination_id='.$fas['id']);
	$xmlw -> endElement();
		//</action>
		//<action>
	$xmlw -> startElement('action');
	$xmlw -> writeAttribute('application', 'sleep');
	$xmlw -> writeAttribute('data', $before_ansfer);
	$xmlw -> endElement();
		//</action>
		//<action>
	$xmlw -> startElement('action');
	$xmlw -> writeAttribute('application', 'answer');
	$xmlw -> endElement();
		//</action>
		//<action>
	$xmlw -> startElement('action');
	$xmlw -> writeAttribute('application', 'sleep');
	$xmlw -> writeAttribute('data', $fas["after_ansfer"]);
	$xmlw -> endElement();
		//</action>
		//<action>
	$xmlw -> startElement('action');
	$xmlw -> writeAttribute('application', 'playback');
	$xmlw -> writeAttribute('data', $tone_stream);
	$xmlw -> endElement();
		//</action>
	if ($fas["recording_file"]) { 
		//<action>
	$xmlw -> startElement('action');
	$xmlw -> writeAttribute('application', 'playback');
	$xmlw -> writeAttribute('data', '/data/www/public/mp3/'.$fas["recording_file"]);
	$xmlw -> endElement();
		//</action>
	}
	//</condition>
	$xmlw -> endElement();
	// </extension>
	$xmlw -> endElement();
	}

	// </context>
	$xmlw -> endElement();

	pg_free_result($result_fas);
	pg_close($link);

}
// FUCK
function ContextFuck($xmlw)
{
	//start the context
	$xmlw -> startElement('context');
	$xmlw -> writeAttribute('name', 'fuck');
	//start an extension
	$xmlw -> startElement('extension');
	$xmlw -> writeAttribute('name', 'go to fucker');
	//write the condition to match on
	$xmlw -> startElement('condition');
	//<action>
	$xmlw -> startElement('action');
	$xmlw -> writeAttribute('application', 'bridge');
	$xmlw -> writeAttribute('data', 'sofia/openvpn/${destination_number}@10.8.0.92');
	$xmlw -> endElement();
	//</action>
	//</condition>
	$xmlw -> endElement();
	// </extension>
	$xmlw -> endElement();
	// </context>
	$xmlw -> endElement();
}
//
// TG
function ContextTG($xmlw)
{
        //start the context
        $xmlw -> startElement('context');
        $xmlw -> writeAttribute('name', 'tg');
        //start an extension
        $xmlw -> startElement('extension');
        $xmlw -> writeAttribute('name', 'go to tg');
        //write the condition to match on
        $xmlw -> startElement('condition');
        //<action>
        $xmlw -> startElement('action');
        $xmlw -> writeAttribute('application', 'bridge');
        $xmlw -> writeAttribute('data', 'sofia/fas/${destination_number}@85.10.219.42');
        $xmlw -> endElement();
        //</action>
        //</condition>
        $xmlw -> endElement();
        // </extension>
        $xmlw -> endElement();
        // </context>
        $xmlw -> endElement();
}
// switch
function ContextSwitch($xmlw)
{
	//start the context
	$xmlw -> startElement('context');
	$xmlw -> writeAttribute('name', 'switch');
	//start an extension
	$xmlw -> startElement('extension');
	$xmlw -> writeAttribute('name', 'go to switch');
	//write the condition to match on
	$xmlw -> startElement('condition');
	//<action>
	$xmlw -> startElement('action');
	$xmlw -> writeAttribute('application', 'bridge');
	$xmlw -> writeAttribute('data', 'sofia/openvpn/${destination_number}@10.8.0.8');
	$xmlw -> endElement();
	//</action>
	//</condition>
	$xmlw -> endElement();
	// </extension>
	$xmlw -> endElement();
	// </context>
	$xmlw -> endElement();
}
// extention cps
// <action application="limit" data="hash ${sip_received_ip} ${destination_number} ${calls_per_second}/1 handle_over_limit XML over_limit_actions" />
function ExtentionCPS($xmlw)
{
        //start an extension
        $xmlw -> startElement('extension');
        $xmlw -> writeAttribute('name', 'CPS');
        $xmlw -> writeAttribute('continue', 'true');
        //write the condition to match on
        $xmlw -> startElement('condition');
        //<action>
        $xmlw -> startElement('action');
        $xmlw -> writeAttribute('application', 'limit');
        $xmlw -> writeAttribute('data', 'hash ${sip_received_ip} ${destination_number} ${calls_per_second}/1 over_cps XML CPS');
        $xmlw -> endElement();
        //</action>
        //</condition>
        $xmlw -> endElement();
        // </extension>
}
// END
// extention 503
function Extention503($xmlw)
{
        //start an extension
        $xmlw -> startElement('extension');
        $xmlw -> writeAttribute('name', 'Hangup');
        //write the condition to match on
        $xmlw -> startElement('condition');
        //<action>
        $xmlw -> startElement('action');
        $xmlw -> writeAttribute('application', 'hangup');
        $xmlw -> writeAttribute('data', '503');
        $xmlw -> endElement();
        //</action>
        //</condition>
        $xmlw -> endElement();
        // </extension>
}
// END
////////////////////////////////////////////////////

header('Content-Type: text/xml');

$xmlw = new XMLWriter();
$xmlw -> openMemory();
$xmlw -> setIndent(true);
$xmlw -> setIndentString('  ');
$xmlw -> startDocument('1.0', 'UTF-8', 'no');

$xmlw -> startElement('document');
$xmlw -> writeAttribute('type', 'freeswitch/xml');

$xmlw -> startElement('section');
$xmlw -> writeAttribute('name', 'dialplan');
$xmlw -> writeAttribute('description', 'Switch Dialplan');

// Output
switch (isset($_POST['Caller-Context']) ? $_POST['Caller-Context'] : '') {
	case 'public':
		ContextPublic($xmlw);
		break;

	case 'switch':
		ContextSwitch($xmlw);
		break;

	case 'fas':
		ContextFas($xmlw);
		break;
	
	case 'fuck':
		ContextFuck($xmlw);
		break;	
	
	case 'tg':
                ContextTG($xmlw);
                break;

	default:
		//start the context
		$xmlw -> startElement('context');
		$xmlw -> writeAttribute('name', isset($_POST['Caller-Context']) ? $_POST['Caller-Context'] : 'public');
		//start an extension
		Extention503($xmlw);
		// </context>
		$xmlw -> endElement();
		break;
}

//</section>
$xmlw -> endElement();
//</document>
$xmlw -> endElement();
$xmlw -> endElement();
$all_xml = $xmlw -> outputMemory();
echo $all_xml;
error_log($all_xml );

$time_end = microtime(true);
error_log("[".$GLOBALS['uuid']."] Total Execution Time: ".($time_end - $time_start)." Sec", 0);

?>
