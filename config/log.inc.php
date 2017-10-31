<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//日志设置
$LOG_FILENAME = (isset($LOG_FILENAME)) ? $LOG_FILENAME : '';
$logHandler = new CLogFileHandler(DATA_DIR . "/logs/" . date('Y-m-d') . $LOG_FILENAME . '.php');
Log::Init($logHandler, 15);

?>
