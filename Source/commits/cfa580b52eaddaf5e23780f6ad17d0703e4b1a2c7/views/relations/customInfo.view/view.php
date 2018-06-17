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
use \ENP\Relations\ePersonInfo;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "customInfoContainer", TRUE);
$viewContainer = HTML::select(".customInfo .viewContainer")->item(0);

// Get person id to show info
$personID = engine::getVar("pid");
$ePersonInfo = new ePersonInfo($personID);
$personCustomInfo = $ePersonInfo->get();
foreach ($personCustomInfo as $name => $value)
{
	$infoRow = getCustomInfoRow("info", $name, $value);
	DOM::append($viewContainer, $infoRow);
}

// Add action to edit button
$editButton = HTML::select(".customInfo .edit")->item(0);
$attr = array();
$attr['pid'] = $personID;
$actionFactory->setAction($editButton, $viewName = "relations/editCustomInfo", $holder = ".customInfoContainer .editFormContainer", $attr, $loading = TRUE);

// Return output
return $appContent->getReport();

function getCustomInfoRow($type, $name, $value)
{
	$infoRow = DOM::create("div", "", "", "infoRow");
	HTML::addClass($infoRow, $type);
	
	// Create ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($infoRow, $ico);
	
	$ifv = DOM::create("div", $name, "", "iname");
	DOM::append($infoRow, $ifv);
	
	$ifv = DOM::create("div", $value, "", "ivalue");
	DOM::append($infoRow, $ifv);
	
	return $infoRow;
}
//#section_end#
?>