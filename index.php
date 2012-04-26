
<?php

require_once('lib/Common.php');

$sql = new utilDb();

$aFields[] = array
(
	'a' => 1,
	'b' => 2
);



$aFields = array(
	'title',
	'location',
	'start_date'
);
$sql->selectAll('pg_scheduleradv_data',$aFields);