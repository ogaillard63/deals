<?php
/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 01/12/2017
* @desc			Controleur des objets : relations
*/

use App\Utils;
use App\Relation;
use App\RelationManager;
use App\Pagination;

require_once( "inc/prepend.php" );
// Récupération des variables
$action			= Utils::get_input('action','both');
$page			= Utils::get_input('page','both');
$item_id		= Utils::get_input('item_id','both');
$category_id	= Utils::get_input('category_id','post');


$relation_manager = new RelationManager($bdd);

switch($action) {
	
	case "add" :
		$smarty->assign("relation", new Relation(array()));
		$smarty->assign("content", "bo/relations/edit.tpl.html");
		$smarty->display("bo/main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("relation", $relation_manager->getRelation($id));
		$smarty->assign("content","bo/relations/edit.tpl.html");
		$smarty->display("bo/main.tpl.html");
		break;


	case "save" :
		$data = array("item_id" => $item_id, "category_id" => $category_id);
		$relation_manager->saveRelation(new Relation($data));
		$log->notification($translate->__('the_relation_has_been_saved'));
		Utils::redirection("relations.php");
		break;

	case "delete" :
		$relation = $relation_manager->getRelation($id);
		if ($relation_manager->deleteRelation($relation)) {
			$log->notification($translate->__('the_relation_has_been_deleted'));
		}
		Utils::redirection("relations.php");
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_relations'));
		$rpp = 10;
		if (empty($page)) $page = 1; // Display first page
		$smarty->assign("relations", $relation_manager->getRelationsByPage($page, $rpp));
		$pagination = new Pagination($page, $relation_manager->getMaxRelations(), $rpp);
		$smarty->assign("btn_nav", $pagination->getNavigation());

		$smarty->assign("content", "bo/relations/list.tpl.html");
		$smarty->display("bo/main.tpl.html");
}
require_once( "inc/append.php" );
?>