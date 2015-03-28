<?php

/**
 * Generate csv file from order and country table, with tracking number
 */

//config
define('DATE_RANGE', 3);
define('ORDER_STATUS', 4);
define('CSV_FOLDER_PATH', './');  //need slash '/' at the end
define('CSV_FILE_NAME', 'shipment.csv');


//do not modify below this line
//-------------------------------------------------------------------------------------

//csv: utf8
//tracking number: trim, 0-9 A-Z ':'
//customer_name, trim
//iso3: trim, uppercase
//all others: trim, lowercase

//begin timer
$start = explode(' ', microtime());

echo 'Script start: '.($start[1] + $start[0]).'<br />';

//require('./Encoding.php');
require('../includes/configure.php');
require('../includes/database_tables.php');

$db = new mysqli(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);

$q = "SELECT o.`orders_id`, o.`customers_name`, o.`customers_email_address`, o.`CP_track_num`, o.`date_purchased`, o.`customers_telephone`, o.`customers_telephone`, c.`countries_iso_code_3`
	 FROM `".TABLE_ORDERS."` o, `".TABLE_COUNTRIES."` c
	 WHERE o.`last_modified` > '".date("Y-m-d H:i:s", time() - DATE_RANGE * 60 * 60 * 24)."'
	 AND o.`delivery_country` = c.`countries_name`
	 AND o.`orders_status` = '".ORDER_STATUS."'
	 ORDER BY o.`last_modified` DESC";

$r = $db->query($q);
$csv_data = array();

//add the header
$csv_data[] = array('order_id', 'customer_name', 'email', 'sms', 'tracking_number', 'destination_country', 'created_date');

echo 'Number of record: '.$r->num_rows.'<br />';

for ($i=0;$i<$r->num_rows;$i++) {
	$d = $r->fetch_assoc();

	$csv_data[] = array(
		strtolower(trim($d['orders_id'])),
		//trim(Encoding::toUTF8(html_entity_decode($d['customers_name']))),
		trim(html_entity_decode(iconv("ISO-8859-1", "UTF-8", $d['customers_name']))),
		strtolower(trim($d['customers_email_address'])),
		strtolower(trim($d['customers_telephone'])),
		preg_replace('/[^A-Z0-9\:]/', '', strtoupper(trim($d['CP_track_num']))),
		strtoupper(trim($d['countries_iso_code_3'])),
		trim($d['date_purchased'])
	);

	echo 'outputting order: '.$d['orders_id'].', '.$csv_data[count($csv_data) - 1][1].', '.$d['customers_email_address'].'<br />';
}

echo 'writing to file<br />';

$fp = fopen(CSV_FOLDER_PATH.CSV_FILE_NAME, 'w');

foreach ($csv_data as $csv) {
	fputcsv($fp, $csv);
}

fclose($fp);

echo 'File outputted to: '.CSV_FOLDER_PATH.CSV_FILE_NAME.'<br />';

//end timer
$end = explode(' ', microtime());

$time_consumed = $end[0] + $end[1] - $start[0] - $start[1];

echo 'Script end: '.($end[1] + $end[0]).', time consumed: '.$time_consumed.'<br />';
