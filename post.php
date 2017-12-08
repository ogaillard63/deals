<?php
/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 28/11/2017
* @desc			Controleur des objets : items
*/

use App\Utils;
use App\Item;
use App\ItemManager;
use App\Pagination;

require_once( "inc/prepend.php" );
// Récupération des variables
$action				= Utils::get_input('action','both');
$id					= Utils::get_input('id','both');
$created			= Utils::get_input('created','post');
$expired			= Utils::get_input('expired','post');
$title				= Utils::get_input('title','post');
$link				= Utils::get_input('link','post');
$affiliate_link		= Utils::get_input('affiliate_link','post');
$content			= Utils::get_input('content','post');
$photo_link			= Utils::get_input('photo_link','post');
$code				= Utils::get_input('code','post');
$normal_price		= Utils::get_input('normal_price','post');
$promo_price		= Utils::get_input('promo_price','post');
$discount			= Utils::get_input('discount','post');
$quantity			= Utils::get_input('quantity','post');
$shop				= Utils::get_input('shop','post');
$status				= Utils::get_input('status','post');

$item_manager = new ItemManager($bdd);

if ($action == "save") {
	$data = array("id" => $id, "created" => $created, "expired" => $expired, 
		"title" => $title, "link" => $link, "affiliate_link" => $affiliate_link, 
		"content" => $content, "photo_link" => $photo_link, "code" => $code, 
		"normal_price" => $normal_price, "promo_price" => $promo_price, 
		"discount" => $discount, "quantity" => $quantity, "shop" => $shop, 
		"status" => $status);
	$item_manager->saveItem(new Item($data));
	break;
}

require_once( "inc/append.php" );
?>