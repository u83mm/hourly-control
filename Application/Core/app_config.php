<?php	

	/** Define root folder */
	define("SITE_ROOT", $_SERVER['DOCUMENT_ROOT']);
	//define('URL', $_SERVER['REQUEST_URI']);	

	/** Define database configuration file */
	define('DB_CONFIG_FILE', SITE_ROOT . '/../Application/Core/db.config.php');	
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php");
	
	/** Define connection */
	require_once(SITE_ROOT . "/../Application/Core/connect.php");
	define('DB_CON', $dbcon);	

	//session_start();
	//session_regenerate_id();
?>
