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
importer::import("AEL", "Resources");
importer::import("API", "Profile");
importer::import("ENP", "Relations");
importer::import("UI", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \AEL\Resources\filesystem\fileManager;
use \API\Profile\team;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\popups\popup;

use \ENP\Relations\ePerson;
use \ENP\Relations\ePersonMail;
use \ENP\Relations\ePersonPhone;
use \ENP\Relations\ePersonAddress;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

if (engine::isPost())
{
	// Get csv separator
	$csv_sep = engine::getVar("csv_separator");
	$csv_sep = (empty($csv_sep) ? ";" : $csv_sep);
	
	// Get multiple value separator
	$separator = engine::getVar("separator");
	$separator = (empty($separator) ? ":" : $separator);
	
	// Export contacts in csv
	$headers = array();
	$headers[] = "Firstname";
	$headers[] = "Lastname";
	$headers[] = "Middlename";
	$headers[] = "notes";
	$headers[] = "email";
	$headers[] = "phones";
	$headers[] = "addresses";
	$csv = implode($csv_sep, $headers)."\n";
	
	// Get all persons and add information
	$allPersons = ePerson::getPersons();
	foreach ($allPersons as $personInfo)
	{
		$personID = $personInfo['id'];
		
		// Create csv row array
		$csv_row = array();
		
		// Add person information
		$csv_row[] = $personInfo['firstname'];
		$csv_row[] = $personInfo['lastname'];
		$csv_row[] = $personInfo['middle_name'];
		$csv_row[] = $personInfo['notes'];
		
		// Add email addresses
		$pMail = new ePersonMail($personID);
		$allMail = $pMail->getAllMail();
		$mailValue = "";
		foreach ($allMail as $mailInfo)
			$mailValue .= $mailInfo['mail'].$separator;
		$mailValue = trim($mailValue, $separator);
		$csv_row[] = $mailValue;
		
		// Add all phones
		$pPhone = new ePersonPhone($personID);
		$allPhones = $pPhone->getAllPhones();
		$phoneValue = "";
		foreach ($allPhones as $phoneInfo)
			$phoneValue .= $phoneInfo['phone'].$separator;
		$phoneValue = trim($phoneValue, $separator);
		$csv_row[] = $phoneValue;
		
		// Add all addresses
		$pAddress = new ePersonAddress($personID);
		$allAddresses = $pAddress->getAllAddresses();
		$addressValue = "";
		foreach ($allAddresses as $addressInfo)
			$addressValue .= $addressInfo['address'].", ".$addressInfo['postal_code'].", ".$addressInfo['city'].$separator;
		$addressValue = str_replace(";", ",", $addressValue);
		$addressValue = trim($addressValue, $separator);
		$csv_row[] = $addressValue;
		
		// New line
		$csv .= implode($csv_sep, $csv_row)."\n";
	}
	
	// Get team information
	$teamInfo = team::info();
	$teamName = $teamInfo['uname'];
	
	$filename = "/contacts/".$teamName."_contacts_".time().".csv";
	
	// Save csv to a file
	$fm = new fileManager($mode = fileManager::TEAM_MODE, $shared = TRUE);
	$status = $fm->create($filename, $csv, $recursive = TRUE);
	
	// Create content
	$appContent->build("", "downloader");
	
	// Add header
	$attr = array();
	$attr['fname'] = "Shared Folder > ".$filename;
	$title = $appContent->getLiteral("main.export", "lbl_exportFilename", $attr);
	$hd = DOM::create("h2", $title, "", "hd");
	$appContent->append($hd);
	
	// Create download button with attributes
	$title = $appContent->getLiteral("main.export", "lbl_downloadFile");
	$dlButton = DOM::create("div", $title, "btn_download");
	$attr = array();
	$attr['fname'] = $filename;
	$attr['shared'] = 1;
	$actionFactory->setDownloadAction($dlButton, $viewName = "export/downloadFile", $attr);
	$appContent->append($dlButton);
	
	return $appContent->getReport($holder = ".exportContactsDialog .downloaderContainer");
}

// Build the application view content
$appContent->build("", "exportContactsDialog", TRUE);

$formContainer = HTML::select(".exportContactsDialog .formContainer")->item(0);
// Build form
$form = new simpleForm("");
$imageForm = $form->build($action = "", $defaultButtons = FALSE)->engageApp("export/exportDialog")->get();
DOM::append($formContainer, $imageForm);

// Contact name
$ph = appLiteral::get("main.create", "lbl_fullname_placeholder", array(), FALSE);
$input = $form->getInput($type = "text", $name = "fullname", $value = "", $class = "bginp", $autofocus = TRUE, $required = FALSE);
DOM::attr($input, "placeholder", $ph);
//$form->append($input);


$title = appLiteral::get("main.export", "lbl_export");
$create_btn = $form->getSubmitButton($title, $id = "btn_export", $name = "");
$form->append($create_btn);

// Create popup
$pp = new popup();
$pp->type($type = popup::TP_PERSISTENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($appContent->get());

return $pp->getReport();
//#section_end#
?>