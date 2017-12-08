<?php
namespace App;

/**
* @project		banggood
* @author		Olivier Gaillard
* @version		1.0 du 28/11/2017
* @desc			Objet item
*/

class Item {
	public $id;
	public $created;
	public $expired;
	public $title;
	public $link;
	public $affiliate_link;
	public $content;
	public $photo_link;
	public $code;
	public $normal_price;
	public $promo_price;
	public $discount;
	public $quantity;
	public $shop;
	public $status;


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
	// created;
	public function setCreated($created) {
		$this->created = $created;
	}
	public function getCreated() {
		return $this->created;
	}
	// expired;
	public function setExpired($expired) {
		$this->expired = $expired;
	}
	public function getExpired() {
		return $this->expired;
	}
	// title;
	public function setTitle($title) {
		$this->title = $title;
	}
	public function getTitle() {
		return $this->title;
	}
	// link;
	public function setLink($link) {
		$this->link = $link;
	}
	public function getLink() {
		return $this->link;
	}
	// affiliate_link;
	public function setAffiliateLink($affiliate_link) {
		$this->affiliate_link = $affiliate_link;
	}
	public function getAffiliateLink() {
		return $this->affiliate_link;
	}
	// content;
	public function setContent($content) {
		$this->content = $content;
	}
	public function getContent() {
		return $this->content;
	}
	// photo_link;
	public function setPhotoLink($photo_link) {
		$this->photo_link = $photo_link;
	}
	public function getPhotoLink() {
		return $this->photo_link;
	}
	// code;
	public function setCode($code) {
		$this->code = $code;
	}
	public function getCode() {
		return $this->code;
	}
	// normal_price;
	public function setNormalPrice($normal_price) {
		$this->normal_price = $normal_price;
	}
	public function getNormalPrice() {
		return $this->normal_price;
	}
	// promo_price;
	public function setPromoPrice($promo_price) {
		$this->promo_price = $promo_price;
	}
	public function getPromoPrice() {
		return $this->promo_price;
	}
	// discount;
	public function setDiscount($discount) {
		$this->discount = (integer)$discount;
	}
	public function getDiscount() {
		return $this->discount;
	}
	// quantity;
	public function setQuantity($quantity) {
		$this->quantity = (integer)$quantity;
	}
	public function getQuantity() {
		return $this->quantity;
	}
	// shop;
	public function setShop($shop) {
		$this->shop = $shop;
	}
	public function getShop() {
		return $this->shop;
	}
	// status;
	public function setStatus($status) {
		$this->status = $status;
	}
	public function getStatus() {
		return $this->status;
	}


}
?>
