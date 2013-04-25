$(document).ready(function() {
	
	getUsers();

	$("body").on("click", "button", function() {
		
		var con = confirm('Are you sure you want to delete this user? This action will delete the user from the SMS Platform, the CIM, and the recurrent billing databse. This action CANNOT be undone!');
		
		if (con) {
		
			var attrstring = $(this).attr("id");
			
			var id = $(this).val();
			
			if(attrstring.match("delete") != null) {
				
				deleteUser(id);
				
				return true;
			}
		}
	});

function deleteUser(id) {
	
	$("#users").html('<img style="padding-top:50px;" src="/automatic_account_creation/aacc_images/loading.gif" alt="Loading" />');
	
	$.ajax({
		type: "POST",
		url: "/automatic_account_creation/admin/AdministrationControlPanel.php",
		data: { deleteUser: id },
		success: function(data) {
			
			if (data == 'true') {
				
				getUsers();
				
				$("#user" + id).remove();
				
			} else {
				
			$("#errorExplanation").html('<h2>ERROR - FAILED TO DELETE USER<br /><br />User must be manually deleted from one or more of the locations below:<br /><br />1) The recurrent billing database.<br />2)The Authorize.Net CIM<br />3) The SMS Platform</h2>');
			
			$("#table").hide();
				
			}
		}
	});
}

function getUsers() {
	
		$.ajax({
			type: "POST",
			url: "/automatic_account_creation/admin/AdministrationControlPanel.php",
			data: { getUsers: 'true' },
			success: function(data) {
				$("#users").html(data);
			}
		});
}

});