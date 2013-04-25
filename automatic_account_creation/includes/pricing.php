<?php
/*
 * Filename: pricing.php
 * 
 * This file contains the function that creates an array of PricePlan values
 * based on the plan id passed.
 * 
 * EDIT THIS FILE TO CHANGE PRICES!
 */

/**
 * Gets the key/value array needed by the PricePlan constructor.
 * 
 * If quota is set to '0', then client is charged the price for every occurance.
 * 
 * @param String $planID ID accepted from the web form.
 * @return Array Key/Value pair for price info.
 */
function getPricePlanArray($planID) {
	
	$priceInfo = array();
	
	switch($planID) {
		
		case 0: //Plan ID 0
		
			/** The one time fee to charge for setup. */
			$priceInfo['setupFee'] = '99.99';
		
			/** Human readable name for the plan. */
			$priceInfo['planName'] = 'Starter Plan';
			
			/** The flat fee to charge the reseller every month. In 'X.XX' format. */
			$priceInfo['monthlyFee'] = '47.00';
		
			/** The fee for each outgoing text. In 'X.XX' format. */
			$priceInfo['outgoingTextPrice'] = '0.02';
			
			/** The amount of messages to grant without fee. */
			$priceInfo['outgoingTextQuota'] = '0';
		
			/** The price the reseller pays for each client. Ignored for MC creation. */
			$priceInfo['clientMonthlyPrice'] = '0.00';
			
			/** The amount of clients to grant without fee. Ignored for MC creation. */
			$priceInfo['clientQuota'] = '0';
		
			/** The fee for each keyword used. In 'X.XX' format. */
			$priceInfo['keywordPrice'] = '5.00';
		
			/** The amount of keywords to grant without fee. */
			$priceInfo['keywordQuota'] = '0';
			
			/** Enables absolute message quota. Enable this feature to glabally limit the number of text messages that may be sent. */
			$priceInfo['absoluteQuotaEnabled'] = 'true';
	
			/** The number of messages to allow globally for the specified duration. Numbers Only. Ignored for MC creation. */
			$priceInfo['absoluteQuota'] = '10000';
	
			/** The duration the quota applies for in days. Ignored for MC creation. */
			$priceInfo['absoluteQuotaDuration'] = '30';
			
		break;
		
		case 1: //Plan ID 1
		
			/** The one time fee to charge for setup. */
			$priceInfo['setupFee'] = '0.00';
		
			/** Human readable name for the plan. */
			$priceInfo['planName'] = 'Blank Plan';
			
			/** The flat fee to charge the reseller every month. In 'X.XX' format. */
			$priceInfo['monthlyFee'] = '0.00';
		
			/** The fee for each outgoing text. In 'X.XX' format. */
			$priceInfo['outgoingTextPrice'] = '0.00';
			
			/** The amount of messages to grant without fee. */
			$priceInfo['outgoingTextQuota'] = '0';
		
			/** The price the reseller pays for each client. Ignored for MC creation. */
			$priceInfo['clientMonthlyPrice'] = '0.00';
			
			/** The amount of clients to grant without fee. Ignored for MC creation. */
			$priceInfo['clientQuota'] = '0';
		
			/** The fee for each keyword used. In 'X.XX' format. */
			$priceInfo['keywordPrice'] = '0.00';
		
			/** The amount of keywords to grant without fee. */
			$priceInfo['keywordQuota'] = '0';
			
			/** Enables absolute message quota. Enable this feature to glabally limit the number of text messages that may be sent. */
			$priceInfo['absoluteQuotaEnabled'] = 'true';
	
			/** The number of messages to allow globally for the specified duration. Numbers Only. Ignored for MC creation. */
			$priceInfo['absoluteQuota'] = '0';
	
			/** The duration the quota applies for in days. Ignored for MC creation. */
			$priceInfo['absoluteQuotaDuration'] = '30';
			
		break;
	}
	
	return $priceInfo;
	
} //end getPricePlanArray


/**
 * Generates the HTML form code for the price plans. The HTML code updates as the above plans are changed.
 * 
 * @param String $planID The planID to set the default value to. OPTIONAL
 * @return The <option> values.
 */
function getPricePlanFormHTML($planID = '') {
		
	if (empty($planID))
	$html = '<option value="-1" selected="selected">Please Select A Plan</option>';
	
	$pricePlanArray;
	
	for ($i = 0; $i < 50; $i++) {
		
		$selected = '';
		
		$pricePlanArray = getPricePlanArray($i);
		
		//if ($i == $planID)
			//$selected = ' selected="selected"';
		
		if (!empty($pricePlanArray['planName']))
			$html .= '<option value="' . $i . '"' . (!empty($selected) ? $selected : '') . '>' . $pricePlanArray['planName'] . ' - $' . $pricePlanArray['monthlyFee'] . ' / Month</option>';	
	}
	
	echo $html;
	
} //end getPricePlanFormHTML

if (isset($_GET['planID']) && $_GET['planID'] != '-1') {
	
	$pricePlan = getPricePlanArray($_GET['planID']);
	
	echo 'Your credit card will be charged $' . $pricePlan['setupFee'] . ' today!';
}

if (isset($_GET['requestPlanInfo'])) {
	
	getPricePlanFormHTML();
}

?>