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
importer::import("ENP", "Relations");
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \ENP\Relations\ePerson;
use \ENP\Relations\ePersonMail;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "relationCardContainer", TRUE);

// Get person id to show detail for
$personID = engine::getVar("pid");
$ePerson = new ePerson($personID);
$personInfo = $ePerson->info();

// Set name
$name = HTML::select(".relationCard .sidebar .name")->item(0);
if (!empty($personInfo['middle_name']))
	$fullname = $personInfo['firstname']." ".$personInfo['middle_name']." ".$personInfo['lastname'];
else
	$fullname = $personInfo['firstname']." ".$personInfo['lastname'];
HTML::innerHTML($name, $fullname);

// Contact info section
$detailsContainer = HTML::select(".relationCard .detailsContainer")->item(0);
$section = DOM::create("div", "", "", "section contact_info");
DOM::append($detailsContainer, $section);

// Load view
$attr = array();
$attr['pid'] = $personID;
$viewContainer = $appContent->getAppViewContainer($viewName = "relations/contactInfo", $attr, $startup = FALSE, $containerID = "contactInfoViewContainer", $loading = FALSE, $preload = TRUE);
DOM::append($section, $viewContainer);

$section = DOM::create("div", "", "", "section custom_info");
DOM::append($detailsContainer, $section);

// Load view
$attr = array();
$attr['pid'] = $personID;
$viewContainer = $appContent->getAppViewContainer($viewName = "relations/customInfo", $attr, $startup = FALSE, $containerID = "customInfoViewContainer", $loading = FALSE, $preload = TRUE);
DOM::append($section, $viewContainer);


// Get identity account info
if (!empty($personInfo['identity_account_id']))
{
	// Create drovio account section
	$section = DOM::create("div", "", "", "section user_info");
	DOM::append($detailsContainer, $section);
	
	// Load view
	$attr = array();
	$attr['aid'] = $personInfo['identity_account_id'];
	$viewContainer = $appContent->getAppViewContainer($viewName = "users/userInfo", $attr, $startup = TRUE, $containerID = "userInfoViewContainer", $loading = FALSE, $preload = FALSE);
	DOM::append($section, $viewContainer);
}

// Delete relation button
$deleteButton = HTML::select(".relationCard .abutton.delete")->item(0);
$attr = array();
$attr['pid'] = $personID;
$actionFactory->setAction($deleteButton, $viewName = "relations/deleteRelation", $holder = "", $attr, $loading = TRUE);

// Action to switch to details view
$appContent->addReportAction($name = "listviewer.switchto.details");

// Return output
return $appContent->getReport();
//#section_end#
?>