<?php
//file_put_contents('post.txt', print_r($_POST, true));
$vars = '';

$GLOBALS['pgsql'] = "host=PGSQL_HOST dbname=switch user=PGSQL_USER password=PGSQL_PASS connect_timeout=10";

header('Content-Type: text/xml');

$xmlw = new XMLWriter();
$xmlw -> openMemory();
$xmlw -> setIndent(true);
$xmlw -> setIndentString('  ');
$xmlw -> startDocument('1.0', 'UTF-8', 'no');

$xmlw -> startElement('document');
$xmlw -> writeAttribute('type', 'freeswitch/xml');

$xmlw -> startElement('section');
$xmlw -> writeAttribute('name', 'directory');
$xmlw -> writeAttribute('description', 'Switch Directory XML');

$link = pg_connect($GLOBALS["pgsql"]) 
	or die('Could not connect: ' . pg_last_error());
// Выполнение SQL запроса
$query = 'SELECT
	"ip",
	"name"
FROM "sip_profiles" WHERE "name" != \'world\' '; 
$result = pg_query($link, $query) or die('Ошибка запроса: ' . pg_last_error());

while ($sip_profiles = pg_fetch_array($result, null, PGSQL_ASSOC)) {
//start the domain
$xmlw -> startElement('domain');
$xmlw -> writeAttribute('name', $sip_profiles['ip']);
//  <params>
$xmlw -> startElement('params');
//  <param>
$xmlw -> startElement('param');
$xmlw -> writeAttribute('name', 'dial-string');
$xmlw -> writeAttribute('value', '{sip_invite_domain=${dialed_domain},presence_id=${dialed_user}@${dialed_domain}}${sofia_contact(${dialed_user}@${dialed_domain})}');
$xmlw -> endElement();
//</params>
$xmlw -> endElement();

//  <groups>
$xmlw -> startElement('groups');
//  <group>
$xmlw -> startElement('group');
$xmlw -> writeAttribute('name', 'default');

//  <users>
$xmlw -> startElement('users');
//  <user>
$sql = ' SELECT "id"
FROM "Getaways"
WHERE "sip_profile" = \''.$sip_profiles['name'].'\'';

$r = pg_query($link, $sql) or die('Ошибка запроса: ' . pg_last_error());
while ($gw = pg_fetch_array($r, null, PGSQL_ASSOC)) {
	//<user>
	$xmlw -> startElement('user');
	$xmlw -> writeAttribute('id', $gw["id"]);
	//$xmlw -> endElement();
	//</user>
	//<params>
	$xmlw -> startElement('params');
	//<param>
	$xmlw -> startElement('param');
	$xmlw -> writeAttribute('name', 'password');
	$xmlw -> writeAttribute('value', '1234');
	$xmlw -> endElement();
	//</param>
	$xmlw -> endElement();
	//</users>
	$xmlw -> endElement();
}
//  </users>
$xmlw -> endElement();
//  </group>
$xmlw -> endElement();
//  </groups>
$xmlw -> endElement();
// </domain>
$xmlw -> endElement();
}
//</section>
$xmlw -> endElement();
//</document>
$xmlw -> endElement();

echo $xmlw -> outputMemory();

pg_free_result($result);
pg_free_result($r);
pg_close($link);

?>
