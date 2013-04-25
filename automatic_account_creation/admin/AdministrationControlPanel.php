<?php
 
require_once('includes/admin_functions.php');
require_once('../includes/pricing.php');
require_once('../includes/connection_info.php');
require_once('../anet_php_sdk/AuthorizeNet.php');

/**
 * Provides an Administration Control Panel that can delete the created customer from all locations.
 * This is the prefered method to delete cumstomers since their account information is in multiple locations.
 * 
 * @author Corbin Schwalm | AvidMobile
 */
class AdministrationControlPanel {
	
	/** Storage for the customer's SMS Platform ID. */
	private $smsPlatformCustomerId;
	
	/** Storage for the customer's CIM profile ID. */
	private $anetCustomerProfileId;
	
	/** Storage for the MYSQLi object. */
	private $dB;
	
	/**
	 * Constructs a default Administration Control Panel.
	 * 
	 * @param $smsPlatformCustomerId
	 * @param $anetCustomerProfileId
	 */
	public function __construct($smsPlatformCustomerId, $anetCustomerProfileId) {
		
		$this->smsPlatformCustomerId = $smsPlatformCustomerId;
		
		$this->anetCustomerProfileId = $anetCustomerProfileId;
		
		$this->dB = new mysqli(DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
		
	} //end __construct

	/**
	 * Deletes the customer from the SMS Platform.
	 * 
	 * @return Returns true if successful; false otherwise.
	 */
	public function deleteResellerSMSPlatform() {
		
		$result = "";
		
		if (MC_LEVEL == '1') {
			
			$type = new Reseller(NULL, NULL);
			
		} else if (MC_LEVEL == 2) {
			
			$type = new Organization(NULL, NULL);
		}
		
		try {
			
			if (MC_LEVEL == '1') {
				
				$result = $type->deleteMC($this->smsPlatformCustomerId);
				
			} else if (MC_LEVEL == '2') {
				
				$result = $type->deleteReseller($this->smsPlatformCustomerId);
			}
				
		} catch (Exception $ex) {
			
			return false;
		}
		
		if ($result->ErrorCode != '0')
			return false;
		
		return true;
		
	} //end deleteResellerSMSPlatform
	
	/**
	 * Deletes the customer from Authorize.Net.
	 * 
	 * @return Returns true if successful; false otherwise.
	 */
	public function deleteCustomerAnet() {
		
		$request = new AuthorizeNetCIM;

		$result = $request->deleteCustomerProfile($this->anetCustomerProfileId);
		
		if ($result->xml->messages->resultCode != "Ok")
			return false;
		
		return true;
		
	} //end deleteCustomerAnet
	
	/**
	 * Deletes the customer for the recurrent billing database.
	 * 
	 * @return Returns true if successful; false otherwise.
	 */
	public function deleteCustomerRecurrentBillingDb() {
		
		$sql = "DELETE FROM `auto_bill` WHERE anet_customer_id = '".$this->anetCustomerProfileId."'";
		
		$this->dB->query($sql);
		
		if ($this->dB->affected_rows <= 0)
			return false;
		
		return true;
		
	} //end deleteCustomerRecurrentBillingDb
	
	/**
	 * Returns a list of the recurrent billing database. Result string is in HTML table.
	 * 
	 * @return String List of the resellers in the recurrent billing database. HTML Table.
	 */
	public static function listUsers() {
		
		$html = "<table id=\"user_table\">\n";
		
		$html .= "<tr>\n\t<th>Customers</th><th></th>\n</tr>";
		
		$sql = "SELECT anet_customer_id, org  FROM `auto_bill`";
		
		$dB = new mysqli(DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
		
		$result = $dB->query($sql);
		
		if ($dB->affected_rows <= 0) {
			
			$html =  '<br /><h3 style="text-align:center;">There are no users.</h3>';
		
		} else {
		
			while($rows = $result->fetch_assoc()) {
				
				$html .= "<tr>\n";
				
				$html .= '<td>'.$rows['org'].'</td><td><button type="button" id="delete'.$rows['anet_customer_id'].'"value="'.$rows['anet_customer_id'].'">Delete User</button>';
				
				$html .= "</tr>\n";
			}
			
			$html .= "</table>\n";	
		}
		
		return $html;
		
	} //end listUsers
	
	/** Looks up the SMS Platform ID from the recurrent billing database.
	 * 
	 * @param String $anetCustomerProfileId
	 * @return String The SMS Platform Customer ID
	 */
	public static function getSmsPlatformId() {
		
		$sql = "SELECT platform_id FROM `auto_bill`";
		
		$dB = new mysqli(DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
		
		$result = $dB->query($sql);
		
		$row = $result->fetch_assoc();
		
		return $row['platform_id'];
		
	} //end getSmsPlatformId
	
	/**
	 * Manually adds a user to the recurrent billing system.
	 * 
	 * @param string $anetCustomerID The authorize.net CIM customer ID.
	 * @param string $anetPaymentID The authorize.net payment profile ID.
	 * @param string $smsPlatformID The AvidMobile SMS Platform ID.
	 * @param string $billDate The date to bill the customer.
	 * @param string $companyName The name of the company to add.
	 * @param string $country The country code of the customer.
	 * @return boolean True on success; false otherwise.
	 */
	public static function manualAdd($anetCustomerID, $anetPaymentID, $smsPlatformID, $billDate, $companyName, $country) {
		
		$dB = new mysqli(DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
		
		$anetCustomerID = $dB->real_escape_string($anetCustomerID);
		
		$anetPaymentID = $dB->real_escape_string($anetPaymentID);
		
		$smsPlatformID = $dB->real_escape_string($smsPlatformID);
		
		$billDate = date("Y-m-d", mktime(0, 0, 0, date("m"), $billDate, date("Y")));
		
		$companyName = $dB->real_escape_string($companyName);
		
		$country = $dB->real_escape_string($country);
		
		$sql = "INSERT INTO `auto_bill` VALUES ('".$anetCustomerID."', '".$anetPaymentID."', '".$smsPlatformID."', 
			'".$billDate."', '".$companyName."', '".$country."')";
			
		$result = $dB->query($sql);
		
		if ($dB->affected_rows <= 0)
			return false;
		
		return true;
		
	} //end manualAdd
	
} //end AdministrationControlPanel

$success = true;

if (!empty($_REQUEST['deleteUser'])) {
	
	$aCP = new AdministrationControlPanel(AdministrationControlPanel::getSmsPlatformId($_REQUEST['deleteUser']), $_REQUEST['deleteUser']);

	$result1 = $aCP->deleteResellerSMSPlatform();
	
	$result2 = $aCP->deleteCustomerAnet();
	
	$result3 = $aCP->deleteCustomerRecurrentBillingDb();
	
	if ($result1 && $result2 && $result3) {
			
		echo 'true';
	
	} else {
						
		echo 'false';
	}		
	
	exit;
}

if (!empty($_POST['getUsers'])) {
		
	echo AdministrationControlPanel::listUsers();
	
	exit;
}

if (!empty($_POST['companyName'])) {
	
	if (AdministrationControlPanel::manualAdd($_POST['anetCustomerID'], $_POST['anetPaymentID'], $_POST['smsPlatformID'], $_POST['billDate'], 
		$_POST['companyName'], $_POST['country']) === false) {
			
		$success = false;
		
		}

} else {
		
	$success = false;
}

?>
<!DOCTYPE html>

<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>Administration Control Panel</title>
		
	<link href="css/reset.css" media="screen, projection" rel="stylesheet" type="text/css" />
	<link href="css/global.css" media="screen, projection" rel="stylesheet" type="text/css" />
	<link href="css/blueprint/print.css" media="print" rel="stylesheet" type="text/css" />
  	<!--[if lt IE 8]><link href="css/blueprint/ie.css?1339104269" media="screen, projection" rel="stylesheet" type="text/css" /><![endif]-->
  	<link href="css/admin.css" media="screen" rel="stylesheet" type="text/css" />

  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
    <script src="js/admin.js" type="text/javascript"></script>

</head>

<body>
		
<div id="app_content_wrapper">
	
<div style="text-align:center; padding:10px;" class="app_header">
      <img src="../<?php echo LOGO_PATH; ?>" alt="<?php echo COMPANY_NAME; ?>" id="logo" />
</div>

<div id="app_content" class="clearfix">
	
<div class="errorExplanation" id="errorExplanation"></div>

<div class="login_form_container" id="table">
<h2>Administration Control Panel</h2>

<div id="users"></div>

</div>

<div class="login_form_container" id="manualAdd">
	
	<h2 style="text-align: center;">Manual Add</h2>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="manualAdd" method="post">
	<label>Enter the Company Name:</label><input name="companyName" type="text" value="<?php if(!$success && !empty($companyName)) { echo $_POST['companyName']; } ?>" /><br /><br />
	<label>Enter Authorize.Net Customer ID:</label> <input name="anetCustomerID" type="text" value="<?php if(!$success && !empty($companyName)) { echo $_POST['anetCustomerID']; } ?>" /><br /><br />
	<label>Enter Authorize.Net Payment Profile ID:</label> <input name="anetPaymentID" type="text" value="<? if(!$success && !empty($companyName)) { echo $_POST['anetPaymentID']; } ?>" /><br /><br />
	<label>Enter AvidMobile Platform ID:</label> <input name="smsPlatformID" type="text" value="<?php if(!$success && !empty($companyName)) { echo $_POST['smsPlatformID']; } ?>" /><br /><br />
	<label>Select the day of the month they will be billed:</label> 
	<select name="billDate">
		<option value="01" <?php if(!$success) { if(isset($_POST['billDate']) == "01") { echo "selected"; } } ?>>01</option>
		<option value="02" <?php if(!$success) { if(isset($_POST['billDate']) == "02") { echo "selected"; } } ?>>02</option>
		<option value="03" <?php if(!$success) { if(isset($_POST['billDate']) == "03") { echo "selected"; } } ?>>03</option>
		<option value="04" <?php if(!$success) { if(isset($_POST['billDate']) == "04") { echo "selected"; } } ?>>04</option>
		<option value="05" <?php if(!$success) { if(isset($_POST['billDate']) == "05") { echo "selected"; } } ?>>05</option>
		<option value="06" <?php if(!$success) { if(isset($_POST['billDate']) == "06") { echo "selected"; } } ?>>06</option>
		<option value="07" <?php if(!$success) { if(isset($_POST['billDate']) == "07") { echo "selected"; } } ?>>07</option>
		<option value="08" <?php if(!$success) { if(isset($_POST['billDate']) == "08") { echo "selected"; } } ?>>08</option>
		<option value="09" <?php if(!$success) { if(isset($_POST['billDate']) == "09") { echo "selected"; } } ?>>09</option>
		<option value="10" <?php if(!$success) { if(isset($_POST['billDate']) == "10") { echo "selected"; } } ?>>10</option>
		<option value="11" <?php if(!$success) { if(isset($_POST['billDate']) == "11") { echo "selected"; } } ?>>11</option>
		<option value="12" <?php if(!$success) { if(isset($_POST['billDate']) == "12") { echo "selected"; } } ?>>12</option>
		<option value="13" <?php if(!$success) { if(isset($_POST['billDate']) == "13") { echo "selected"; } } ?>>13</option>
		<option value="14" <?php if(!$success) { if(isset($_POST['billDate']) == "14") { echo "selected"; } } ?>>14</option>
		<option value="15" <?php if(!$success) { if(isset($_POST['billDate']) == "15") { echo "selected"; } } ?>>15</option>
		<option value="16" <?php if(!$success) { if(isset($_POST['billDate']) == "16") { echo "selected"; } } ?>>16</option>
		<option value="17" <?php if(!$success) { if(isset($_POST['billDate']) == "17") { echo "selected"; } } ?>>17</option>
		<option value="18" <?php if(!$success) { if(isset($_POST['billDate']) == "18") { echo "selected"; } } ?>>18</option>
		<option value="19" <?php if(!$success) { if(isset($_POST['billDate']) == "19") { echo "selected"; } } ?>>19</option>
		<option value="20" <?php if(!$success) { if(isset($_POST['billDate']) == "20") { echo "selected"; } } ?>>20</option>
		<option value="21" <?php if(!$success) { if(isset($_POST['billDate']) == "21") { echo "selected"; } } ?>>21</option>
		<option value="22" <?php if(!$success) { if(isset($_POST['billDate']) == "22") { echo "selected"; } } ?>>22</option>
		<option value="23" <?php if(!$success) { if(isset($_POST['billDate']) == "23") { echo "selected"; } } ?>>23</option>
		<option value="24" <?php if(!$success) { if(isset($_POST['billDate']) == "24") { echo "selected"; } } ?>>24</option>
		<option value="25" <?php if(!$success) { if(isset($_POST['billDate']) == "25") { echo "selected"; } } ?>>25</option>
		<option value="26" <?php if(!$success) { if(isset($_POST['billDate']) == "26") { echo "selected"; } } ?>>26</option>
		<option value="27" <?php if(!$success) { if(isset($_POST['billDate']) == "27") { echo "selected"; } } ?>>27</option>
		<option value="28" <?php if(!$success) { if(isset($_POST['billDate']) == "28") { echo "selected"; } } ?>>28</option>
	</select><br /><br />
	<label>Country:</label> <select name="country">
		<option value="US" <?php if(!$success) { if(isset($_POST['country']) && isset($_POST['country']) == "US") { echo "selected"; } } ?>>United States</option>
		<option value="CA" <?php if(!$success) { if(isset($_POST['country']) && isset($_POST['country']) == "CA") { echo "selected"; } } ?>>Canada</option>
	</select><br /><br />
	<input type="submit" name="submit" class="right" value="Add User" />
</form>

<?php if($success && !empty($_POST['companyName'])) { echo "<font style='color:#00FF00;'>Client Successfully Added!</font>"; } elseif(!$success && !empty($_POST['companyName'])) { echo "<font style='color:#FF0000;'>An error occurred and the client was not added.</font>"; } ?>
	
</div>

</div>
</div>
</body>
</html>