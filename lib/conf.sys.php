<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

define ("TEST_URI",  $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$d_uri = explode(".", TEST_URI);

$s_uri = explode("/", TEST_URI);

define ("PLUGIN_NAME", $s_uri[2]);

define ("PLUGIN_VERSION", $s_uri[3]);

require_once($DOCUMENT_ROOT . '/lib/environment.php');
require_once($DOCUMENT_ROOT . '/lib/conf.func.php');
require_once($DOCUMENT_ROOT . '/lib/util/utilDb.php');

?>
