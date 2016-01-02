<?php
class AlbumAction extends Action {
    public function index() {
    	$artistId = $this->_get('artist_id');

    	$Album = D('Album');

    	$albumList = $Album->getListByCondition(array('artist_id' => $artistId));

    	$this->assign('albumList', $albumList);
    	$this->display('index');
    }

    public function info() {
    	$albumId = $this->_get('id');

    	$Album = D('Album');

		$album = $Album->getSingle($albumId);

		$this->assign('album', $album);
		$this->display('info');
    }
}