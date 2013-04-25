<?php
/**
 * This class creates a PricePlan object which stores the options each plan has.
 * It takes a key/value array as
 *
 * @author AvidMobile | Corbin Schwalm
*/

class PricePlan {
		
	/** Human readable name for the plan. */
	private $planName;

	/** The flat fee to charge the reseller every month. In 'X.XX' format. */
	private $monthlyFee;

	/** The fee for each outgoing text. In 'X.XX' format. */
	private $outgoingTextPrice;
	
	/** The amount of clients to grant without fee. */
	private $outgoingTextQuota;

	/** The price the reseller pays for each client. */
	private $clientMonthlyPrice;
	
	/** The amount of clients to grant without fee. */
	private $clientQuota;

	/** The fee for each keyword used. In 'X.XX' format. */
	private $keywordPrice;

	/** The amount of keywords to grant without fee. */
	private $keywordQuota;
	
	/** Enables absolute message quota. Enable this feature to glabally limit the number of text messages that may be sent. */
	private $absoluteQuotaEnabled;
	
	/** The number of messages to allow globally for the specified duration. */
	private $absoluteQuota;
	
	/** The duration the quota applies for in days. */
	private $absoluteQuotaDuration;
	
	
	/**
	 * Constructs a PricePlan object with the fields stored in an array with same key/value pairs.
	 * 
	 * @param array $priceInfoArray
	 */
	public function __construct($priceInfoArray) {
		
		foreach($priceInfoArray as $key => $value) {
			
			$this->$key = (String) $value;
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
	
} //end PricePlan

?>