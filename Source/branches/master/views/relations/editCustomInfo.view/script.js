jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Edit action
	jq(document).on("click", ".editCustomInfo .close_ico", function() {
		// Trigger to cancel edit
		jq(document).trigger("custominfo.cancel_edit");
	});
	
	// Add new row action
	jq(document).on("click", ".editCustomInfo .editGroup .ico.create_new", function() {
		// Get first new form row and clone
		var frow = jq(this).closest(".editGroup").find(".frow.new").first().clone(true);
		frow.find("input").val("");
		jq(this).closest(".editGroup").find(".frow.new").last().after(frow);
	});
	
	// Remove row
	jq(document).on("click", ".editCustomInfo .frow .ico.remove", function() {
		// Get row and remove
		jq(this).closest(".frow").remove();
	});
});