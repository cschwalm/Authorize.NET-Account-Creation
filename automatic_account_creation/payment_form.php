<?php
/*
 * Filename: payment_form.php
 * 
 * This file may be converted to pure HTML by removing this PHP block and the PHP function call for the logo.
 * If the desire is to place this form inline, just remove both blocks of code.
 */

require_once('includes/connection_info.php');
 
?>
<!DOCTYPE html>

<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>Plan Signup</title>
		
	<link href="aacc_css/reset.css" media="screen, projection" rel="stylesheet" type="text/css" />
	<link href="aacc_css/global.css" media="screen, projection" rel="stylesheet" type="text/css" />
	<link href="aacc_css/blueprint/print.css" media="print" rel="stylesheet" type="text/css" />
  	<!--[if lt IE 8]><link href="aacc_css/blueprint/ie.css?1339104269" media="screen, projection" rel="stylesheet" type="text/css" /><![endif]-->
  	<link href="aacc_css/signup.css" media="screen" rel="stylesheet" type="text/css" />

  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
    <script src="aacc_js/signup.js" type="text/javascript"></script>

</head>

<body>
		
<div id="app_content_wrapper">
	
<div style="text-align:center; padding:10px;" class="app_header">
	<img src="<?php echo LOGO_PATH; ?>" alt="<?php echo COMPANY_NAME; ?>" id="logo" />
</div>

<div id="app_content" class="clearfix">
	
<div class="errorExplanation" id="errorExplanation">
	
</div>
		
<form class="formtastic seller" id="aCForm" name="aCForm">
	
<div class="left leftcol">
  
  <div class="login_form_container">
    <h3>Customer Information</h3>
    <ul>
     	<li>
	        <label for="companyName">Company Name *</label>
	        <input id="companyName" name="aCForm[companyName]" size="30" type="text" />
      	</li>
        <li class="fieldsmall fieldleft">
          <label for="firstname">First Name *</label>
          <input class="inputsmall" id="firstName" name="aCForm[firstName]" size="30" type="text" />
        </li>
        <li class="fieldsmall right">
          <label for="lastName">Last Name *</label>
          <input class="inputsmall" id="lastName" name="aCForm[lastName]" size="30" type="text" />
        </li>
        <li>
          <label for="email">Email *</label>
          <input id="email" name="aCForm[email]" size="30" type="text" />
        </li>
        <li class="fieldsmall fieldleft">
        <label for="phone">Mobile *</label>
        <input class="inputsmall" id="phone" name="aCForm[phone]" size="30" type="text" />
      	</li>
    </ul>
  </div>
  
  <div class="login_form_container">
    <h3 style="float:left;">Plan</h3>
    <select class="right" id="planID" name="aCForm[planID]"></select>
    <p class="right" style="padding-top:25px; font-weight:bold;" id="setupFee"></p>
  </div>
</div>

<div class="right" id="signupccinfo">
  <div class="login_form_container right">
	<h3>Credit Card Information</h3>
	  
        <ul>
          <li class="fieldsmall fieldleft">
            <label for="billingFirstName">First Name *</label>
            <input class="inputsmall" id="billingFirstName" name="aCForm[billingFirstName]" size="30" type="text" />
          </li>
          <li class="fieldsmall right">
            <label for="billingLastName">Last Name *</label>
            <input class="inputsmall" id="billingLastName" name="aCForm[billingLastName]" size="30" type="text" />
          </li>
          <li>
            <label for="creditCard">Credit Card Number *</label>
            <input id="creditCard" name="aCForm[creditCard]" size="30" type="text" />
          </li>
          <li class="fieldsmall left expdate">
            <label for="expirationDateMonth">Expires On *</label>
            <select id="expiratioDateMonth" name="aCForm[expirationDateMonth]">
				<option value="" selected=""></option>
				<option value="01">1 - Jan</option>
				<option value="02">2 - Feb</option>
				<option value="03">3 - Mar</option>
				<option value="04">4 - Apr</option>
				<option value="05">5 - May</option>
				<option value="06">6 - Jun</option>
				<option value="07">7 - Jul</option>
				<option value="08">8 - Aug</option>
				<option value="09">9 - Sep</option>
				<option value="10">10 - Oct</option>
				<option value="11">11 - Nov</option>
				<option value="12">12 - Dec</option>
			</select>

            <select id="expiratioDateYear" name="aCForm[expirationDateYear]">
				<option value=""></option>
				<option value="2012">2012</option>
				<option value="2013">2013</option>
				<option value="2014">2014</option>
				<option value="2015">2015</option>
				<option value="2016">2016</option>
				<option value="2017">2017</option>
				<option value="2018">2018</option>
				<option value="2019">2019</option>
				<option value="2020">2020</option>
				<option value="2021">2021</option>
				<option value="2022">2022</option>
		  </select>

          </li>
          <li class="fieldsmall right ccvinput">
            <label for="cvv">Security Code (CCV) *</label>
            <input class="inputsmall" id="cvv" name="aCForm[cvv]" size="30" type="text" />
          </li>
          <li>
            <label class="left" for="billingStreetAddress1">Street Address *</label>
              <a href="#" class="right" id="addaddress">Add Address Line</a>
            <input id="billingStreetAddress1" name="aCForm[billingStreetAddress1]" size="30" type="text" />
          </li>
          <li id="address2">
            <label for="billingStreetAddress2">Street Address 2</label>
            <input id="billingStreetAddress2" name="aCForm[billingStreetAddress2]" size="30" type="text" />
          </li>
          <li>
            <label for="billingCity">City *</label>
            <input id="billingCity" name="aCForm[billingCity]" size="30" type="text" />
          </li>
          <li class="fieldsmall left">
            <label for="billingState">Billing State / Province *</label>
            <select class="billstate" id="billingState" name="aCForm[billingState]"><option value="">Please select</option>
				<option value="AK">AK</option>
				<option value="AL">AL</option>
				<option value="AR">AR</option>
				<option value="AZ">AZ</option>
				<option value="CA">CA</option>
				<option value="CO">CO</option>
				<option value="CT">CT</option>
				<option value="DC">DC</option>
				<option value="DE">DE</option>
				<option value="FL">FL</option>
				<option value="GA">GA</option>
				<option value="HI">HI</option>
				<option value="IA">IA</option>
				<option value="ID">ID</option>
				<option value="IL">IL</option>
				<option value="IN">IN</option>
				<option value="KS">KS</option>
				<option value="KY">KY</option>
				<option value="LA">LA</option>
				<option value="MA">MA</option>
				<option value="MD">MD</option>
				<option value="ME">ME</option>
				<option value="MI">MI</option>
				<option value="MN">MN</option>
				<option value="MO">MO</option>
				<option value="MS">MS</option>
				<option value="MT">MT</option>
				<option value="NC">NC</option>
				<option value="ND">ND</option>
				<option value="NE">NE</option>
				<option value="NH">NH</option>
				<option value="NJ">NJ</option>
				<option value="NM">NM</option>
				<option value="NV">NV</option>
				<option value="NY">NY</option>
				<option value="OH">OH</option>
				<option value="OK">OK</option>
				<option value="OR">OR</option>
				<option value="PA">PA</option>
				<option value="RI">RI</option>
				<option value="SC">SC</option>
				<option value="SD">SD</option>
				<option value="TN">TN</option>
				<option value="TX">TX</option>
				<option value="UT">UT</option>
				<option value="VA">VA</option>
				<option value="VT">VT</option>
				<option value="WA">WA</option>
				<option value="WI">WI</option>
				<option value="WV">WV</option>
				<option value="WY">WY</option>
		  </select>
          </li>
          <li  class="fieldsmall right billzip">
            <label for="zip">Zip Code / Postal Code*</label>
            <input class="inputsmall" id="zip" name="aCForm[billingZip]" size="30" maxlength="5" type="text" />
          </li>
          <li>
            <input name="terms1" type="hidden" value="0" /><input class="terms_checkbox" id="accept_terms" name="aCForm[terms]" type="checkbox" value="1" />
            <label class="checkbox" style="float:left;">I accept the <a href="" target="blank">Terms and Conditions</a></label>
            <br style="clear:both" />
          </li>
        </ul>
        
        <div id="seller_submit_id">
        	<input class="app-submit-button" id="seller_submit" name="submit" type="button" value="Pay &amp; Create My Account &rsaquo;" />
        </div>
        
  </div>
  </div>


</form>
	  
	</div>
</div>
</body>
</html>