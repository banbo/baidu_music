<?php
class ArtistAction extends Action {
    public function index() {
    	$Artist = D('Artist');
    	$indexLetterArr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'other');

    	foreach($indexLetterArr as $letter) {
    		$condArr = array(
    			'index_letter' => $letter
    		);

    		$artistList[$letter] = $Artist->getListByCondition($condArr);
    	}

    	$this->assign('artistList', $artistList);
    	$this->display('index');
    }
}