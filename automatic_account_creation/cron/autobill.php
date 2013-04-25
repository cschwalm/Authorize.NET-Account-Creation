<?php
/*
 * Filename: autobill.php
 * 
 * This file runs once a day and queries the recurrent billing database.
 * If it finds that a customer's bill date is today, it bills them based on the data in the SMS Platform.
 * 
 * This file must be set to run once a day as a CRON job and cannot be accessed by a browser.
 */
 
if ($_SERVER["REMOTE_ADDR"] != $_SERVER["SERVER_ADDR"]) 
	die("Cron Job Access Only"); 
 
require_once('../includes/connection_info.php');
require_once('AvidMobile_billing_functions.php');
require_once('../anet_php_sdk/AuthorizeNet.php');
	
$count = 0;
$accountstocharge = array();
$j = 0;
	
$mysql = new mysqli(DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);

$myquery = $mysql->query("SELECT * FROM `auto_bill`");
	
while ($row = $myquery->fetch_assoc()) {
			
	if (substr($row['chargedate'], -2) > 28) {
				
		$curchargedate = 28;	
	}
		
	else {
				
		$curchargedate = substr($row['chargedate'], -2);
	}
		
	if ($curchargedate == date("d")) {
			
		$accountstocharge[$j]['anet_customer_id'] = $row['anet_customer_id'];
		$accountstocharge[$j]['anet_payment_id'] = $row['anet_payment_id'];
		$accountstocharge[$j]['platform_id'] = $row['platform_id'];
		$j++;
	}
}
	
$i = 0;

if (MC_LEVEL == 1) {
	
	$reseller = array();
		
	while (isset($accountstocharge[$i]['anet_customer_id'])) {
				
		$customer[$i] = new ClientInfo(MC_USERNAME, MC_PASSWORD, MC_CUSTOMERID, MC_LEVEL);
		$customer[$i]->id = $accountstocharge[$i]['platform_id'];
		$customer[$i]->listClientBasicInfo();
		$customer[$i]->listClientQuotaRates(date("m", mktime(0, 0, 0, date("m"), date("d"), date("Y"))), date("Y"));
		$customer[$i]->costmts = $customer[$i]->calculateTotalMTCost(date("m", mktime(0, 0, 0, date("m")-1, date("d"), date("Y"))), date("Y"));
		$customer[$i]->costmos = $customer[$i]->calculateTotalMOCost(date("m", mktime(0, 0, 0, date("m")-1, date("d"), date("Y"))), date("Y"));
		$customer[$i]->costkeywords = $customer[$i]->calculateTotalKeywordCost(date("m"), date("Y"));
			
		// Create Auth & Capture Transaction
		$order = new AuthorizeNetTransaction;
		$transaction = new AuthorizeNetTransaction;
		$transaction->amount = $customer[$i]->base_price + $customer[$i]->costmts + $customer[$i]->costmos + $customer[$i]->costkeywords;
		$transaction->customerProfileId = $accountstocharge[$i]['anet_customer_id'];
		$transaction->customerPaymentProfileId = $accountstocharge[$i]['anet_payment_id'];
		
		$order->description = 'Monthly Transaction';
		$transaction->order = $order;
		$extraOptions = urlencode('customer_ip=' . $_SERVER['REMOTE_ADDR']);
			
		$request = new AuthorizeNetCIM;
		$response = $request->createCustomerProfileTransaction("AuthCapture", $transaction, $extraOptions);
		$transactionResponse = $response->getTransactionResponse();
		$i++;
	}
	
} else if (MC_LEVEL == '2') {
	
	$reseller = array();
		
	while (isset($accountstocharge[$i]['anet_customer_id'])) {
				
		$reseller[$i] = new ResellerInfo(MC_USERNAME, MC_PASSWORD, MC_CUSTOMERID, MC_LEVEL);
		$reseller[$i]->id = $accountstocharge[$i]['platform_id'];
		$reseller[$i]->listResellerBasicInfo();
		$reseller[$i]->listResellerQuotaRates(date("m", mktime(0, 0, 0, date("m"), date("d"), date("Y"))), date("Y"));
		$reseller[$i]->costmts = $reseller[$i]->calculateTotalMTCost(date("m", mktime(0, 0, 0, date("m")-1, date("d"), date("Y"))), date("Y"));
		$reseller[$i]->costmos = $reseller[$i]->calculateTotalMOCost(date("m", mktime(0, 0, 0, date("m")-1, date("d"), date("Y"))), date("Y"));
		$reseller[$i]->costkeywords = $reseller[$i]->calculateTotalKeywordCost(date("m"), date("Y"));
		$reseller[$i]->costclients = $reseller[$i]->calculateTotalClientCost(date("m")-1, date("Y"));
			
		// Create Auth & Capture Transaction
		$order = new AuthorizeNetTransaction;
		$transaction = new AuthorizeNetTransaction;
		$transaction->amount = $reseller[$i]->base_price + $reseller[$i]->costmts + $reseller[$i]->costmos + $reseller[$i]->costkeywords + $reseller[$i]->costclients;
		$transaction->customerProfileId = $accountstocharge[$i]['anet_customer_id'];
		$transaction->customerPaymentProfileId = $accountstocharge[$i]['anet_payment_id'];
		
		$order->description = 'Monthly Transaction';
		$transaction->order = $order;
		$extraOptions = urlencode('customer_ip=' . $_SERVER['REMOTE_ADDR']);
			
		$request = new AuthorizeNetCIM;
		$response = $request->createCustomerProfileTransaction("AuthCapture", $transaction, $extraOptions);
		$transactionResponse = $response->getTransactionResponse();
		$i++;
	}
}

?>