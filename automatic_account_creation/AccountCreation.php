<?php
/*
 * Title: SMS Platform Intrgration & Authorize.Net
 * Date: 08/21/2012
 * Author: Corbin Schwalm | AvidMobile
 * Version: 3.0
 * Requires: Authorize.Net & SMSPlatform Packages
 * 
 * This application accepts form input and then creates an Authorize.Net CIM profile.
 * Then the customer profile information is inserted into the recurrent billing database.
 * The application then creates a SMS Platform with the customers information.
 * It reads the MC_LEVEL in connection_info.php to determine the correct account level to create.
 * A success screen is then displayed with a welcome email being sent.
 * 
 * Main method starts below Account Creation Class.
 */
 
require_once('includes/functions.php');
require_once('includes/pricing.php');
require_once('includes/connection_info.php');
require_once('anet_php_sdk/AuthorizeNet.php');

/**
 * Creates a customer account in marketing platform after ensurance of payment being processed.
 * 
 * @author Corbin Schwalm | AvidMobile
 * @version 3.0
 * @package automatic_account_creation
 */
class AccountCreation {
		
	/** Storage for the Customer object. Contains personal info. */	
	private $customer;
	
	/** Storage for the price plan object. Contains info about the plan options. */
	private $pricePlan;
	
	/** Storage for the MYSQLi object for the recurrent billing database. */
	private $dB;
	
	/** The profile ID for the customer in CIM. */
	private $customerProfileId;
	
	/** The payment ID for the added payment info in CIM. */
	private $customerPaymentId;
	
	/** Storage for the organization object. */
	private $organization;
	
	/** Storage for the reseller object. */
	private $reseller;

	/**
	* Default Constructor for AccountCreation Class.
	*
	* @param Customer $customer
	* @param PricePlan $pricePlan 
	*/
	public function __construct($customer, $pricePlan) {
		
		$this->customer = $customer;
		
		$this->pricePlan = $pricePlan;
		
		$this->dB = new mysqli(DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
		
	} //end constructor
	
	/**
	 * Processes the payment using the Authorize.Net package. This only charges the PricePlan's one-time setupFee.
	 * Recurrent billing is not handled in this application.
	 * 
	 * @throws AccountCreationException Throws exception if payment fails.
	 */
	public function processPayment() {
			
		$request = new AuthorizeNetCIM;
		$customerProfile = new AuthorizeNetCustomer;
		$paymentProfile = new AuthorizeNetPaymentProfile;
		
		/* Customer Profile */
		$customerProfile->description = $this->pricePlan->planName . ' - Reseller';
		$customerProfile->merchantCustomerId = $this->customer->companyName;
		$customerProfile->email = $this->customer->email;
		
		/* Customer Payment Profile */
		$paymentProfile->customerType = "business";

		$paymentProfile->billTo->firstName = $this->customer->firstName;
		$paymentProfile->billTo->lastName = $this->customer->lastName;
		$paymentProfile->billTo->company = $this->customer->companyName;
		$paymentProfile->billTo->address = $this->customer->billingStreetAddress1 . ' ' . $this->customer->billingStreetAddress2;
		$paymentProfile->billTo->city = $this->customer->billingCity;
		$paymentProfile->billTo->state = $this->customer->billingState;
		$paymentProfile->billTo->zip = $this->customer->billingZip;
		$paymentProfile->billTo->country = $this->customer->country;
		$paymentProfile->billTo->phoneNumber = $this->customer->phone;
		
		$paymentProfile->payment->creditCard->cardNumber = $this->customer->creditCard;
		$paymentProfile->payment->creditCard->expirationDate = $this->customer->expirationDateYear . "-" . $this->customer->expirationDateMonth;
		$paymentProfile->payment->creditCard->cardCode = $this->customer->cvv;
		$customerProfile->paymentProfiles[] = $paymentProfile;
		
	
		$response = $request->createCustomerProfile($customerProfile); //Creates a contact profile in CIM.
		
		if ($response->xml->messages->resultCode != "Ok")
			throw new AccountCreationException('<li>Your credit card information was rejected.</li>', 1, '');
		
		$this->customerProfileId = $response->getCustomerProfileId();
		
		$this->customerPaymentId = $response->getCustomerPaymentProfileIds();
		
		/* Create Authorize & Capture Transaction. */
		$order = new AuthorizeNetTransaction;
		$transaction = new AuthorizeNetTransaction;
		$transaction->amount = $this->pricePlan->setupFee;
		$transaction->customerProfileId = $this->customerProfileId;
		$transaction->customerPaymentProfileId = $this->customerPaymentId;
		
		$order->description = $this->pricePlan->planName . ' - Setup Fee';
		$transaction->cardCode = $this->customer->cvv;
		$transaction->order = $order;
		$extraOptions = urlencode('customer_ip=' . $_SERVER['REMOTE_ADDR'] . '&email_customer=FALSE');
		
		
		$response = $request->createCustomerProfileTransaction("AuthCapture", $transaction, $extraOptions); //Charges Setup Fee.
		$transactionResponse = $response->getTransactionResponse();
		
		if (empty($transactionResponse->approved)) {
			
			$request->deleteCustomerProfile($this->customerProfileId);
			
			throw new AccountCreationException('Your credit card information was rejected.', 2, '');
		}
		
	} //end processPayment
	
	/**
	 * Adds the required information to the AvidMobile recurrent billing processor. The actual monthly billing is
	 * handled by the cron job.
	 *
	 * @throws AccountCreationException Throws exception if the insert fails.
	 */
	public function setupRecurrentBilling() {
		
		$sql = "INSERT INTO `auto_bill`(anet_customer_id, anet_payment_id, platform_id, chargedate, org, country)
			VALUES('".$this->customerProfileId."', '".$this->customerPaymentId."', '".$this->organization->getCustomerId()."', '".date('Y-m-d')."', '".$this->customer->companyName."', '".$this->customer->country."')";
		
		$this->dB->query($sql); //Insert Customer Info.
		
		if ($this->dB->errno != '0')
			throw new AccountCreationException('Failed to add customer information to recurrent billing database.', 3, $this->dB->error);
		
	} //end setupRecurrentBilling
	
	/**
	 * Creates a reseller level user in the SMS platform.
	 * 
	 * @throws AccountCreationException Throws exception if the account or universial username can't be created.
	 */
	public function createReseller() {
		
		$result = '';
		
		$this->organization = new Organization($this->customer, $this->pricePlan);
		
		try {
			
			$result = $this->organization->createReseller();
		
		} catch (Exception $ex) {
			
			throw new AccountCreationException('Failed to create reseller account in SMS platform.', 4, $result);
		}
		
		if ($result->ErrorCode != '0' || preg_match('/username/', $result->ErrorDetails))
			throw new AccountCreationException('Failed to create reseller account in SMS platform.', 5, $result);
		
	} //end createReseller
	
		/**
	 * Creates a reseller level user in the SMS platform.
	 * 
	 * @throws AccountCreationException Throws exception if the account or universial username can't be created.
	 */
	public function createMC() {
		
		$result = '';
		
		$this->reseller = new Reseller($this->customer, $this->pricePlan);
		
		try {
			
			$result = $this->reseller->createMC();
		
		} catch (Exception $ex) {
			
			throw new AccountCreationException('Failed to create MC account in SMS platform.', 4, $result);
		}
		
		if ($result->ErrorCode != '0' || preg_match('/username/', $result->ErrorDetails))
			throw new AccountCreationException('Failed to create MC account in SMS platform.', 5, $result);
		
		$this->organization = $this->reseller; //Patch to allow MCs to be created with minor code edits.
		
	} //end createMC
	
	/**
	 * Sends a welcome email to the paid customer with his/her login information.
	 * 
	 * @throws AccountCreationException Throws exception if the welcome e-mail fails to send.
	 */
	public function sendWelcomeEmail() {
		
		$emailTo = $this->customer->email;
		
		$emailFrom = 'website@' . $_SERVER['HTTP_HOST'];
		
		$companyName = COMPANY_NAME;
		
		$loginURL = LOGIN_URL;
		
		$date = date("Y-m-d H:i:s");
			
		$emailSubject = "Welcome to $companyName!";
			
		$emailHeaders = "MIME-Version: 1.0\r\n";
		$emailHeaders .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$emailHeaders .= 'From: ' . $emailFrom . "\r\n";	
			
		$emailBody = <<<EOF
		
			<h2 style="text-align:center;">Welcome to {$companyName}!</h2><br />
			<h3 style="text-align:center;">Your account login infomation is below.<br />
			Don't hesitate to contact us if you have any questions.</h3>
				
			<table border="0" align="center">
			<tbody>
			<tr>
			<td><span style="font-size: small;">Username:</span></td>
			<td><span style="font-size: small;">{$this->organization->getUsername()}</span></td>
			</tr>
			<tr>
			<td><span style="font-size: small;">Password:</span></td>
			<td><span style="font-size: small;">{$this->organization->getPassword()}</span></td>
			</tr>
			<tr>
			<td><span style="font-size: small;">SMS Platform Link:    </span></td>
			<td><span style="font-size: small;"><a href="{$loginURL}" target="_blank">{$loginURL}</a></span></td>
			</tr>
			</tbody>
			</table>
				
EOF;
		$result = @mail($emailTo, $emailSubject, $emailBody, $emailHeaders);
		
		if ($result !== true)
			throw new AccountCreationException("The welcome e-mail failed to send.", 5, $emailBody);
		
	} //end sendWelcomeEmail
	
	/**
	 * Redirects the user to the payment success page using JavaScript.
	 */
	public function successRedirect() {
		
		echo '<script type="text/javascript">window.location="http://' . $_SERVER['HTTP_HOST'] . '/automatic_account_creation/payment_success.php?loginURL=' . LOGIN_URL . '";</script>';
	
	} //end generatePaymentReceivedHTML

} //end AccountCreation Class

if (empty($_POST['aCForm']))
	die('Only accepts form input.');

$customerData = FormValidation::sanitize($_POST['aCForm']);

$formValidator = new FormValidation($customerData);

try {

	$formValidator->validateInput();

} catch (AccountCreationException $ex) {
	
	$ex->handleError(false);
}

$customer = new Customer($customerData);

$pricePlan = new PricePlan(getPricePlanArray($customerData['planID']));

$aC = new AccountCreation($customer, $pricePlan);

try {
	
	$aC->processPayment();

} catch (AccountCreationException $ex) {
	
	$ex->handleError(false);
}

try {
	
	if (MC_LEVEL == '1') {
		
		$aC->createMC();
		
	} else if (MC_LEVEL == '2') {
	
		$aC->createReseller();
		
	}
	
	$aC->setupRecurrentBilling();
	
	$aC->sendWelcomeEmail();

} catch (AccountCreationException $ex) {
	
	$ex->handleError(true);
}

$aC->successRedirect();

exit; //End Of Application.

?>