<?php
/**
 * 专辑采集，只采集ID
 * 包括专辑ID和歌手ID
 * 此采集较慢，因为有些歌手的专辑较多，请求百度专辑接口的次数较多
 * 最好每次只采 100 位歌手的专辑
 */
if(!defined('APP_PATH')) {
	exit("Access is denied.");
}

require_once APP_PATH . 'util/db.php';
require_once APP_PATH . 'util/Collect.class.php';

$con = get_con();

$collect = new Collect();

// manual_collect表示手动再次采集该歌手的专辑，比如想要更新歌手的新专辑就可以设置该字段为1
// 如果想重新采集某歌手的专辑设置manual_collect为1并设置shield_collect为0
$result = mysql_query("select id from " . DBPREFIX . "artist where (manual_collect=1 or id not in (select distinct artist_id from " . DBPREFIX . "album)) and shield_collect=0 order by manual_collect desc limit 100", $con);
$artistIdArr = array();
while(!!($row = mysql_fetch_array($result))) {

    $artistIdArr[] = $row['id'];

}
// print_r($artistIdArr);die;

if(!empty($artistIdArr)) {

    $albumArr = $collect->albumList($artistIdArr);
	// print_r($albumArr);die;

    if(!empty($albumArr)) {

        foreach($artistIdArr as $artistId) {

        	$data['artist_id'] = $artistId;

        	//  有些歌手没有专辑只有歌曲，把没有专辑的歌手的shield_collect设为1，防止下次还采集它。如果想再采集该歌手设置manual_collect为1。
            if(!empty($albumArr[$artistId])) { // 该歌手有专辑

                foreach($albumArr[$artistId] as $album) {

                    $data['album_id'] = $album['album_id'];
    
                    $result = mysql_query("select id from " . DBPREFIX . "album where album_id={$data['album_id']}", $con);
                    if(!($row = mysql_fetch_array($result))) { // 数据库不存在该专辑 -> insert

                        $sql = "insert into
                                            " . DBPREFIX . "album
                                           (album_id,
                                            artist_id)
                                     values
                                           ({$data['album_id']},
                                            {$data['artist_id']})";

                    }
                    // echo $sql;die;
                    mysql_query($sql, $con);

                }

            } else { // 该歌手没有专辑，设置shield_collect为1 

            	$sql = "update " . DBPREFIX . "artist set shield_collect=1 where id={$data['artist_id']}";
            	// echo $sql;die;
            	mysql_query($sql, $con);

            }

            // 采集完成后设置manual_collect为0
            $sql = "update " . DBPREFIX . "artist set manual_collect=0 where id={$data['artist_id']}";
            mysql_query($sql, $con);

        }

    }

}

close_con($con);
?>

<div>
	专辑列表采集完成！
	<a href="index.php">返回首页</a>
</div>
