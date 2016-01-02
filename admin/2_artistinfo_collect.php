<?php
/**
 * 歌手资料采集
 * 包括歌手姓名、头像、生日、地区
 * 此采集较慢，因为要下载歌手头像到本地
 * 最好每次只采 500 位歌手的资料
 */
if(!defined('APP_PATH')) {
	exit("Access is denied.");
}

require_once APP_PATH . 'util/db.php';
require_once APP_PATH . 'util/Collect.class.php';

$con = get_con();

$collect = new Collect();

$result = mysql_query("select id from " . DBPREFIX . "artist where name='' or name is null limit 500", $con);
$artistIdArr = array();
while(!!($row = mysql_fetch_array($result))) {

    $artistIdArr[] = $row['id'];

}

if(!empty($artistIdArr)) {

    $artistInfoArr = $collect->artistInfo($artistIdArr, AVATARSAVEPATH, AVATARRELATIVEPATH);

    if(!empty($artistInfoArr)) {

        foreach($artistInfoArr as $artist) {

            $data['id'] = $artist['id'];
            $data['name'] = $artist['name'];
            $data['avatar'] = $artist['avatar'];
            $data['area'] = $artist['area'];
            $data['birth'] = $artist['birth'];

            $sql = "update
                           " . DBPREFIX . "artist
                       set
                            name = '{$data['name']}',
                            avatar = '{$data['avatar']}',
                            area = '{$data['area']}',
                            birth = '{$data['birth']}'
                     where
                            id = {$data['id']}";
            // echo $sql;die;
            mysql_query($sql, $con);

        }

    }

}
close_con($con);
?>

<div>
	歌手信息采集完成！
	<a href="index.php">返回首页</a>
</div>