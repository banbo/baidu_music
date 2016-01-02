<?php
/**
 * 专辑信息采集
 * 包括专辑名称、封面、发行时间、流派、发行公司、歌曲列表
 * 此采集较慢，因为要下载歌手头像到本地
 * 最好每次只采 300 张专辑的信息
 */
if(!defined('APP_PATH')) {
	exit("Access is denied.");
}

require_once APP_PATH . 'util/db.php';
require_once APP_PATH . 'util/Collect.class.php';

$con = get_con();

$collect = new Collect();

$result = mysql_query("select album_id from " . DBPREFIX . "album where album_title='' or album_title is null limit 300", $con);
$albumIdArr = array();
while(!!($row = mysql_fetch_array($result))) {

    $albumIdArr[] = $row['album_id'];

}

if(!empty($albumIdArr)) {

    $albumInfoArr = $collect->albumInfo($albumIdArr, COVERSAVEPATH, COVERRELATIVEPATH);

    if(!empty($albumInfoArr)) {

        foreach($albumInfoArr as $album) {

            $data['album_id'] = $album['album_id'];
            $data['album_title'] = $album['album_title'];
            $data['album_cover'] = $album['album_cover'];
            $data['time'] = $album['time'];
            $data['styles'] = $album['styles'];
            $data['company'] = $album['company'];
			$data['description'] = $album['description'];
			$data['status'] = 1; // 已审核
			$data['create_time'] = time();

            $albumSql = "update
                                " . DBPREFIX . "album
                            set
                                album_title = '{$data['album_title']}',
                                album_cover = '{$data['album_cover']}',
                                time = '{$data['time']}',
                                styles = '{$data['styles']}',
                                company = '{$data['company']}',
								description='{$data['description']}',
								status='{$data['status']}',
								create_time='{$data['create_time']}'
                          where
                                album_id = {$data['album_id']}";
            // echo $albumSql;die;
            mysql_query($albumSql, $con);

            // 该专辑包含的歌曲
            if(!empty($album['songList'])) {

                // 如果之前已保存，删除之前保存的歌曲列表
                mysql_query("delete from " . DBPREFIX . "song where album_id={$album['album_id']}", $con);
                foreach($album['songList'] as $song) {

                    $songSql = "insert into
                                            " . DBPREFIX . "song
                                           (id,
                                            album_id,
                                            name)
                                     values
                                           ({$song['id']},
                                            {$song['album_id']},
                                            '{$song['name']}')";

                    // echo $songSql;die;
                    mysql_query($songSql, $con);

                }

            }

        }

    }

}

close_con($con);
?>

<div>
	专辑信息采集完成！
	<a href="index.php">返回首页</a>
</div>
