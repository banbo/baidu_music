<?php
header("Content-type:text/html; charset=UTF-8");

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set("display_errors", 1);
set_time_limit(0);

define('APP_PATH', getcwd() . '/');

require_once APP_PATH . 'config/config.php';

$c = $_GET['c'];

if($c == '1') {
	require_once APP_PATH . '1_artistlist_collect.php';
} elseif($c == '2') {
	require_once APP_PATH . '2_artistinfo_collect.php';
} elseif($c == '3') {
	require_once APP_PATH . '3_albumlist_collect.php';
} else if($c == '4') {
	require_once APP_PATH . '4_albuminfo_collect.php';
} else {
?>
<div style="width:300px; margin:0 auto; margin-top: 100px;">
	<a href="index.php?c=1">1.采集歌手</a><br />
	<a href="index.php?c=2">2.采集歌手信息</a><br />
	<a href="index.php?c=3">3.采集专辑</a><br />
	<a href="index.php?c=4">7.采集专辑信息</a><br />
</div>

<?php
}
?>