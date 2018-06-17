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
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "relationsListViewContainer", TRUE);

// Get all relation persons
$personRelations = ePerson::getPersons();

// Set relation categories
$listContainer = HTML::select(".listContainer")->item(0);
foreach ($personRelations as $personInfo)
{
	$personID = $personInfo['id'];
	
	// Create person list item
	$listItem = DOM::create("div", "", "", "listItem");
	DOM::append($listContainer, $listItem);
	
	// Set action
	$attr = array();
	$attr['pid'] = $personID;
	$actionFactory->setAction($listItem, "details/personDetails", ".relationsListView .detailsContainer .wbox.details", $attr, $loading = TRUE);
	
	// Ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($listItem, $ico);
	
	// Name
	$personName = $personInfo['firstname']." ".$personInfo['lastname'];
	$name = DOM::create("div", $personName, "", "name");
	DOM::append($listItem, $name);
}

// Return output
return $appContent->getReport();
//#section_end#
?>