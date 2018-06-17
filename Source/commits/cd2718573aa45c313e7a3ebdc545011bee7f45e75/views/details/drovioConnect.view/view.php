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
importer::import("UI", "Forms");

// Import APP Packages
//#section_end#
//#section#[view]
use \API\Profile\person;
use \ENP\Relations\ePerson;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

$personID = engine::getVar("pid");
if (engine::isPost())
{
	// Get person id to connect
	$connectPersonID = engine::getVar("dpid");
	
	// Connect to person
	$ePerson = new ePerson($personID);
	$ePerson->connectToPerson($connectPersonID);
	
	// Load drovio person info
	$personInfoElement = $appContent->loadView("details/drovioPersonInfo");
	$appContent->buildElement($personInfoElement);
	
	// Return report
	return $appContent->getReport($holder = ".section.connect", $method = APPContent::REPLACE_METHOD);
}

// Build the application view content
$appContent->build("", "personInfoContainer", TRUE);
$personInfo = HTML::select(".personInfo")->item(0);

// Get person id to show detail for
$ePerson = new ePerson($personID);
$connectPersonIDs = $ePerson->getDrovioPersonsByMail();
foreach ($connectPersonIDs as $connectPersonID)
{
	// Get person info
	$connectPersonInfo = person::info($connectPersonID);
	
	// Get info row
	$value = $connectPersonInfo['firstname']." ".$connectPersonInfo['lastname']." (".$connectPersonInfo['mail'].")";
	$weblink = $appContent->getWeblink($connectPersonInfo['profile_url'], $value, $target = "_blank", $class = "");
	$infoRow = getInfoRow_connect($weblink, $personID, $connectPersonInfo);
	DOM::append($personInfo, $infoRow);
}

// Return output
return $appContent->getReport();


function getInfoRow_connect($ivalue, $personID, $connectPersonInfo)
{
	// Get person id and other info
	$connectPersonID = $connectPersonInfo['id'];
	
	// Create info row
	$infoRow = DOM::create("div", "", "", "infoRow");
	
	// Create ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($infoRow, $ico);
	
	if (!empty($connectPersonInfo['profile_image_url']))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $connectPersonInfo['profile_image_url']);
		DOM::append($ico, $img);
	}
	
	// Create form to connect to person
	$form = new simpleForm();
	$connectForm = $form->build("", $defaultButtons = FALSE)->engageApp("details/drovioConnect")->get();
	HTML::addClass($connectForm, "iform");
	DOM::append($infoRow, $connectForm);
	
	$input = $form->getInput($type = "hidden", $name = "pid", $value = $personID, $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$input = $form->getInput($type = "hidden", $name = "dpid", $value = $connectPersonID, $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$appContent = new APPContent();
	$title = $appContent->getLiteral("relations.details", "btn_connect");
	$submit = $form->getSubmitButton($title);
	HTML::addClass($submit, "no_margin");
	$form->append($submit);
	
	$value = DOM::create("div", $ivalue, "", "ivalue");
	DOM::append($infoRow, $value);
	
	return $infoRow;
}
//#section_end#
?>