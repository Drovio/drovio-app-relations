var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Get filter listeners
	jq(document).on("click", ".drovioRelationsApplication .pnavigation .navitem.filter", function() {
		// filter all relation contacts
		var filter = jq(this).data("filter");
		if (filter == "all")
			jq(".relationsListViewContainer .listContainer .clist").show();
		else {
			jq(".relationsListViewContainer .listContainer .clist").hide();
			jq(".relationsListViewContainer .listContainer .clist."+filter).show();
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
		// Reload relations
		jq("#avlistViewContainer").trigger("reload");
		
		// Go back to list
		jq(".drovioRelationsApplication .pnavigation .navitem.filter.selected").trigger("click");
	});
});