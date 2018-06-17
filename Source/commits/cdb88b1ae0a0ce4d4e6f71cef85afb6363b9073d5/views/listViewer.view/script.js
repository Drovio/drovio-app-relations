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
	
	
	// Search for contacts
	jq(document).on("keyup", ".relationsListViewContainer .listContainer .searchContainer .searchInput", function() {
		var search = jq(this).val();
		if (search == "")
			return jq(".relationsListViewContainer .listContainer .listItem").show();
			
		// Create the regular expression
		var regEx = new RegExp(jq.map(search.trim().split(' '), function(v) {
			return '(?=.*?' + v + ')';
		}).join(''), 'i');
		
		// Select all project boxes, hide and filter by the regex then show
		jq(".relationsListViewContainer .listContainer .listItem").hide().find(".name").filter(function() {
			return regEx.exec(jq(this).text());
		}).each(function() {
			jq(this).closest(".listItem").show();
		});
	});
});