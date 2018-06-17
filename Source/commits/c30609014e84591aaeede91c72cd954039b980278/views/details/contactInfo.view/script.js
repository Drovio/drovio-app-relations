jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Edit action
	jq(document).on("contactinfo.edit", function() {
		jq(".contactInfoContainer .contactInfo").addClass("edit");
	});
	
	// Cancel edit action
	jq(document).on("contactinfo.cancel_edit", function() {
		// Remove class
		jq(".contactInfoContainer .contactInfo").removeClass("edit");
		
		// Clear edit form container contents
		jq(".editFormContainer").html("");
	});
});