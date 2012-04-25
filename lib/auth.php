<?php

/* Base Define Info */

define ("TEST_URI",  $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$d_uri = explode(".", TEST_URI);

$s_uri = explode("/", TEST_URI);

define ("PLUGIN_NAME" , $s_uri[2]);

define ("PLUGIN_VERSION" , $s_uri[3]);

define ("PLUGIN_USER_ID", $d_uri[0]);

define ("PLUGIN_TPL_FILENAME", "index.tpl");

/* Base Additional Info */
define ("PLUGIN_TPL_USER_FILENAME" , "index_".PLUGIN_USER_ID.".tpl");

//define ('PLUGIN_TPL_FILENAME', PLUGIN_TPL_USER_FILENAME);

define ("PLUGIN_URL" , SERVER_PLUGIN_URL.PLUGIN_NAME."/".PLUGIN_VERSION."/");

define ("PLUGIN_PATH" , SERVER_PLUGIN_PATH.DS.PLUGIN_NAME.DS.PLUGIN_VERSION.DS);

define ("PLUGIN_TEMPLATE_PATH" , PLUGIN_PATH."templates".DS);

//upload path
define ("PLUGIN_UPLOAD_PATH" , SERVER_ATTACHEMENT_PATH.PLUGIN_NAME.DS.PLUGIN_VERSION.DS);

unset ($sCurUrl, $sCurrentPluginName, $sCurrrentPluginVersion);
?>