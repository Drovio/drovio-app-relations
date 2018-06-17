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
importer::import("AEL", "Literals");
importer::import("ENP", "Relations");
importer::import("UI", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\popups\popup;

use \ENP\Relations\ePerson;

$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

if (engine::isPost())
{
	// Check if something is empty
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check contact name
	if (empty($_POST['fullname']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = appLiteral::get("main.create", "lbl_fullname_placeholder");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check email or phone
	if (empty($_POST['email_phone']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = appLiteral::get("main.create", "lbl_email_phone_placeholder");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create new contact
	$ePerson = new ePerson();
	
	$name = $_POST['fullname'];
	$nameParts = explode(" ", $name);
	$firstname = $nameParts[0];
	unset($nameParts[0]);
	$lastname = implode(" ", $nameParts);
	$status = $ePerson->create(ePerson::RELATION_CUSTOMER, $_POST['email_phone'], $firstname, $lastname);
	
	// If there is an error in creating the library, show it
	if (!$status)
	{
		$err_header = appLiteral::get("main.create", "lbl_create");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating relation..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the application view content
$appContent->build("", "createNewContactDialog", TRUE);

$formContainer = HTML::select(".createNewContactDialog .formContainer")->item(0);
// Build form
$form = new simpleForm("");
$imageForm = $form->build($action = "", $defaultButtons = FALSE)->engageApp("createNewContact")->get();
DOM::append($formContainer, $imageForm);

// Contact name
$ph = appLiteral::get("main.create", "lbl_fullname_placeholder", array(), FALSE);
$input = $form->getInput($type = "text", $name = "fullname", $value = "", $class = "bginp", $autofocus = TRUE, $required = TRUE);
DOM::attr($input, "placeholder", $ph);
$form->append($input);

$ph = appLiteral::get("main.create", "lbl_email_phone_placeholder", array(), FALSE);
$input = $form->getInput($type = "text", $name = "email_phone", $value = "", $class = "bginp", $autofocus = FALSE, $required = TRUE);
DOM::attr($input, "placeholder", $ph);
$form->append($input);

$title = appLiteral::get("main.create", "lbl_create");
$create_btn = $form->getSubmitButton($title, $id = "btn_create", $name = "");
$form->append($create_btn);

// Create popup
$pp = new popup();
$pp->type($type = popup::TP_PERSISTENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($appContent->get());

return $pp->getReport();
//#section_end#
?>