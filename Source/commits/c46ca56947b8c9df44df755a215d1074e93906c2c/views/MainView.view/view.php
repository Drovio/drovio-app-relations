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
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "drovioRelationsApplicationContainer", TRUE);

// Set static navigation to sidebar items
$sections = array();
$sections[] = "all";
$sections[] = "contacts";
$sections[] = "users";
foreach ($sections as $sClass)
{
	// Get mitem
	$navitem = HTML::select(".sidebar .navitem.".$sClass)->item(0);
	
	// Set static nav
	$appContent->setStaticNav($navitem, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "relGroup", $display = "none");
}

// Load all relations by default
$listViewContainer = HTML::select(".drovioRelationsApplication .sectionbody")->item(0);
$listView = $appContent->getAppViewContainer($viewName = "listViewer", $attr = array(), $startup = TRUE, $containerID = "avlistViewContainer", $loading = TRUE, $preload = TRUE);
DOM::append($listViewContainer, $listView);


// Set rest of the actions

// Export contacts
$navitem = HTML::select(".sidebar .navitem.export")->item(0);
$actionFactory->setAction($navitem, $viewName = "export/exportDialog", $holder = "", $attr = array(), $loading = TRUE);

// Application settings
$navitem = HTML::select(".sidebar .navitem.settings")->item(0);
$actionFactory->setAction($navitem, $viewName = "settings/appSettings", $holder = ".relationsListView .detailsContainer .wbox.details", $attr = array(), $loading = TRUE);

// Create new contact action
$navitem = HTML::select(".sidebar .navitem.create")->item(0);
$actionFactory->setAction($navitem, $viewName = "createNewContact", $holder = "", $attr = array(), $loading = TRUE);

// Return output
return $appContent->getReport();
//#section_end#
?>