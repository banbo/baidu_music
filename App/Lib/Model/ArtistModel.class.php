<?php
class ArtistModel extends Model {

	protected $fields = array(
		'id', 'name', 'avatar', 'area', 'birth', 'index_letter', 'area_code', 'type', 'shield_collect', 'manual_collect', '_pk' => 'id', '_autoinc' => false
	);

	public function create($valArr) {
	}

	public function update($valArr) {
		
	}

	public function delete($id) {
		
	}

	public function getSingle($id) {
		
	}

	public function getList() {
		$artistList = $this->select();
		return $artistList;
	}

	public function getListByCondition($condArr) {
		$artistList = $this->where($condArr)->select();
		return $artistList;
	}
}