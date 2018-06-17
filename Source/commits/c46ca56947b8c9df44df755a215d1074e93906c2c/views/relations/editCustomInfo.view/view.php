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
importer::import("UI", "Forms");

// Import APP Packages
//#section_end#
//#section#[view]
use \ENP\Relations\ePersonInfo;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Get person id to show detail for
$personID = engine::getVar("pid");
$cPersonInfo = new ePersonInfo($personID);
if (engine::isPost())
{
	// Get all information in one array
	$info = array();
	foreach ($_POST['name'] as $index => $name)
		if (!empty($name))
			$info[$name] = $_POST['value'][$index];
	
	// Update person information
	$cPersonInfo->setAll($info);
	
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add action to reload info
	$succFormNtf->addReportAction($type = "custominfo.reload", $value = "");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the application view content
$appContent->build("", "editCustomInfoContainer", TRUE);
$formContainer = HTML::select(".editCustomInfo .formContainer")->item(0);

// Build form
$form = new simpleForm();
$editForm = $form->build()->engageApp("relations/editCustomInfo")->get();
DOM::append($formContainer, $editForm);

$input = $form->getInput($type = "hidden", $name = "pid", $value = $personID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);


// Person basic info
$personCustomInfo = $cPersonInfo->get();

$title = $appContent->getLiteral("relations.info", "hd_customInfo_details");
$group = getEditGroup($title);
$form->append($group);

// List all information fields
foreach ($personCustomInfo as $name => $value)
{
	$namePh = $appContent->getLiteral("relations.info", "lbl_info_name_ph", array(), FALSE);
	$valuePh = $appContent->getLiteral("relations.info", "lbl_info_value_ph", array(), FALSE);
	$fRow = getCustomFormRow($form, $name, $value, $class = "", $namePh, $valuePh, $removable = TRUE);
	DOM::append($group, $fRow);
}

// Create a new row
$namePh = $appContent->getLiteral("relations.info", "lbl_info_name_ph", array(), FALSE);
$valuePh = $appContent->getLiteral("relations.info", "lbl_info_value_ph", array(), FALSE);
$fRow = getCustomFormRow($form, $nameValue = "", $valueValue = "", $class = "new", $namePh, $valuePh, $removable = FALSE);
DOM::append($group, $fRow);


// Set action to switch to edit info
$appContent->addReportAction($type = "custominfo.edit", $value = "");

// Return output
return $appContent->getReport();

function getEditGroup($title, $newButton = TRUE)
{
	$group = DOM::create("div", "", "", "editGroup");
	
	// Add new button
	if ($newButton)
	{
		$create_new = DOM::create("div", "", "", "ico create_new");
		DOM::append($group, $create_new);
	}
	
	// Header
	$hd = DOM::create("h3", $title, "", "ghd");
	DOM::append($group, $hd);
	
	return $group;
}

function getCustomFormRow($form, $nameValue, $valueValue, $class, $namePh, $valuePh, $removable = TRUE)
{
	// Create a new row
	$fRow = DOM::create("div", "", "", "frow");
	HTML::addClass($fRow, $class);
	
	$input = $form->getInput($type = "text", "name[]", $value = $nameValue, $class = "finput", $autofocus = FALSE, $required = FALSE);
	DOM::append($fRow, $input);
	DOM::attr($input, "placeholder", $namePh);
	$input = $form->getInput($type = "text", "value[]", $value = $valueValue, $class = "finput", $autofocus = FALSE, $required = FALSE);
	DOM::append($fRow, $input);
	DOM::attr($input, "placeholder", $valuePh);
	
	// Remove ico
	if ($removable)
	{
		$removeIco = DOM::create("div", "", "", "ico remove");
		DOM::append($fRow, $removeIco);
	}
	
	return $fRow;
}
//#section_end#
?>