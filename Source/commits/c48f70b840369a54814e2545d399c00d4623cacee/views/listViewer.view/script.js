jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Listener to switch to contact details
	jq(document).on("listviewer.switchto.details", function() {
		jq(".relationsListViewContainer .relationsListView").addClass("details");
	});
	
	// Switch to list
	jq(document).on("click", ".relationsListViewContainer .detailsContainer .wbutton.back", function() {
		jq(".relationsListViewContainer .relationsListView").removeClass("details");
	});
});