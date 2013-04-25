$(document).ready(function() {
	getPlanData();
	
	$("#planID").change(function() {
		setSetupFee();
	});

	$("body").on("click", "input", function() {
		
		switch ($(this).attr('id')) {
			
			case "seller_submit" :
				
				submitForm();
				
			break;
			
		}
	});
	
	$("#addaddress").click(function() {
		signupAddressLine();
	});

function signupAddressLine(event) {
		
		var myadd = $("#address2").is(":visible");
		
		if (myadd) {
			
				$("#addaddress").text("Add Address Line");
				$("#address2").hide();
			
			} else {
				
				$("#addaddress").text("Remove Address Line");
				$("#address2").show();
			}
		
		event.preventDefault();
}

function submitForm() {
	
	$("#seller_submit_id").html('<img style="padding-top:50px;" src="/automatic_account_creation/aacc_images/loading.gif" alt="Loading" />');
	
	$.ajax({
		type: "POST",
		url: "/automatic_account_creation/AccountCreation.php",
		data: $("#aCForm").serialize(),
		success: function(data) {
			$("#seller_submit_id").html('<input class="app-submit-button" id="seller_submit" name="submit" type="button" value="Pay &amp; Create My Account &rsaquo;" />');
			$("#errorExplanation").html(data);
		}
	});
}

function setSetupFee() {
	$.ajax({
		type: "GET",
		url: "/automatic_account_creation/includes/pricing.php",
		data: { planID: $("#planID").val() },
		success: function(data) {
			$("#setupFee").html(data);
		}
	});
}

function getPlanData() {
	
	$.ajax({
		type: "GET",
		url: "/automatic_account_creation/includes/pricing.php",
		data: { requestPlanInfo : "1" },
		success: function(data) {
			$("#planID").html(data);
		}
	});
}

});