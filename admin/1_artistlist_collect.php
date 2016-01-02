<?php
/**
 * 歌手列表采集，只采集ID
 * 包括歌手ID、字母编号、地区编号、类型
 * 一次采集全部歌手
 */
if(!defined('APP_PATH')) {
	exit("Access is denied.");
}

require_once APP_PATH . 'util/db.php';
require_once APP_PATH . 'util/Collect.class.php';

$con = get_con();

$collect = new Collect();

//$artistAreaCodeArr = array('cn', 'western', 'kr', 'jp');
$artistAreaCodeArr = array('cn');
//$artistTypeArr = array('male', 'female', 'group');
$artistTypeArr = array('male');
//$indexLetterArr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'other');
$indexLetterArr = array('A');
$artistArr = $collect->artistList($artistAreaCodeArr, $artistTypeArr, $indexLetterArr);

if(!empty($artistArr)) {

    foreach($artistArr as $artist) {

        $data['id'] = $artist['id'];
        $data['index_letter'] = $artist['index_letter'];
        $data['area_code'] = $artist['area_code'];
        $data['type'] = $artist['type'];

        $result = mysql_query("select id from " . DBPREFIX ."artist where id={$data['id']}", $con);
        if(!($row = mysql_fetch_array($result))) { // 数据库不存在该歌手 -> insert

            $sql = "insert into
                                " . DBPREFIX . "artist
                               (id,
                                index_letter,
                                area_code,
                                type)
                        values
                               ({$data['id']},
                                '{$data['index_letter']}',
                                '{$data['area_code']}',
                                '{$data['type']}')";

        } else { // 数据库已存在该歌手 -> update

            $sql = "update
                           " . DBPREFIX . "artist
                       set
                           index_letter = '{$data['index_letter']}',
                           area_code = '{$data['area_code']}',
                           type = '{$data['type']}'
                     where
                           id = {$data['id']}";

        }

        mysql_query($sql, $con);

    }

}

close_con($con);
?>

<div>
	歌手列表采集完成！
	<a href="index.php">返回首页</a>
</div>
