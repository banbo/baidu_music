<?php

class GetImage {

    public function get($url, $savePath) {

        $fr = explode('/', $url);
        $filename = $fr[count($fr) - 1];
        $filesavepath = $savePath . $filename;

        if(file_exists($filesavepath)) {

            return $filename;

        }

        // 保存目录 
        $url = trim($url);
        //$url = str_replace(' ', '%20', $url);
        $filepath = trim($url);

        // 读文件
        $htmlfp = @fopen($filepath, 'r');

        // 远程 
        if(strstr($filepath, '://')) {
            while(!!($data = @fread($htmlfp, 50000000))) {
                $string .= $data;
            }
        } else { // 本地 
            // $string = @fread($htmlfp, @filesize($filepath));
        }
        @fclose($htmlfp);

        if(empty($string)) {

            return false;

        }

        // 存放目录 
        if (!file_exists($savePath)) { //不存在则建立 

            $mk = @mkdir($savePath, 0777); //权限 
            @chmod($savePath, 0777);

        }

        //写文件 
        $fp = @fopen($filesavepath, 'x+');
        $result = fputs($fp, $string);
        @fclose($fp);

        if($result) {
            return $filename;
        }
        return false;

    }

}