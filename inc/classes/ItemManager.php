<?php
namespace App;

/**
* @project		banggood
* @author		Olivier Gaillard
* @version		1.0 du 28/11/2017
* @desc			Gestion des items
*/

use PDO;

class ItemManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet item correspondant à l'Id
	* @param $id
	*/
	public function getItem($id) {
		$q = $this->bdd->prepare("SELECT * FROM items WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Item($q->fetch(PDO::FETCH_ASSOC));
	}

	/**
	* Retourne la liste des items
	*/
	public function getItems($offset = null, $count = null) {
		$items = array();
		if (isset($offset) && isset($count)) {
			$q = $this->bdd->prepare('SELECT * FROM items ORDER BY created DESC LIMIT :offset, :count');
			$q->bindValue(':offset', $offset, PDO::PARAM_INT);
			$q->bindValue(':count', $count, PDO::PARAM_INT);
		}
		else {
			$q = $this->bdd->prepare('SELECT * FROM items ORDER BY created');
		}

		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$items[] = new Item($data);
		}
		return $items;
	}
	/**
	* Retourne la liste des items par categorie
	*/
	public function getItemsByCategory($category) {
		$items = array();
		if (isset($category) && strlen($category) > 0) {
			$q = $this->bdd->prepare('SELECT i.* FROM items i
						JOIN item_category r ON i.id = r.item_id
						JOIN categories c ON c.id = r.category_id
						WHERE c.path = :category ORDER BY i.discount DESC, i.created DESC');
			$q->bindValue(':category', $category , PDO::PARAM_STR);
		}
		else $q = $this->bdd->prepare('SELECT * FROM items ORDER BY discount DESC, created DESC');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$items[] = new Item($data);
		}
		return $items;
	}
	/**
	* Retourne la liste des items sans categorie
	*/
	public function getItemsWithoutCategory() {
		$items = array();
		$q = $this->bdd->prepare('SELECT i.* FROM items i
			LEFT OUTER JOIN item_category r ON r.item_id = i.id
			WHERE r.item_id IS NULL	ORDER BY i.id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$items[] = new Item($data);
			}
		return $items;
	}


	/**
	 * Retourne la liste des items par page
	 */
	 public function getItemsByPage($page_num, $count) {
		return $this->getItems(($page_num-1)*$count, $count);
	 }
	/**
	* Recherche les items par lien
	*/
	public function searchItemsByLink($query) {
		$items = array();
		$q = $this->bdd->prepare('SELECT * FROM items WHERE link LIKE :query');
		$q->bindValue(':query', '%'.$query.'%', PDO::PARAM_STR);
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$items[] = new Item($data);
		}
		return $items;
	}
	 /**
	* Recherche les items
	*/
	public function searchItems($query) {
		$items = array();
		$q = $this->bdd->prepare('SELECT * FROM items 
			WHERE created LIKE :query OR expired LIKE :query OR title LIKE :query OR link LIKE :query OR affiliate_link LIKE :query OR content LIKE :query OR photo_link LIKE :query OR code LIKE :query OR normal_price LIKE :query OR promo_price LIKE :query OR discount LIKE :query OR quantity LIKE :query OR shop LIKE :query OR status LIKE :query');
		$q->bindValue(':query', '%'.$query.'%', PDO::PARAM_STR);
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$items[] = new Item($data);
		}
		return $items;
	}

	/**
	 * Retourne le nombre max de items
	 */
	public function getMaxItems() {
		$q = $this->bdd->prepare('SELECT count(1) FROM items');
		$q->execute();
		return intval($q->fetch(PDO::FETCH_COLUMN));
	}

	/**
	* Efface l'objet item de la bdd
	* @param Item $item
	*/
	public function deleteItem(Item $item) {
		try {	
			$q = $this->bdd->prepare("DELETE FROM items WHERE id = :id");
			$q->bindValue(':id', $item->getId(), PDO::PARAM_INT);
			return $q->execute();
			}
		catch( PDOException $Exception ) {
			return false;
		}
	}

	/**
	* Enregistre l'objet item en bdd
	* @param Item $item
	*/
	public function saveItem(Item $item) {
		if ($item->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO items SET created = :created, expired = :expired, title = :title, link = :link, affiliate_link = :affiliate_link, content = :content, photo_link = :photo_link, code = :code, normal_price = :normal_price, promo_price = :promo_price, discount = :discount, quantity = :quantity, shop = :shop, status = :status');
		} else {
			$q = $this->bdd->prepare('UPDATE items SET created = :created, expired = :expired, title = :title, link = :link, affiliate_link = :affiliate_link, content = :content, photo_link = :photo_link, code = :code, normal_price = :normal_price, promo_price = :promo_price, discount = :discount, quantity = :quantity, shop = :shop, status = :status WHERE id = :id');
			$q->bindValue(':id', $item->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':created', $item->getCreated(), PDO::PARAM_STR);
		$q->bindValue(':expired', $item->getExpired(), PDO::PARAM_STR);
		$q->bindValue(':title', $item->getTitle(), PDO::PARAM_STR);
		$q->bindValue(':link', $item->getLink(), PDO::PARAM_STR);
		$q->bindValue(':affiliate_link', $item->getAffiliateLink(), PDO::PARAM_STR);
		$q->bindValue(':content', $item->getContent(), PDO::PARAM_STR);
		$q->bindValue(':photo_link', $item->getPhotoLink(), PDO::PARAM_STR);
		$q->bindValue(':code', $item->getCode(), PDO::PARAM_STR);
		$q->bindValue(':normal_price', $item->getNormalPrice(), PDO::PARAM_STR);
		$q->bindValue(':promo_price', $item->getPromoPrice(), PDO::PARAM_STR);
		$q->bindValue(':discount', $item->getDiscount(), PDO::PARAM_INT);
		$q->bindValue(':quantity', $item->getQuantity(), PDO::PARAM_INT);
		$q->bindValue(':shop', $item->getShop(), PDO::PARAM_STR);
		$q->bindValue(':status', $item->getStatus(), PDO::PARAM_STR);


		$q->execute();
		if ($item->getId() == -1) $item->setId($this->bdd->lastInsertId());
	}

	/**
	 * Retourne une liste des items formatés pour peupler un menu déroulant
	 */
	public function getItemsForSelect() {
		$items = array();
		$q = $this->bdd->prepare('SELECT id, name FROM items ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$items[$row["id"]] =  $row["name"];
		}
		return $items;
	}
}
?>