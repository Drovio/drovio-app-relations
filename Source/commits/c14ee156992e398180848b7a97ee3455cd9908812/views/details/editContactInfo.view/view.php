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
use \ENP\Relations\ePerson;
use \ENP\Relations\ePersonAddress;
use \ENP\Relations\ePersonMail;
use \ENP\Relations\ePersonPhone;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Get person id to show detail for
$personID = engine::getVar("pid");
if (engine::isPost())
{
	// Update person information
	$person = new ePerson($personID);
	$person->update($_POST['firstname'], $_POST['lastname'], $_POST['middlename'], $_POST['notes'], $_POST['birthday']);
	
	// Update phones
	$phoneManager = new ePersonPhone($personID);
	$allPhones = $phoneManager->getAllPhones();
	foreach ($allPhones as $phoneID => $phoneInfo)
	{
		$pPhone = new ePersonPhone($personID, $phoneID);
		$phoneValue = $_POST['phone'][$phoneID];
		if (!isset($_POST['phone_type'][$phoneID]) || empty($phoneValue))
		{
			$pPhone->remove();
			continue;
		}
		
		// Get phone value
		$typeID = $_POST['phone_type'][$phoneID];
		$pPhone->update($typeID, $phoneValue, $countryID = "");
	}
	
	// Create new phones
	foreach ($_POST['new_phone_type'] as $phoneID => $typeID)
	{
		$phoneValue = $_POST['new_phone'][$phoneID];
		if (!empty($phoneValue))
			$phoneManager->create($typeID, $phoneValue, $countryID = "");
	}
	
	
	// Update mail
	$mailManager = new ePersonMail($personID);
	$allMail = $mailManager->getAllMail();
	foreach ($allMail as $mailID => $mailInfo)
	{
		$pMail = new ePersonMail($personID, $mailID);
		$mailValue = $_POST['mail'][$mailID];
		if (!isset($_POST['mail_type'][$mailID]) || empty($mailValue))
		{
			$pMail->remove();
			continue;
		}
		
		// Get phone value
		$typeID = $_POST['mail_type'][$mailID];
		$pMail->update($typeID, $mailValue);
	}
	
	// Create new mail
	foreach ($_POST['new_mail_type'] as $mailID => $typeID)
	{
		$mailValue = $_POST['new_mail'][$mailID];
		if (!empty($mailValue))
			$mailManager->create($typeID, $mailValue);
	}
	
	
	// Update addresses
	$addressManager = new ePersonAddress($personID);
	$allAddresses = $addressManager->getAllAddresses();
	foreach ($allAddresses as $addressID => $addressInfo)
	{
		$pAddress = new ePersonAddress($personID, $addressID);
		$addressValue = $_POST['address'][$addressID];
		if (!isset($_POST['address_type'][$addressID]) || empty($addressValue))
		{
			$pAddress->remove();
			continue;
		}
		
		// Get phone value
		$typeID = $_POST['address_type'][$addressID];
		$addressParts = explode(",", $addressValue);
		$address = $addressParts[0];
		$postal_code = $addressParts[1];
		$city = $addressParts[2];
		$pAddress->update($typeID, trim($address), trim($postal_code), trim($city), $countryID = "");
	}
	
	// Create new address
	foreach ($_POST['new_address_type'] as $addressID => $typeID)
	{
		$addressValue = $_POST['new_address'][$addressID];
		$addressParts = explode(",", $addressValue);
		$address = $addressParts[0];
		$postal_code = $addressParts[1];
		$city = $addressParts[2];
		if (!empty($addressValue))
			$addressManager->create($typeID, trim($address), trim($postal_code), trim($city), $countryID = "");
	}
	
	
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add action to reload info
	$succFormNtf->addReportAction($type = "contactinfo.reload", $value = "");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the application view content
$appContent->build("", "editContactInfoContainer", TRUE);
$formContainer = HTML::select(".editContactInfo .formContainer")->item(0);

// Build form
$form = new simpleForm();
$editForm = $form->build()->engageApp("details/editContactInfo")->get();
DOM::append($formContainer, $editForm);

$input = $form->getInput($type = "hidden", $name = "pid", $value = $personID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);


// Person basic info
$person = new ePerson($personID);
$personInfo = $person->info();

$title = $appContent->getLiteral("relations.details.edit", "hd_basicInfo");
$group = getEditGroup($title, FALSE);
$form->append($group);

$title = $appContent->getLiteral("relations.details.edit", "lbl_firstname");
$ph = $appContent->getLiteral("relations.details.edit", "lbl_firstname", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $personInfo['firstname'], $ph, $name = "firstname");
DOM::append($group, $fRow);

$title = $appContent->getLiteral("relations.details.edit", "lbl_middlename");
$ph = $appContent->getLiteral("relations.details.edit", "lbl_middlename", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $personInfo['middle_name'], $ph, $name = "middlename");
DOM::append($group, $fRow);

$title = $appContent->getLiteral("relations.details.edit", "lbl_lastname");
$ph = $appContent->getLiteral("relations.details.edit", "lbl_lastname", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $personInfo['lastname'], $ph, $name = "lastname");
DOM::append($group, $fRow);

$title = $appContent->getLiteral("relations.details.edit", "lbl_birthday");
$ph = $appContent->getLiteral("relations.details.edit", "lbl_birthday", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $personInfo['date_of_birth'], $ph, $name = "birthday", $type = "date");
DOM::append($group, $fRow);





// Phone group
$title = $appContent->getLiteral("relations.details.edit", "hd_phones");
$group = getEditGroup($title);
$form->append($group);

// Get all phones
$phoneManager = new ePersonPhone($personID);
$phoneTypes = $phoneManager->getPhoneTypes();
$allPhones = $phoneManager->getAllPhones();
foreach ($allPhones as $phoneID => $phoneInfo)
{
	$ph = $appContent->getLiteral("relations.details.edit", "lbl_phone_new_ph", array(), FALSE);
	$fRow = getSelectFormRow($form, $typeResource = $phoneTypes, $typeValue = $phoneInfo['type_id'], $valueValue = $phoneInfo['phone'], $class = "", $ph, $name = "phone", $id = $phoneID, $removable = TRUE);
	DOM::append($group, $fRow);
}

// Create a new row
$ph = $appContent->getLiteral("relations.details.edit", "lbl_phone_new_ph", array(), FALSE);
$fRow = getSelectFormRow($form, $typeResource = $phoneTypes, $typeValue = "", $valueValue = "", $class = "new", $ph, $name = "new_phone", $id = "");
DOM::append($group, $fRow);



// Mail group
$title = $appContent->getLiteral("relations.details.edit", "hd_mail");
$group = getEditGroup($title);
$form->append($group);

// Get all mail
$mailManager = new ePersonMail($personID);
$mailTypes = $mailManager->getMailTypes();
$allMail = $mailManager->getAllMail();
foreach ($allMail as $mailID => $mailInfo)
{
	$ph = $appContent->getLiteral("relations.details.edit", "lbl_mail_new_ph", array(), FALSE);
	$fRow = getSelectFormRow($form, $typeResource = $mailTypes, $typeValue = $mailInfo['type_id'], $valueValue = $mailInfo['mail'], $class = "", $ph, $name = "mail", $id = $mailID, $removable = TRUE);
	DOM::append($group, $fRow);
}

// Create a new row
$ph = $appContent->getLiteral("relations.details.edit", "lbl_mail_new_ph", array(), FALSE);
$fRow = getSelectFormRow($form, $typeResource = $mailTypes, $typeValue = "", $valueValue = "", $class = "new", $ph, $name = "new_mail", $id = "");
DOM::append($group, $fRow);



// Address group
$title = $appContent->getLiteral("relations.details.edit", "hd_addresses");
$group = getEditGroup($title);
$form->append($group);

// Get all addresses
$addressManager = new ePersonAddress($personID);
$addressTypes = $addressManager->getAddressTypes();
$allAddresses = $addressManager->getAllAddresses();
foreach ($allAddresses as $addressID => $addressInfo)
{
	$ph = $appContent->getLiteral("relations.details.edit", "lbl_address_new_ph", array(), FALSE);
	$valueValue = $addressInfo['address'].", ".$addressInfo['postal_code'].", ".$addressInfo['city'];
	$fRow = getSelectFormRow($form, $typeResource = $addressTypes, $typeValue = $addressInfo['type_id'], $valueValue, $class = "", $ph, $name = "address", $id = $addressID, $removable = TRUE);
	DOM::append($group, $fRow);
}

// Create a new row
$ph = $appContent->getLiteral("relations.details.edit", "lbl_address_new_ph", array(), FALSE);
$fRow = getSelectFormRow($form, $typeResource = $addressTypes, $typeValue = "", $valueValue = "", $class = "new", $ph, $name = "new_address", $id = "");
DOM::append($group, $fRow);



// Notes group
$title = $appContent->getLiteral("relations.details.edit", "hd_notes");
$group = getEditGroup($title);
$form->append($group);

$textarea = $form->getTextarea($name = "notes", $value = $personInfo['notes'], $class = "ftext", $autofocus = FALSE, $required = FALSE);
DOM::append($group, $textarea);
$ph = $appContent->getLiteral("relations.details.edit", "hd_notes", array(), FALSE);
DOM::attr($textarea, "placeholder", $ph);
DOM::attr($textarea, "rows", 6);



// Set action to switch to edit info
$appContent->addReportAction($type = "contactinfo.edit", $value = "");

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

function getSelectFormRow($form, $typeResource, $typeValue, $valueValue, $class, $ph, $name, $id = "", $removable = FALSE)
{
	// Create a new row
	$fRow = DOM::create("div", "", "", "frow");
	HTML::addClass($fRow, $class);
	
	$select = $form->getResourceSelect($name."_type[$id]", $multiple = "", $class = "fselect", $resource = $typeResource, $selectedValue = $typeValue);
	DOM::append($fRow, $select);
	$input = $form->getInput($type = "text", $name."[$id]", $value = $valueValue, $class = "finput", $autofocus = FALSE, $required = FALSE);
	DOM::append($fRow, $input);
	DOM::attr($input, "placeholder", $ph);
	
	// Remove ico
	if ($removable)
	{
		$removeIco = DOM::create("div", "", "", "ico remove");
		DOM::append($fRow, $removeIco);
	}
	
	return $fRow;
}

function getSimpleFormRow($form, $labelTitle, $valueValue, $ph, $name, $inputType = "text")
{
	// Create a new row
	$fRow = DOM::create("div", "", "", "frow");
	
	$input = $form->getInput($type = $inputType, $name, $value = $valueValue, $class = "finput", $autofocus = FALSE, $required = FALSE);
	DOM::attr($input, "placeholder", $ph);
	$inputID = DOM::attr($input, "id");
	$label = $form->getLabel($labelTitle, $for = $inputID, $class = "flabel");
	
	// Append to frow
	DOM::append($fRow, $label);
	DOM::append($fRow, $input);
	
	return $fRow;
}
//#section_end#
?>