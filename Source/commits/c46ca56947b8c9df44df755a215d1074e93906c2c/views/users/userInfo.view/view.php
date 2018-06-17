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
importer::import("AEL", "Identity");
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Identity\account;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "userInfoContainer", TRUE);
$viewContainer = HTML::select(".userInfo")->item(0);

// Get account id to show detail for
$accountID = engine::getVar("aid");
$accountInfo = account::getInstance()->info($accountID);

$infoRow = getPersonInfoRow("info", $accountInfo['title']);
DOM::append($viewContainer, $infoRow);

$infoRow = getPersonInfoRow("mail", $accountInfo['mail']);
DOM::append($viewContainer, $infoRow);

// Action to switch to details view
$appContent->addReportAction($name = "listviewer.switchto.details");

// Return output
return $appContent->getReport();

function getPersonInfoRow($type, $value)
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