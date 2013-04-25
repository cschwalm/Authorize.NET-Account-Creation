<?php

/**
 * Provides fault tolerence fo major account creation errors.
 * @author Corbin Schwalm | AvidMobile
 */
class AccountCreationException extends Exception {
	
	private $data;
	
	/**
	 * Default Constructor
	 * @param string $message
	 * @param int $progress
	 * @param array $additionalInfo
	 */
	public function __construct($message = '', $progress = -1, $additionalInfo = null) {
		
		$this->data = $additionalInfo;
		
		parent::__construct($message, $progress);
		
	} //end constructor
	
	/**
	 * Displays the error to the user and ends the script
	 * boolean $fatal Kill script if it is a fatal error & contact the admin.
	 */
	public function handleError($fatal) {
		
		if ($fatal) {
			
			$this->alertAdmin();
			
			echo $this->generateFatalHTML();
			
			exit; //End The Application.
		
		} else {
			
			echo $this->generateHTML();
			
			exit; //End The Script
			
		}
	} //end handleError
	
	public function generateHTML() {
		
		$error = '';
		
		if (!empty($this->data)) {
		
			for ($i = 0; $i < sizeof($this->data); $i++) {
				
				$error .= '<li>'.$this->data[$i].'</li>';
			}
		
		} else {
			
			$error = $this->getMessage();
		}
		
		$html = <<< EOF
					
      <h2>Error</h2>
      <ul>
      	{$error}
      </ul>

EOF;
		
	return $html;
		
	} //end generateHTML
	
	/**
	 * Generates the HTML to display to the user for fatal error.
	 */
	public function generateFatalHTML() {
		
		$html = <<< EOF

<script type="text/javascript">$("#aCForm").hide();</script>

<span style="text-align:center; line-height:1.5em;">

<h2>A fatal error has occured!</h2>
		
<p>Error Message: {$this->message}

An e-mail has been sent the website administrator alerting him of a problem. Your credit card has already been billed and you will
be contacted shortly to resolve the problem.</p>

</span>
		
EOF;

	return $html;
		
	} //end generateFatalHTML
	
	/**
	 * Sends an email to the admin alerting him of the error
	 */
	private function alertAdmin() {
		
		$emailTo = ADMIN_EMAIL_ADDRESS;
		
		$emailFrom = 'website@' . $_SERVER['HTTP_HOST'];
		
		$date = date("Y-m-d H:i:s");
		
		$additionalInfo = print_r($this->data, true);
			
		$emailSubject = 'ERROR: FAILED TO PROCESS CUSTOMER PAYMENT!';
			
		$emailHeaders = "MIME-Version: 1.0\r\n";
		$emailHeaders .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
		$emailHeaders .= 'From: ' . $emailFrom . "\r\n";	
			
		$emailBody = <<< EOF
		
THERE WAS AN ERROR WHILE ATTEMPTING TO PROCESS A CUSTOMERS PAYMENT!\n\n

THE CUSTOMER HAS PAID BUT WAS NOT ABLE TO RECEIVE LOGIN INFO!\n

THE CUSTOMER HAS BEEN NOTIFIED THAT THEY WILL BE CONTACTED SHORTLY!\n
		
Date/Time: {$date}\n
		
Location: {$this->code}\n
		
Error Message: {$this->message}\n
		
Additional Info: {$additionalInfo}\n
		
EOF;
		@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);
		
	} //end alertAdmin
} //end AccountCreationException

?>