<?php

require_once('lib/Common.php');

$sql = new utilDb();

$aFields[] = array(
	'a' => 1,
	'b' => 2
);


echo $sql->getInsertQueryLoop('mytable',$aFields);