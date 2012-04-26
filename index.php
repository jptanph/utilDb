<?php

require_once('lib/Common.php');

$sql = new utilDb2();

$aFields[] = array
(
	'a' => 1,
	'b' => 2
);


echo $sql->getInsertQueryLoop('test',$aFields)->debug();
