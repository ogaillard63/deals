<?php
namespace App;

/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 01/12/2017
* @desc			Objet relation
*/

class Relation {
	public $item_id;
	public $category_id;


	public function __construct($data) {
		$this->hydrate($data);
	}

	public function hydrate(array $data){
		foreach ($data as $key => $value) {
			if (strpos($key, "_") !== false) {
				$method = 'set';
				foreach (explode("_", $key) as $part) {
					$method .= ucfirst($part);
				}
			}
			else $method = 'set'.ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	/* --- Getters et Setters --- */
	// item_id;
	public function setItemId($item_id) {
		$this->item_id = (integer)$item_id;
	}
	public function getItemId() {
		return $this->item_id;
	}
	// category_id;
	public function setCategoryId($category_id) {
		$this->category_id = (integer)$category_id;
	}
	public function getCategoryId() {
		return $this->category_id;
	}


}
?>
