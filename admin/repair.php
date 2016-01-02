<?php
/**
 * 之前已填写的专辑下载地址
 */
require_once './include/header.php';

$result = mysql_query("select album_id,file_url,extract_code,file_url_spare,extract_code_spare from {$config['dbprefix']}album2");

while(!!($row = mysql_fetch_array($result))) {

    $sql = "update {$config['dbprefix']}album set file_url='{$row['file_url']}',extract_code='{$row['extract_code']}',file_url_spare='{$row['file_url_spare']}',extract_code_spare='{$row['extract_code_spare']}' where album_id={$row['album_id']};";

    mysql_query($sql);
    
    echo $sql . '<br />';
}

echo '修复完成';

require_once './include/footer.php';