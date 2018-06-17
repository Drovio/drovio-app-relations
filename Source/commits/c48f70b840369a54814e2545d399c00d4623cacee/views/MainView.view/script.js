var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Get filter listeners
	jq(document).on("click", ".drovioRelationsApplication .pnavigation .navitem.filter", function() {
		// filter all relation contacts
		var filter = jq(this).data("filter");
		console.log(filter);
		
	});
});