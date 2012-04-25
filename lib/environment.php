<?php
/**
 * 개발에 필수적인 기본 변수들이 정의되어있는 파일 , DB 정보나 경로등이 지정되어있는 파일입니다.
 */

// define path
define ('DS', DIRECTORY_SEPARATOR);
define ('SERVER_DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);


// define base configuration
define ('COMMON_DB_TYPE','mysql');
define ('COMMON_DB_HOST','localhost');
define ('COMMON_DB_USER','root');
define ('COMMON_DB_PASSWD','');
define ('COMMON_DB_NAME','plugin');
define ('COMMON_DB_PORT','3306');

if (substr(PHP_OS, 0, 3) == 'WIN') {
    define('PLUGIN_OS', 'WIN');
    define('PLUGIN_OS_WIN', true);
    define('PLUGIN_OS_UNIX', false);
} else {
    define('PLUGIN_OS', 'UNIX');
    define('PLUGIN_OS_WIN', false);
    define('PLUGIN_OS_UNIX', true);
}


$aDevServerList = array(
    'pr-dev1.cafe24test.com',
    'pr.dev.com'
);


$aQAServerList = array(
    'pr-qa1.cafe24test.com'
);

// 로컬
if(PLUGIN_OS_WIN === true) {
    define('IS_TEST', true);
    define('IS_LOCAL_SERVER', true);
    define('IS_DEV_SERVER', false);
    define('IS_QA_SERVER', false);
    define('IS_LIVE_SERVER', false);

// 개발 서버
} else if(PLUGIN_OS_UNIX === true && (in_array(getenv('SERVER_NAME'), $aDevServerList) === true || in_array(gethostname(), $aDevServerList) === true)) {
    define('IS_TEST', true);
    define('IS_LOCAL_SERVER', false);
    define('IS_DEV_SERVER', true);
    define('IS_QA_SERVER', false);
    define('IS_LIVE_SERVER', false);

// QA 서버
} else if(PLUGIN_OS_UNIX === true && (in_array(getenv('SERVER_NAME'), $aQAServerList) === true || in_array(gethostname(), $aQAServerList) === true)) {
    define('IS_TEST', false);
    define('IS_LOCAL_SERVER', false);
    define('IS_DEV_SERVER', false);
    define('IS_QA_SERVER', true);
    define('IS_LIVE_SERVER', false);

// 실서버
} else if(PLUGIN_OS_UNIX === true && (preg_match('/\.cafe24\.com/i', getenv('SERVER_NAME')) || preg_match('/\.cafe24\.com/i', gethostname()))) {
    define('IS_TEST', false);
    define('IS_LOCAL_SERVER', false);
    define('IS_DEV_SERVER', false);
    define('IS_QA_SERVER', false);
    define('IS_LIVE_SERVER', true);
}

define('UTIL_DB_RESULT_ROW', 'row');
define('UTIL_DB_RESULT_ROWS', 'rows');
define('UTIL_DB_RESULT_EXEC', 'exec');

define('UTIL_DB_ORDER_BY_ASC', 'asc');
define('UTIL_DB_ORDER_BY_DESC', 'desc');


?>
