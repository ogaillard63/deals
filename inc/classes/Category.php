<?php
namespace App;

/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 01/12/2017
* @desc			Objet category
*/

class Category {
	public $id;
	public $name;
	public $path;
	public $description;
	public $sort;
	public $parent;


	public function __construct(array $data) {
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
	// id;
	public function setId($id) {
		$this->id = (integer)$id;
	}
	public function getId() {
		return $this->id;
	}
	// name;
	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	// path;
	public function setPath($path) {
		$this->path = $path;
	}
	public function getPath() {
		return $this->path;
	}
	// description;
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getDescription() {
		return $this->description;
	}
	// sort;
	public function setSort($sort) {
		$this->sort = (integer)$sort;
	}
	public function getSort() {
		return $this->sort;
	}
	// parent;
	public function setParent($parent) {
		$this->parent = (integer)$parent;
	}
	public function getParent() {
		return $this->parent;
	}

	// sub_categories
	public function setSubCategories($sub_categories) {
		$this->sub_categories = $sub_categories;
	}
	public function getSubCategories() {
		return $this->sub_categories;
	}
}
?>
