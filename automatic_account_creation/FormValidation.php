<?php

require_once('AccountCreationException.php');

/**
 * Validates the customer data array to conform to Authorize/SMSPlatform Standards.
 * @author Corbin Schwalm | AvidMobile
 */
class FormValidation {
	
	private $firstName;
	private $lastName;
	private $companyName;
	private $email;
	private $phone;
	
	private $creditCard;
	private $expirationDateMonth;
	private $expirationDateYear;
	private $cvv;
	private $billingFirstName;
	private $billingLastName;
	private $billingStreetAddress1;
	private $billingStreetAddress2;
	private $billingCity;
	private $billingState;
	private $billingZip;
	private $billingCountry;
	
	private $planID;
	private $terms;
	
	/**
	 * Contains a list of the errors with form input | null otherwise
	 * @var array $errors
	 */
	private $errors;
	
	/**
	 * Default Constructor to validate form input
	 * @param array $customerData
	 * @param string $productHandle
	 */
	public function __construct($customerData) {
		
		foreach ($customerData as $key => $element) {
		
			$this->$key = (string) $element;
		}
		
		$this->errors = array();
	} //end constructor
	
	/**
	 * Validates the customer's input bassed whether it is empty or other sanity checks.
	 */
	public function validateInput() {
		
		$this->validateEmpty(); //Empty Check
		
		$this->validateSanity(); //Sanity Checks
		
		if ( (sizeof($this->errors) != 0) ) {
		
			throw new AccountCreationException('The form validation failed.', 0, $this->errors);
		}
		
	} //end validateInput
	
	/**
	 * Ensures none of the customer data fields are empty
	 */
	private function validateEmpty() {
		
		if (empty($this->firstName) || empty($this->lastName) || empty($this->companyName) || empty($this->email) || empty($this->phone) || 
			empty($this->creditCard) || empty($this->expirationDateMonth) || empty($this->expirationDateYear) ||
			empty($this->cvv) || empty($this->billingFirstName) || empty($this->billingLastName) || empty($this->billingStreetAddress1)||
			empty($this->billingCity) || empty($this->billingState) || empty($this->billingZip) || !isset($this->planID) ) {
				
			$this->errors[] = 'One or more of the form fields are empty.';
				
		}
	} //end validateEmpty
	
	/**
	 * Checks form fields for acceptable values.
	 */
	private function validateSanity() {
		
		if ( !(preg_match('/^[A-Za-z]+$/', $this->firstName)) || !( preg_match('/^[A-Za-z]+$/', $this->lastName)) || !( preg_match('/^[A-Za-z ]+$/', $this->billingCity))
			|| !( preg_match('/^[A-Za-z]+$/', $this->billingState)) ) {
				
			$this->errors[] = 'One or more of the fields contains a non-valid character.';
		}
		
		if (strlen($this->phone) != 14) {
				
			$this->errors[] = 'The phone number is invalid.';
		}
		
		if (!preg_match('/^[a-z0-9_\-\.]+@[a-z0-9\-\.]+$/', $this->email)) {
				
			array_push($this->errors, 'The e-mail address is invalid.');
		}
		
		if (!preg_match('/^[0-9]{3,5}$/', $this->cvv)) {
				
			$this->errors[] = 'The credit card security code is invalid.';
		}
		
		if (!preg_match('/^[0-9]{1,2}$/', $this->expirationDateMonth)) {
		
			$this->errors[] = 'The credit card expiration date is invalid.';
		}
		
		if (!preg_match('/^[0-9]{4}$/', $this->expirationDateYear)) {
		
			$this->errors[] = 'The credit card expiration date is invalid.';
		}
		
		if (!preg_match('/^[0-9]{13,16}|[1]{1}$/', $this->creditCard)) {
		
			$this->errors[] = 'The credit card number is invalid.';
		}
		
		if (!preg_match('/^[0-9]{5}$/', $this->billingZip)) {
				
			$this->errors[] = 'One or more of the zip codes are invalid.';
		}

		if (!preg_match('/^[0-9]{1,2}$/', $this->planID)) {
		
			$this->errors[] = 'Please select a plan.';
		}

		if ($this->terms != "1") {
			
			$this->errors[] = 'You must agree to the terms.';
		}

	} //end validateSanity
	
	/**
	 * Converts phone numbers to human readable form and sanitizes input.
	 * @param array $postArray
	 * @return array $array
	 */
	public static function sanitize($postArray) {
		
		if ($postArray == NULL)
			return array();
		
		$array = array();
		
		foreach ($postArray as $key => $value) {
			
			$array[$key] = $value;
		}
		
		foreach ($array as $key => $element) {
			
			if ($key == 'email') {
				
				$array[$key] = htmlspecialchars($array[$key]);
				
			} else if ($key == 'phone') {
				
				$array[$key] = preg_replace('/[^0-9]/', '', $array[$key]);
				
				if (preg_match('/^[0-9]{10}$/', $array[$key]))
					$array[$key] = '(' . substr($array[$key], 0, 3) . ')' . ' ' . substr($array[$key], 3, 3) . '-' . substr($array[$key], 6, 4);
				
			} else {
			
				$array[$key] = trim($array[$key]);
			
				$array[$key] = ucfirst($array[$key]);
				
				$array[$key] = htmlspecialchars($array[$key]);
			}
		}

		return $array;
	}
	
} // end FormValidation

?>