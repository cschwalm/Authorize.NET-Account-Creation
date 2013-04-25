<?php

/**
 * This class provides functionality for the MarketingCenter operations on the SMS Platform.
 * 
 * @author Corbin Schwalm | AvidMobile
 */
class Reseller extends SMSPlatformConnector {
		
	/** Storage for the Customer object. */
	private $customer;
	
	/** Storage for the PricePlan object. */
	private $pricePlan;
	
	/** SMS Platform name for the customer. */
	private $id;
	
	/** Username for the customer. It is the customer's email address. */
	private $username;
	
	/** The customers randomly generated password. */
	private $password;
	
	/** The shortcode to bind the customer to. */
	private $SHORT_CODE = '72727';
	
	/** Storage for the SMS Platform Customer Id. */
	private $customerId;
	
	/** Default constructor.
	 * 
	 * @param Customer $customer
	 * @param PricePlan $pricePlan
	 */
	public function __construct($customer, $pricePlan) {
		
		parent::__construct();
		
		$this->customer = $customer;
		
		$this->pricePlan = $pricePlan;
		
		if ($customer != NULL)
			$this->id = preg_replace('/\W/', '', strtoupper($customer->companyName));
		
		if ($customer != NULL)
			$this->username = $customer->email;
		
		$this->password = parent::generatePassword();
	
	} //end __construct
	
	/**
	 * Returns the ID for the reseller.
	 * @return string $id
	 */
	public function getId() {
		
		return $this->id;
	}
	
	/**
	* Returns the username for the reseller.
	* @return string $username
	*/
	public function getUsername() {
		
		return $this->username;
	}
	
	/**
	* Returns the password for the reseller.
	* @return string $password
	*/
	public function getPassword() {
		
		return $this->password;
	}
	
	/**
	 * Returns the MC SMS Platform ID.
	 * @return string $id
	 */
	public function getCustomerId() {
		
		return $this->customerId;
	}
	
	/**
	 * Creates a SMS Platform MC with the specifed user infomation and price plan options.
	 * 
	 * @return String The XML responce from the SMS Platform.
	 */
	public function createMC() {
		
			$arguments = array(
				array('Key' => 'customer_name', 'Value' => $this->id),
				array('Key' => 'customer_readable_name', 'Value' => $this->customer->companyName),
				array('Key' => 'shortcode', 'Value' => $this->SHORT_CODE),
				array('Key' => 'username', 'Value' => $this->username),
				array('Key' => 'password', 'Value' => $this->password),
				array('Key' => 'universal_username', 'Value' => $this->username),
				array('Key' => 'contact_name', 'Value' => $this->customer->firstName . ' ' . $this->customer->lastName),
				array('Key' => 'company_name', 'Value' => $this->customer->companyName),
				array('Key' => 'addr1', 'Value' => $this->customer->billingStreetAddress1),
				array('Key' => 'addr2', 'Value' => $this->customer->billingStreetAddress2),
				array('Key' => 'city', 'Value' => $this->customer->billingCity),
				array('Key' => 'state', 'Value' => $this->customer->billingState),
				array('Key' => 'zip', 'Value' => $this->customer->billingZip),
				array('Key' => 'contact_mobile', 'Value' => $this->customer->phone),
				array('Key' => 'email', 'Value' => $this->customer->email),
				array('Key' => 'contact_email', 'Value' => $this->customer->email),
				array('Key' => 'num_blasts', 'Value' => '100'),
				array('Key' => 'num_keywords', 'Value' => '100'),
	 			array('Key' => 'num_poll', 'Value' => '100'),
				array('Key' => 'num_ar', 'Value' => '100'),
				array('Key' => 'num_tagged_events', 'Value' => '100'),
				array('Key' => 'num_surveys', 'Value' => '100'),
				array('Key' => 'num_text2win', 'Value' => '100'),
				array('Key' => 'num_trivia', 'Value' => '100'),
				array('Key' => 'num_social', 'Value' => '100'),
				array('Key' => 'num_mobilesite', 'Value' => '100'),
				array('Key' => 'enable_mobile_web_builder', 'Value' => 'true'),
				array('Key' => 'enable_contact_manager', 'Value' => 'true'),
				array('Key' => 'enable_redir_urls', 'Value' => 'true'),
				array('Key' => 'billing_cycle_name', 'Value' => $this->pricePlan->planName),
				array('Key' => 'base_rate', 'Value' => $this->pricePlan->monthlyFee),
				array('Key' => 'message_quota', 'Value' => $this->pricePlan->outgoingTextQuota),
				array('Key' => 'message_overage_rate', 'Value' => $this->pricePlan->outgoingTextPrice),
				array("Key" => "keyword_quota", "Value" => $this->pricePlan->keywordQuota),
				array("Key" => "keyword_overage_rate", "Value" => $this->pricePlan->keywordPrice),
				array('Key' => 'msg_quota_enabled', 'Value' => $this->pricePlan->absoluteQuotaEnabled),
				array('Key' => 'msg_quota', 'Value' => $this->pricePlan->absoluteQuota),
				array('Key' => 'msg_quota_duration', 'Value' => $this->pricePlan->absoluteQuotaDuration)	
			);
		
			$result = $this->client->Put($this->auth, 'reseller.createmc', $arguments);
			
			if ($result->ErrorCode == '0')
				$this->customerId = trim(substr($result->ErrorDetails, 1, 6)); //Gets the SMS Platform Customer ID.
		
		return $result;
		
	} //end createMC
	
	/**
	 * Deletes the MC account from the SMS Platform.
	 * 
	 * @param String $customerId The SMS Platform ID - OPTIONAL
	 * @return String The XML responce from the SMS Platform.
	 */
	public function deleteMC($customerId = '') {
		
		if (empty($customerId))
			$customerId = $this->customerId;
		
		$arguments = array(
			array('Key' => 'id', 'Value' => $customerId),
			array('Key' => 'shortcode', 'Value' => $this->SHORT_CODE)
			);
		
		$result = $this->client->Put($this->auth, 'reseller.deletemc', $arguments);
		
		
		
		return $result;
		
	} //end deleteMC
	
} //end Reseller

?>