<?php
/*
 * Filename: admin_functions.php
 * 
 * This file contains all non-OOP functions for the administration control panel.
 * This includes the custom __autoload() functions.
 */
 
function autoload($class_name) {
	
	$file = '../' . $class_name. '.php';

	if (file_exists($file)) {

		require_once($file);
	}
}
 
function autoload_SMSPlatform($class_name) {

	$file = '../SMSPlatform/' . $class_name. '.php';

	if (file_exists($file)) {

		require_once($file);
	}
}

spl_autoload_register('autoload_SMSPlatform');
spl_autoload_register('autoload');

?>