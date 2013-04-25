<?php

/**
 * Provides Funcationality to connect to SMS Platform.
 * @author Corbin Schwalm | AvidMobile
 * @package SMSPlatformConnector
 */
class SMSPlatformConnector {
	
	private static $SERVICE_URL = 'https://login.avidmobile.com/MCSOAP2.1/MarketingCenter2-1.php?wsdl';
	private static $MC_USERNAME = '';
	private static $MC_PASSWORD = '';
	private static $MC_CUSTOMERID = '';
	private static $MC_LEVEL = '';
	
	protected $auth;
	protected $client;
	
	public function __construct() {
		
		$this->auth = array(
					'Username' => MC_USERNAME,
					'Password' => MC_PASSWORD,
					'CustomerId' => MC_CUSTOMERID,
					'Level' => MC_LEVEL,
					'Source' => 'hostname: ' . php_uname('n'),
					'Options' => '0');
		
		$this->client = new SoapClient(SMSPlatformConnector::$SERVICE_URL, array('trace' => 1));
		
	}
	
	/**
	 * Generates Random String 6 Characters Long.
	 * @return string
	 */
	public static function generatePassword() {
	
		$character_set_array = array();
		$character_set_array[] = array('count' => 3, 'characters' => 'abcdefghijklmnopqrstuvwxyz');
		$character_set_array[] = array('count' => 2, 'characters' => '0123456789');
		$character_set_array[] = array('count' => 2, 'characters' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		$character_set_array[] = array('count' => 1, 'characters' => '!@#$%^&*()?');
		$temp_array = array();
		
		foreach ($character_set_array as $character_set) {
			
			for ($i = 0; $i < $character_set['count']; $i++) {
				
				$temp_array[] = $character_set['characters'][mt_rand(0, strlen($character_set['characters']) - 1 )];
			}
		}
		
		shuffle($temp_array);
		
		return (implode( '', $temp_array ));
	} //end generatePassword

} //end SMSPlatformConnector

?>