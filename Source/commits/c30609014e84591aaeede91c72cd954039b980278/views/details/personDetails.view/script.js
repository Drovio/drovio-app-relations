jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload contact info
	jq(document).on("contactinfo.reload", function() {
		jq("#contactInfoViewContainer").trigger("reload");
	});
});