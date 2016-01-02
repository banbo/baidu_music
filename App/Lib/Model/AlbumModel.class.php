<?php

class AlbumModel extends Model {
	
	protected $fields = array(
		'album_id', 'artist_id', 'album_title', 'album_cover', 'time', 'styles', 'company', 'description', 'status', 'create_time', '_pk' => 'album_id', '_autoinc' => false
	);
	
	public function create($valArr) {
		
	}

	public function update($valArr) {
		
	}

	public function delete($id) {
		
	}

	public function getSingle($id) {
		$album = $this->find($id);
		return $album;
	}

	public function getList() {
		
	}

	public function getListByCondition($condArr) {
		$albumList = $this->where($condArr)->select();
		return $albumList;
	}
	
}