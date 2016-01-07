<?php
/**
 * 采集类
 * 特殊字符如法语 É 百度是用 &Eacute; 保存的，参考 http://tool.oschina.net/commons?type=2
 * @author chenweiwei
 */
class Collect {

    public function __construct() {

        set_time_limit(0);

    }

    /**
     * 采集歌手列表
     * 包括歌手ID、字母编号、地区编号、类型
     * 歌手大概三万个左右，用一次采，每个地区采完后中间休息 30 秒
     * @param array $artistAreaCodeArr 歌手地区
     * @param array $artistTypeArr 歌手类型
     * @return array
     */
    public function artistList($artistAreaCodeArr, $artistTypeArr, $indexLetterArr) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $artistList = array();

        foreach($artistAreaCodeArr as $artistAreaCode) {

            foreach($artistTypeArr as $artistType) {

                curl_setopt($ch, CURLOPT_URL, 'http://music.baidu.com/artist/' . $artistAreaCode . '/' . $artistType);

                // 得到页面数据
                $pageData = curl_exec($ch);
                // 删除回车符空格
                $pageData = preg_replace('/[\n\r\s]/', '', $pageData);
                if(empty($pageData)) {
                    continue;
                }

                if(false !== strpos($pageData, 'http://verify.baidu.com/vcode')) { // 百度阻止访问
                    continue; 
                }

                foreach($indexLetterArr as $letter) {

                    $letterName = ($letter == 'other') ? '其他' : $letter; // 其他歌手显示的是"其他"不是"other"

                    // A-Z 区域
                    preg_match('/<liclass="list-item"><divclass="module-linemodule-line-bottom"><\/div><h3><aname="' . $letter . '"><\/a>' . $letterName . '<\/h3><ulclass="clearfix">(.*)<\/ul><\/li>/U', $pageData, $indexAreaMatches);
                    // var_dump($indexAreaMatches);die;
                    if(empty($indexAreaMatches)) {
                        continue;
                    }
                    // 区域内部，取得歌手ID
                    preg_match_all('/href="\/artist\/([0-9]+)"/U', $indexAreaMatches[1], $matches);
                    // var_dump($matches);die;
                    if(empty($matches)) {
                        continue;
                    }

                    foreach($matches[1] as $k => $v) { // 组合成一个数组

                        $artistList[] = array(
                                'id' => $matches[1][$k],
                                'index_letter' => $letter,
                                'area_code' => $artistAreaCode,
                                'type' => $artistType
                        );

                    }

                }

            }

            // 一个地区歌手采集完休息30秒
            sleep(30);

        }

        curl_close($ch);

        return $artistList;

    }

    /**
     * 采集歌手资料
     * 包括歌手姓名、头像、生日、地区
     * 按 100 个歌手为单位，每次采集中间休息 60 秒，防止访问过频导致百度拒绝请求
     * @param array $artistIdArr 歌手ID数组
     * @param string $avatarDir 歌手头像保存的物理路径
     * @param string $avatarRelativeDir 歌手头像保存到数据库的路径
     * @return array
     */
    public function artistInfo($artistIdArr, $avatarDir, $avatarRelativeDir) {

        require_once './util/GetImage.class.php';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $artistInfoList = array();

        for($i = 0; $i < count($artistIdArr); $i++) {

            $artistId = $artistIdArr[$i];

            curl_setopt($ch, CURLOPT_URL, 'http://music.baidu.com/artist/' . $artistId);

            // 得到页面数据
            $pageData = curl_exec($ch);
            // 删除回车符
            $pageData = preg_replace('/[\n\r]/', '', $pageData);
            // echo $pageData;die;
            if(empty($pageData)) {
                continue;
            }

            // 百度阻止访问
            if(false !== strpos($pageData, 'http://verify.baidu.com/vcode')) {
                continue;
            }

            // 歌手姓名
            preg_match('/<h2 class="singer-name" title="[^"]+">([^<]+)<\/h2>/', $pageData, $artistNameMatche);
            // var_dump($artistNameMatche);die;

            // 删除空格，不能在取得歌手姓名前删除空格，因为歌手姓名中间可能包含空格，如欧美歌手
            $pageData = preg_replace('/[\s]/', '', $pageData);

            // 歌手头像
            preg_match('/<spanclass="cover"><imgsrc="([^"]+)"alt="[^"]+"\/><\/span>/', $pageData, $artistAvatarMatche);
            // var_dump($artistAvatarMatche);die;
            // 下载头像到本地
			$avatarPath = '';
            if(isset($artistAvatarMatche[1])) {
                $getImage = new GetImage();
                if(!!($fileName = $getImage->get($artistAvatarMatche[1], $avatarDir))) {
                    $avatarPath = $avatarRelativeDir . $fileName;
                }
            }

            preg_match('/地区：<span>([^<]+)<\/span>/', $pageData, $artistAreaMatche);
            // var_dump($artistAreaMatche);die;

            preg_match('/生日：<span>([^<]+)<\/span>/', $pageData, $artistBirthMatche);
            // var_dump($artistBirthMatche);die;

            $artistInfoList[] = array(
                    'id' => $artistId,
                    'name' => isset($artistNameMatche[1]) ? $artistNameMatche[1] : '',
                    'avatar' => isset($avatarPath) ? $avatarPath : '',
                    'area' => isset($artistAreaMatche[1]) ? $artistAreaMatche[1] : '',
                    'birth' => isset($artistBirthMatche[1]) ? $artistBirthMatche[1] : '',
            );

            if(($i + 1) % 100 == 1) {

                sleep(60);

            }

        }

        curl_close($ch);

        return $artistInfoList;

    }

    /**
     * 采集专辑列表
     * 只采集专辑ID
     * @param array $artistIdArr 歌手ID数组
     * @return array
     */
    public function albumList($artistIdArr) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $albumList = array();

        for($i = 0; $i < count($artistIdArr); $i++) {

            $artistId = $artistIdArr[$i];

            curl_setopt($ch, CURLOPT_URL, 'http://music.baidu.com/artist/' . $artistId);
            $pageData = curl_exec($ch);

            // 删除回车符和空格
            $pageData = preg_replace('/[\n\r\s]/', '', $pageData);
            if(empty($pageData)) {
                continue;
            }

            // 百度阻止访问
            if(false !== strpos($pageData, 'http://verify.baidu.com/vcode')) {
                continue;
            }

            // 百度专辑是分页显示的，通过调用接口得到数据
            // 获取第一页的专辑
            $albumList[$artistId] = array();
            $firstPageReturn = $this->getAlbumByInterface($artistId, 0);
            if($firstPageReturn['status'] == 200) {

                $albumList[$artistId] = $firstPageReturn['data'];

            } elseif($firstPageReturn['status'] == 201) { // 如果错误采下一个歌手

                continue;

            }
			
            // 匹配得到专辑分页页码，然后获取后几页的专辑
            preg_match_all('/<aclass="page-navigator-numberPNNW-S"href="\/data\/artist\/getAlbum\?start=([0-9]+)&amp;size=10">[0-9]+<\/a>/U', $pageData, $startMatches);
            if(isset($startMatches[1])) {
                for($j = 0; $j < count($startMatches[1]); $j++) {

                    $otherPageReturn = $this->getAlbumByInterface($artistId, $startMatches[1][$j]);
                    if($otherPageReturn['status'] == 200) {

                        foreach($otherPageReturn['data'] as $otherAlbum) {

                            array_push($albumList[$artistId], $otherAlbum);

                        }

                    } else { // 如果错误退出并删除该歌手采到的专辑，继续采下一个歌手

                        unset($albumList[$artistId]);
                        break;

                    }

                }
            }

            if(($i + 1) % 50 == 1) {

                sleep(60);

            }

        }

        curl_close($ch);

        return $albumList;

    }

    /**
     * 通过百度的接口获取专辑列表
     * 只采集专辑ID
     * @param integer $artistId
     * @param integer $start
     * @return boolean
     */
    private function getAlbumByInterface($artistId, $start) {

        require_once './util/GetImage.class.php';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://music.baidu.com/data/user/getalbums?start=' . $start . '&ting_uid=' . $artistId . '&order=time&.r=' . mt_rand(0, 1000));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 获得返回的ajax数据
        preg_match('/\{.*\}/', curl_exec($ch), $ajaxData);

        curl_close($ch);

        if(empty($ajaxData)) {
            return array('status' => 201);
        }

        // 转换编码
        $albumObject = json_decode($ajaxData[0]);

        // 获得html内容
        $pageData = $albumObject->data->html;
        // 删除回车符空格
        $pageData = preg_replace('/[\n\r\s]/', '', $pageData);

        // 匹配
        preg_match_all('/href="\/album\/([0-9]+)"title="[^"]+"class="cover"/U', $pageData, $matches);

        if(!isset($matches[1]) || empty($matches[1])) {

            return array('status' => 201);

        }

        foreach($matches[1] as $k => $v) {

            $albumList[] = array(
                    'artist_id' => $artistId,
                    'album_id' => $matches[1][$k],
            );

        }

        return array('status' => 200, 'data' => $albumList);

    }

    /**
     * 采集该专辑信息
     * 包括专辑封面、专辑标题、其他信息、专辑说明、歌曲列表
     * @param array $albumIdArr
     * @param string $coverDir 专辑封面保存物理路径
     * @param string $coverRelativeDir 专辑封面保存到数据库的路径
     * @return array
     */
    public function albumInfo($albumIdArr, $coverDir, $coverRelativeDir) {

        require_once './util/GetImage.class.php';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $albumInfoList = array();

        for($i = 0; $i < count($albumIdArr); $i++) {

            $albumId = $albumIdArr[$i];

            curl_setopt($ch, CURLOPT_URL, 'http://music.baidu.com/album/' . $albumId);
            $pageData = curl_exec($ch);

            // 删除回车符，不能删除空格，专辑名、歌曲名、发行公司之间都可能包含空格
            $pageData = preg_replace('/[\n\r]/', '', $pageData);
            // 删除其他信息那块包含的&nbsp;
            $pageData = str_replace('&nbsp;', '', $pageData);
            // echo $pageData;die;
            if(empty($pageData)) {
                continue;
            }

            // 百度阻止访问
            if(false !== strpos($pageData, 'http://verify.baidu.com/vcode')) {
                continue;
            }

            // 专辑封面
            preg_match('/<span class="cover"><img src="([^"]+)" alt="[^"]+" \/><\/span>/U', $pageData, $albumCoverMatche);
            // 下载图片到本地
            $albumCoverPath = '';
            // 下载头像到本地
            if(isset($albumCoverMatche[1])) {
                $getImage = new GetImage();
                if(!!($fileName = $getImage->get($albumCoverMatche[1], $coverDir))) {
                    $albumCoverPath = $coverRelativeDir . $fileName;
                }
            }

            // 专辑标题
            preg_match('/<h2 class="album-name">(.*)<\/h2>/U', $pageData, $albumTitleMatche);

            // 其他信息
            preg_match('/发行时间：([^\s]+)/', $pageData, $timeMatch);
            preg_match('/流派：([^\s]+)/', $pageData, $stylesMatch);
            preg_match('/发行公司：([^<]+)/', $pageData, $companyMatch);

            // 专辑说明
            preg_match('/<span class="description-all">(.*)<\/span>/U', $pageData, $descriptionMatch);

            // 专辑歌曲列表
            preg_match_all('/<a href="\/song\/([0-9a-z#]+)" title="([^"]+)">[^<]+<\/a>/iU', $pageData, $albumSongListMatches);
            foreach($albumSongListMatches[1] as $k => $v) {
                $songList[] = array(
                        'id' => $albumSongListMatches[1][$k],
                        'album_id' => $albumId,
                        'name' => $albumSongListMatches[2][$k]
                );
            }

            $albumInfoList[] = array(
                    'album_id' => $albumId,
                    'album_cover' => $albumCoverPath,
                    'album_title' => $albumTitleMatche[1],
                    'time' => isset($timeMatch[1]) ? $timeMatch[1] : '',
                    'styles' => isset($stylesMatch[1]) ? $stylesMatch[1] : '',
                    'company' => isset($companyMatch[1]) ? trim($companyMatch[1]) : '', // 公司名后面可能带了空格
                    'description' => isset($descriptionMatch[1]) ? $descriptionMatch[1] : '',
                    'songList' => $songList
            );

        }

        curl_close($ch);

        return $albumInfoList;

    }

}