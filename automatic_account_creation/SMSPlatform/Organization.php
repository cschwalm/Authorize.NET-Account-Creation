<?php

/**
 * This class provides functionality to for the reseller operations on the SMS Platform.
 * 
 * @author Corbin Schwalm | AvidMobile
 */
class Organization extends SMSPlatformConnector {
		
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
	
	/** The shortcode to bind the reseller to. */
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
	 * Returns the reseller SMS Platform ID.
	 * @return string $id
	 */
	public function getCustomerId() {
		
		return $this->customerId;
	}
	
	/**
	 * Creates a SMS Platform reseller with the specifed user infomation and price plan options.
	 * 
	 * @return String The XML responce from the SMS Platform.
	 */
	public function createReseller() {
		
			$arguments = array(
			array('Key' => 'name', 'Value' => $this->id),
			array('Key' => 'readable_name', 'Value' => $this->customer->companyName),
			array('Key' => 'shortcode', 'Value' => $this->SHORT_CODE),
			array('Key' => 'username', 'Value' => $this->username),
			array('Key' => 'password', 'Value' => $this->password),
			array('Key' => 'universal_username', 'Value' => $this->username),
			array('Key' => 'contact_name', 'Value' => $this->customer->firstName . ' ' . $this->customer->lastName),
			array('Key' => 'mobile', 'Value' => $this->customer->phone),
			array('Key' => 'email', 'Value' => $this->customer->email),
			array('Key' => 'plan_name', 'Value' => $this->pricePlan->planName),
			array('Key' => 'base_rate', 'Value' => $this->pricePlan->monthlyFee),
			array('Key' => 'message_quota', 'Value' => $this->pricePlan->outgoingTextQuota),
			array('Key' => 'message_overage_rate', 'Value' => $this->pricePlan->outgoingTextPrice),
			array('Key' => 'keyword_quota', 'Value' => $this->pricePlan->keywordQuota),
			array('Key' => 'keyword_overage_rate', 'Value' => $this->pricePlan->keywordPrice),
			array('Key' => 'client_quota', 'Value' => $this->pricePlan->clientQuota),
			array('Key' => 'client_overage_rate', 'Value' => $this->pricePlan->clientMonthlyPrice),
			array('Key' => 'msg_quota', 'Value' => $this->pricePlan->absoluteQuota),
			array('Key' => 'msg_quota_duration', 'Value' => $this->pricePlan->absoluteQuotaDuration),
			array('Key' => 'msg_quota_enabled', 'Value' => $this->pricePlan->absoluteQuotaEnabled)
			);
		
			$result = $this->client->Put($this->auth, 'organization.createreseller', $arguments);
			
			if ($result->ErrorCode == '0')
				$this->customerId = substr($result->ErrorDetails, 1, 4); //Gets the SMS Platform Customer ID.
		
		return $result;
		
	} //end createReseller
	
	/**
	 * Deletes the reseller account from the SMS Platform.
	 * 
	 * @param String $customerId The SMS Platform ID - OPTIONAL
	 * @return String The XML responce from the SMS Platform.
	 */
	public function deleteReseller($customerId = '') {
		
		if (empty($customerId))
			$customerId = $this->customerId;
		
		$arguments = array(
			array('Key' => 'id', 'Value' => $customerId),
			array('Key' => 'shortcode', 'Value' => $this->SHORT_CODE)
			);
		
		$result = $this->client->Put($this->auth, 'organization.deletereseller', $arguments);
		
		return $result;
		
	} //end deleteReseller
	
} //end Reseller

?>