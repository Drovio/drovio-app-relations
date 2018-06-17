jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Edit action
	jq(document).on("custominfo.edit", function() {
		jq(".customInfoContainer .customInfo").addClass("edit");
	});
	
	// Cancel edit action
	jq(document).on("custominfo.cancel_edit", function() {
		// Remove class
		jq(".customInfoContainer .customInfo").removeClass("edit");
		
		// Clear edit form container contents
		jq(".customInfoContainer .editFormContainer").html("");
	});
});