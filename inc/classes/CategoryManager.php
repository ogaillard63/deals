<?php
namespace App;

/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 01/12/2017
* @desc			Gestion des categories
*/

use PDO;

class CategoryManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet category correspondant à l'Id
	* @param $id
	*/
	public function getCategory($id) {
		$q = $this->bdd->prepare("SELECT * FROM categories WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Category($q->fetch(PDO::FETCH_ASSOC));
	}
	/**
	* Retourne l'objet category correspondant contenant path
	* @param $path
	*/
	public function getCategoryByPath($path) {
		$q = $this->bdd->prepare("SELECT * FROM categories WHERE path = :path");
		$q->bindValue(':path', $path, PDO::PARAM_INT);
		$q->execute();
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if (is_array($data)) return new Category($data);
		else return false;
	}

	/**
	* Retourne la liste des categories
	*/
	/*
	public function getCategories($offset = null, $count = null) {
		$categories = array();
		if (isset($offset) && isset($count)) {
			$q = $this->bdd->prepare('SELECT * FROM categories ORDER BY parent, sort LIMIT :offset, :count');
			$q->bindValue(':offset', $offset, PDO::PARAM_INT);
			$q->bindValue(':count', $count, PDO::PARAM_INT);
		}
		else {
			$q = $this->bdd->prepare('SELECT * FROM categories ORDER BY parent, sort');
		}

		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$categories[] = new Category($data);
		}
		return $categories;
	}
*/
		/**
	* Retourne la liste des categories
	*/
	public function getCategories() {
		$categories = array();
		$subs = array();
		$q = $this->bdd->prepare('SELECT * FROM categories WHERE parent = 0 ORDER BY id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$category = new Category($data);
			$subs = $this->getSubCategories($category->getId());
			$category->setSubCategories($subs);
			$categories[] = $category;
		}
		return $categories;
	}
	/**
	 * Retourne la liste des sous categories d'une catégorie
	 */
	public function getSubCategories($parent_id) {
		$categories = array();
		$q = $this->bdd->prepare('SELECT * FROM categories WHERE parent = '.$parent_id);
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$categories[] = new Category($data);
		}
		return $categories;
	}
	
	public function getCategoriesToMenu() {
		$categories = array();
		$q = $this->bdd->prepare('SELECT * FROM categories ORDER BY parent, sort');
		
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$categories[] = new Category($data);
		}
		return $categories;
	}

	/**
	 * Retourne la liste des categories par page
	 */
	 public function getCategoriesByPage($page_num, $count) {
		return $this->getCategories(($page_num-1)*$count, $count);
	 }
	/**
	* Recherche les categories
	*/
	public function searchCategories($query) {
		$categories = array();
		$q = $this->bdd->prepare('SELECT * FROM categories 
			WHERE name LIKE :query OR path LIKE :query OR description LIKE :query OR sort LIKE :query OR parent LIKE :query');
		$q->bindValue(':query', '%'.$query.'%', PDO::PARAM_STR);
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$categories[] = new Category($data);
		}
		return $categories;
	}

	/**
	 * Retourne le nombre max de categories
	 */
	public function getMaxCategories() {
		$q = $this->bdd->prepare('SELECT count(1) FROM categories');
		$q->execute();
		return intval($q->fetch(PDO::FETCH_COLUMN));
	}

	/**
	* Efface l'objet category de la bdd
	* @param Category $category
	*/
	public function deleteCategory(Category $category) {
		try {	
			$q = $this->bdd->prepare("DELETE FROM categories WHERE id = :id");
			$q->bindValue(':id', $category->getId(), PDO::PARAM_INT);
			return $q->execute();
			}
		catch( PDOException $Exception ) {
			return false;
		}
	}

	/**
	* Enregistre l'objet category en bdd
	* @param Category $category
	*/
	public function saveCategory(Category $category) {
		if ($category->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO categories SET name = :name, path = :path, description = :description, sort = :sort, parent = :parent');
		} else {
			$q = $this->bdd->prepare('UPDATE categories SET name = :name, path = :path, description = :description, sort = :sort, parent = :parent WHERE id = :id');
			$q->bindValue(':id', $category->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':name', $category->getName(), PDO::PARAM_STR);
		$q->bindValue(':path', $category->getPath(), PDO::PARAM_STR);
		$q->bindValue(':description', $category->getDescription(), PDO::PARAM_STR);
		$q->bindValue(':sort', $category->getSort(), PDO::PARAM_INT);
		$q->bindValue(':parent', $category->getParent(), PDO::PARAM_INT);


		$q->execute();
		if ($category->getId() == -1) $category->setId($this->bdd->lastInsertId());
	}

	/**
	 * Retourne une liste des categories formatés pour peupler un menu déroulant
	 */
	public function getCategoriesForSelect() {
		$categories = array();
		$q = $this->bdd->prepare('SELECT id, name FROM categories ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$categories[$row["id"]] =  $row["name"];
		}
		return $categories;
	}

	/**
	 * Retourne une liste des categories formatée pour peupler un menu déroulant
	 */
	public function getParentCategoriesForSelect() {
		$categories = array();
		$categories[0] =  "None "; // no parent
		$q = $this->bdd->prepare('SELECT id, name FROM categories WHERE parent = 0 ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$categories[$row["id"]] =  $row["name"];
		}

		 return $categories;
	}
	public function isValidePath($path) {
		// decompose le path
		$categories = array();
		$categories = explode("/", $path);
		// si path superieur à 2 categories
		if (sizeof($categories)>2) return "";
		foreach ($categories as $category) {
			// test si la categorie n'existe pas
			if (!$this->getCategoryByPath($category)) return "";
		}
		return $category; // renvoi la erniere categorie trouvée dans le path 
	}

	public function makeBreadcrumb($category_path) {
		$breadcrumb = array();
		$category = $this->getCategoryByPath($category_path);
		$breadcrumb[] = $category;
		if ($category->parent > 0) { // à une sous-categorie
			$principal = $this->getCategory($category->parent);
			array_unshift($breadcrumb, $principal);	
		}
		return $breadcrumb;
	}

}





?>