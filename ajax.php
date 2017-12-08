<?php
/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 28/11/2017
* @desc			Ajax
*/

use App\Utils;
use App\Relation;
use App\RelationManager;
use App\CategoryManager;

require_once( "inc/prepend.php" );
//$user->isLoggedIn(); // Espace privé
// Récupération des variables
$action				= Utils::get_input('action','both');
$category_id		= Utils::get_input('cat','post');
$item_id			= Utils::get_input('item','post');

$relation_manager = new RelationManager($bdd);
$category_manager = new CategoryManager($bdd);

// determine si c'est une categorie ou une sous-categorie
$category = $category_manager->getCategory($category_id);

if ($category->parent == 0) { //si c'est une categorie principal
	// alors enregistre la relation avec la categorie principal
	$data = array("item_id" => $item_id, "category_id" => $category_id);
	$relation_manager->saveRelation(new Relation($data));
}
else { //si c'est une sous-categorie
	// alors enregistre la relation avec la categorie principal
	$data = array("item_id" => $item_id, "category_id" => $category->parent);
	$relation_manager->saveRelation(new Relation($data));
	// alors enregistre la relation avec la sous-categorie
	$data = array("item_id" => $item_id, "category_id" => $category_id);
	$relation_manager->saveRelation(new Relation($data));
}


?>