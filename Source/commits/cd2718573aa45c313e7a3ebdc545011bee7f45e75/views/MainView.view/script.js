var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Get filter listeners
	jq(document).on("click", ".drovioRelationsApplication .pnavigation .navitem.filter", function() {
		// filter all relation contacts
		var filter = jq(this).data("filter");
		if (filter == "all")
			jq(".relationsListViewContainer .listContainer .listItem").show();
		else {
			// Hide all items and show only those that have the filter
			jq(".relationsListViewContainer .listContainer .listItem").hide().each(function() {
				if (jq(this).data("types")[filter] == 1)
					jq(this).show();
			});
		}
		
		// Remove details
		jq(".relationsListViewContainer .relationsListView").removeClass("details");
	});
	
	// Load all relations on click
	jq(document).on("click", ".drovioRelationsApplication .pnavigation .navitem.filter.all", function() {
		jq("#avlistViewContainer").trigger("reload");
	});
	
	// Reload list
	jq(document).on("relations.list.reload", function() {
		jq(".drovioRelationsApplication .pnavigation .navitem.filter.all").trigger("click");
	});
});