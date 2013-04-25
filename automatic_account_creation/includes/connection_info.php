<?php
/*
 * Filename: connection_info.php
 * 
 * This file includes the connection information for Authorize.Net and the SMS Platform.
 * 
 * This is the only place this info needs to be entered.
 */
 
/* MySQL Database Connection Infomation. Used for the recurrent billing database. */

define('DATABASE_USERNAME', '');

define('DATABASE_PASSWORD', '');

define('DATABASE_NAME', '');

define('DATABASE_HOSTNAME', 'localhost');


/* Authorize.Net API Credientials. */

define('AUTHORIZENET_API_LOGIN_ID', '');

define('AUTHORIZENET_TRANSACTION_KEY', '');

define("AUTHORIZENET_SANDBOX", false);


/* SMS Platform Credientials. */

define('MC_USERNAME', '');

define('MC_PASSWORD', '');

define('MC_CUSTOMERID', '');

define('MC_LEVEL', '2');


/* Application Options. */

/* The name of your company as you want it to appear in various places. */
define('COMPANY_NAME', 'Test Company');

/* The address the customers will login to the SMS Platform at. */
define('LOGIN_URL', 'http://login.avidmobile.com');

/* The relative path to your logo to brand the application. */
define('LOGO_PATH', 'aacc_images/logo.png');

/* The e-mail address to receive an alert if something goes wrong. */
define('ADMIN_EMAIL_ADDRESS', '');

/* The timezone you live in. */
date_default_timezone_set('America/Chicago');

?>