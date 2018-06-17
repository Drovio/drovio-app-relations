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
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "personInfoContainer", TRUE);
$personInfo = HTML::select(".personInfo")->item(0);

// Get person id to show detail for
$personID = engine::getVar("pid");
$ePerson = new ePerson($personID);
$publicPersonInfo = $ePerson->getConnectedPersonInfo();

// Name
$value = $publicPersonInfo['title'];
$weblink = $appContent->getWeblink($publicPersonInfo['profile_url'], $value, $target = "_blank", $class = "");
$infoRow = getInfoRow2("info", $weblink);
DOM::append($personInfo, $infoRow);

// Mail
$infoRow = getInfoRow2("mail", $publicPersonInfo['mail']);
DOM::append($personInfo, $infoRow);

// Return output
return $appContent->getReport();


function getInfoRow2($type, $value)
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