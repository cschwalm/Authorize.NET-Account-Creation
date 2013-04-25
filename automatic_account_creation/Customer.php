<?php
/**
 * This class creates a Customer object whose fields specify a customer's personal infomation.
 * This class uses the global variable POST to fill it's fields.
 */
 class Customer {
 	
	private $firstName;
	private $lastName;
	private $companyName;
	private $email;
	private $phone;
	private $country = 'US';
	
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
	
	/**
	 * Constructs a Customer object using the passed POST array to fill the data members.
	 * Key/Value Pairs must match.
	 * 
	 * @param array $postData The POST data from the submitted form.
	 */
	public function __construct($postData) {
		
		foreach ($postData as $key => $value) {
			
			$this->$key = $value;
		}
		
	} //end __construct
	
	/**
	 * Magic __get function. Gets the values for private fields.
	 * This function is called automatically by PHP.
	 * 
	 * @param K $field THe name of the private field to get.
	 * @return K The field.
	 */
	public function __get($field) {
		
		return $this->$field;
	}
	
} //end Customer
 
?>