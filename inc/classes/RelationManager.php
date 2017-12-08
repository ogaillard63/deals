<?php
namespace App;

/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 01/12/2017
* @desc			Gestion des relations
*/

use PDO;

class RelationManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet relation correspondant à l'item_id
	* @param $item_id
	*/
	public function getRelation($item_id) {
		$q = $this->bdd->prepare("SELECT * FROM item_category WHERE item_id = :item_id");
		$q->bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$q->execute();
		return new Relation($q->fetch(PDO::FETCH_ASSOC));
	}

	/**
	* Retourne la liste des relations
	*/
	public function getRelations($offset = null, $count = null) {
		$relations = array();
		if (isset($offset) && isset($count)) {
			$q = $this->bdd->prepare('SELECT * FROM item_category LIMIT :offset, :count');
			$q->bindValue(':offset', $offset, PDO::PARAM_INT);
			$q->bindValue(':count', $count, PDO::PARAM_INT);
		}
		else {
			$q = $this->bdd->prepare('SELECT * FROM item_category');
		}

		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$relations[] = new Relation($data);
		}
		return $relations;
	}

	/**
	 * Retourne la liste des relations par page
	 */
	 public function getRelationsByPage($page_num, $count) {
		return $this->getRelations(($page_num-1)*$count, $count);
	 }

	/**
	 * Retourne le nombre max de relations
	 */
	public function getMaxRelations() {
		$q = $this->bdd->prepare('SELECT count(1) FROM item_category');
		$q->execute();
		return intval($q->fetch(PDO::FETCH_COLUMN));
	}

	/**
	* Efface l'objet relation de la bdd
	* @param Relation $relation
	*/
	public function deleteRelation(Relation $relation) {
		try {	
			$q = $this->bdd->prepare("DELETE FROM item_category WHERE id = :id");
			$q->bindValue(':id', $relation->getId(), PDO::PARAM_INT);
			return $q->execute();
			}
		catch( PDOException $Exception ) {
			return false;
		}
	}

	/**
	* Enregistre l'objet relation en bdd
	* @param Relation $relation
	*/
	public function saveRelation(Relation $relation) {
		$q = $this->bdd->prepare('INSERT INTO item_category SET category_id = :category_id, item_id = :item_id');
		$q->bindValue(':item_id', $relation->getItemId(), PDO::PARAM_INT);
		$q->bindValue(':category_id', $relation->getCategoryId(), PDO::PARAM_INT);
		$q->execute();
	}

	/**
	 * Retourne une liste des relations formatés pour peupler un menu déroulant
	 */
	public function getRelationsForSelect() {
		$relations = array();
		$q = $this->bdd->prepare('SELECT id, name FROM item_category ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$relations[$row["id"]] =  $row["name"];
		}
		return $relations;
	}
}
?>