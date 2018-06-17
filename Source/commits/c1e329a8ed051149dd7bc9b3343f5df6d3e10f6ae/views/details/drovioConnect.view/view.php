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
importer::import("API", "Profile");
importer::import("ENP", "Relations");
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \API\Profile\person;
use \ENP\Relations\ePerson;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

$personID = engine::getVar("pid");
if (engine::isPost())
{
	// Get person id to connect
	$connectPersonID = engine::getVar("dpid");
	
	// Connect to person
	$ePerson = new ePerson($personID);
	$ePerson->connectToPerson($connectPersonID);
	
	// Notification ??
	
	// Return report
	return $appContent->getReport();
}

// Build the application view content
$appContent->build("", "personInfoContainer", TRUE);
$personInfo = HTML::select(".personInfo")->item(0);

// Get person id to show detail for
$ePerson = new ePerson($personID);
$connectPersonID = $ePerson->getDrovioPersonByMail();
$personInfo = person::info($connectPersonID);

// Return output
return $appContent->getReport();


function getInfoRow3($type, $value)
{
	$infoRow = DOM::create("div", "", "", "infoRow");
	HTML::addClass($infoRow, $type);
	
	// Create ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($infoRow, $ico);
	
	$value = DOM::create("div", $value, "", "ivalue");
	DOM::append($infoRow, $value);
	
	return $infoRow;
}
//#section_end#
?>