<?php

require_once('lib/Common.php');

$sql = new utilDb();

$ssql = "SELECT * FROM pg_scheduleradv_data";

var_dump($sql->query($ssql));