<?php
/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 01/12/2017
* @desc			Controleur des objets : categories
*/

use App\Utils;
use App\Category;
use App\CategoryManager;
use App\Pagination;

require_once( "inc/prepend.php" );
// Récupération des variables
$action			= Utils::get_input('action','both');
$page			= Utils::get_input('page','both');
$id				= Utils::get_input('id','both');
$name			= Utils::get_input('name','post');
$path			= Utils::get_input('path','post');
$description	= Utils::get_input('description','post');
$sort			= Utils::get_input('sort','post');
$parent			= Utils::get_input('parent','post');

$query			= Utils::get_input('query','post');

$category_manager = new CategoryManager($bdd);

switch($action) {
	
	case "add" :
		$smarty->assign("category", new Category(array("id" => -1)));
		$smarty->assign("categories", $category_manager->getParentCategoriesForSelect());
		$smarty->assign("content", "bo/categories/edit.tpl.html");
		$smarty->display("bo/main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("category", $category_manager->getCategory($id));
		$smarty->assign("categories", $category_manager->getParentCategoriesForSelect());
		$smarty->assign("content","bo/categories/edit.tpl.html");
		$smarty->display("bo/main.tpl.html");
		break;

	case "search" :
		$smarty->assign("content","bo/categories/search.tpl.html");
		$smarty->display("bo/main.tpl.html");
		break;

	case "search_results" :
		if (strlen($query) > 2) {
			$smarty->assign("categories", $category_manager->searchCategories($query));
		}
		else {
			$log->notification($translate->__('query_too_short'));
			Utils::redirection("categories.php?action=search");
		}
		$smarty->assign("query",$query);
		$smarty->assign("content","bo/categories/search.tpl.html");
		$smarty->display("bo/main.tpl.html");
		break;

	case "save" :
		$data = array("id" => $id, "name" => $name, "path" => $path, "description" => $description, "sort" => $sort, "parent" => $parent);
		$category_manager->saveCategory(new Category($data));
		$log->notification($translate->__('the_category_has_been_saved'));
		Utils::redirection("categories.php");
		break;

	case "delete" :
		$category = $category_manager->getCategory($id);
		if ($category_manager->deleteCategory($category)) {
			$log->notification($translate->__('the_category_has_been_deleted'));
		}
		Utils::redirection("categories.php");
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_categories'));
		$smarty->assign("categories", $category_manager->getCategories());
		$smarty->assign("content", "bo/categories/list.tpl.html");
		$smarty->display("bo/main.tpl.html");
}
require_once( "inc/append.php" );
?>