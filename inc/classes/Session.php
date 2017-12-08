<?php
namespace App;

class Session {

	private static $_instance;
	
	public static function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new Session();
			//echo "new Session";
		}
		return self::$_instance;
	}

	private function __construct() {
		session_start();
	}

	public function getValue($key) {
		return isset($_SESSION[$key]) ? $_SESSION[$key] : FALSE;
	}

	public function setValue($key, $value) {
		$_SESSION[$key] = $value;
	}

	public function unsetkey($key) {
		unset($_SESSION[$key]);
	}
	public function getReferer() {
		return isset($_SESSION["referer"]) ? $_SESSION["referer"] : FALSE;
	}

	public function setReferer() {
		if (isset($_SERVER['HTTP_REFERER'])) $referer = parse_url(($_SERVER['HTTP_REFERER'] == $_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['HTTP_REFERER']);
		else $referer = parse_url($_SERVER['PHP_SELF']);
		//$referer = parse_url(($_SERVER['HTTP_REFERER'] == $_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['HTTP_REFERER']);
		$_SESSION["referer"] = $referer["path"];
	}

	public function login($pValues = array()) {
		$_SESSION['authentication_ip'] = $_SERVER['REMOTE_ADDR'];
		foreach ($pValues as $key => $value) {
			$_SESSION[$key] = $value;
		}
	}

	public function logout() {
		session_unset();
		session_destroy();
	}

	public function isLogged() {
		return isset($_SESSION['authentication_ip']) &&
		$_SESSION['authentication_ip'] == $_SERVER['REMOTE_ADDR'];
	}
}
?>