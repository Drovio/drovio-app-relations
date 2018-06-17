<?php
//#section#[header]
// Use Important Headers
use \API\Platform\importer;
use \API\Platform\engine;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import DOM, HTML
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

use \UI\Html\DOM;
use \UI\Html\HTML;

// Import application for initialization
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;

// Increase application's view loading depth
application::incLoadingDepth();

// Set Application ID
$appID = 62;

// Init Application and Application literal
application::init(62);
// Secure Importer
importer::secure(TRUE);

// Import SDK Packages
importer::import("AEL", "Literals");
importer::import("ENP", "Relations");
importer::import("UI", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \ENP\Relations\ePerson;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

$personID = engine::getVar('pid');
$ePerson = new ePerson($personID);
if (engine::isPost())
{
	// Create form error Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Delete person relation
	$status = $ePerson->remove();

	// If there is an error in creating the folder, show it
	if ($status !== TRUE)
	{
		$err_header = appLiteral::get("relations.details", "hd_deleteRelation");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error deleting relation."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE);
	
	// Add action to reload list
	$succFormNtf->addReportAction($type = "relations.list.reload", $value = "");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Build the frame
$frame = new dialogFrame();
$title = appLiteral::get("relations.details", "hd_deleteRelation");
$frame->build($title, "", FALSE)->engageApp("relations/deleteRelation");
$form = $frame->getFormFactory();

// Header
$personInfo = $ePerson->info();
$attr = array();
$attr['rname'] = $personInfo['firstname']." ".$personInfo['lastname'];
$title = appLiteral::get("relations.details", "lbl_sureDeleteRelation", $attr);
$hd = DOM::create("h3", $title);
$frame->append($hd);

// Person id
$input = $form->getInput($type = "hidden", $name = "pid", $value = $personID, $class = "", $autofocus = FALSE, $required = FALSE);
$frame->append($input);

// Return the report
return $frame->getFrame();
//#section_end#
?>