<?php

require_once('lib/Common.php');

$sql = new utilDb();

$aFields[] = array
(
	'a' => 1,
	'b' => 2
);


echo $sql->getInsertQueryLoop('mytable',$aFields);


var_dump($sql->selectAll('pg_scheduleradv_data'));