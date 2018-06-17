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
$appContent->build("", "personDetailsContainer", TRUE);

// Get person id to show detail for
$personID = engine::getVar("pid");
$ePerson = new ePerson($personID);
$personInfo = $ePerson->info();

// Set name
$name = HTML::select(".personDetails .sidebar .name")->item(0);
$fullname = $personInfo['firstname']." ".$personInfo['lastname'];
HTML::innerHTML($name, $fullname);

// Contact info section
$detailsContainer = HTML::select(".personDetails .detailsContainer")->item(0);
$section = DOM::create("div", "", "", "section contact_info");
DOM::append($detailsContainer, $section);

// Load view
$attr = array();
$attr['pid'] = $personID;
$viewContainer = $appContent->getAppViewContainer($viewName = "details/contactInfo", $attr, $startup = FALSE, $containerID = "contactInfoViewContainer", $loading = FALSE, $preload = TRUE);
DOM::append($section, $viewContainer);


// Get drovio account info
$publicPersonInfo = $ePerson->getConnectedPersonInfo();
if (!empty($publicPersonInfo))
{
	// Create drovio account section
	$section = DOM::create("div", "", "", "section public_person_info");
	DOM::append($detailsContainer, $section);
	
	// Load view
	$attr = array();
	$attr['pid'] = $personID;
	$viewContainer = $appContent->getAppViewContainer($viewName = "details/drovioPersonInfo", $attr, $startup = FALSE, $containerID = "", $loading = FALSE, $preload = TRUE);
	DOM::append($section, $viewContainer);
}
else
{
	// Get drovio person by mail
	$connectPersonIDs = $ePerson->getDrovioPersonsByMail();
	if (!empty($connectPersonIDs))
	{
		$section = DOM::create("div", "", "", "section connect");
		DOM::append($detailsContainer, $section);

		// Load view
		$attr = array();
		$attr['pid'] = $personID;
		$viewContainer = $appContent->getAppViewContainer($viewName = "details/drovioConnect", $attr, $startup = FALSE, $containerID = "", $loading = FALSE, $preload = TRUE);
		DOM::append($section, $viewContainer);
	}
}

// Delete relation button
$deleteButton = HTML::select(".personDetails .abutton.delete")->item(0);
$attr = array();
$attr['pid'] = $personID;
$actionFactory->setAction($deleteButton, $viewName = "details/deleteRelation", $holder = "", $attr, $loading = TRUE);

// Action to switch to details view
$appContent->addReportAction($name = "listviewer.switchto.details");

// Return output
return $appContent->getReport();
//#section_end#
?>