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
importer::import("ENP", "Relations");
importer::import("UI", "Content");

// Import APP Packages
application::import("Security");
//#section_end#
//#section#[view]
use \AEL\Identity\account;
use \APP\Security\privateAppKey;
use \ENP\Relations\ePerson;
use \UI\Content\JSONContent;

// Create json content
$jsonContent = new JSONContent();

// Check request method
if (!engine::isPost())
{
	// Show error
	$error = array();
	$error['message'] = "You are using a wrong request method for this call. Use POST.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Validate the key
if (!privateAppKey::validate())
{
	// Show error
	$error = array();
	$error['message'] = "Your api key is not valid or registered in your settings.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Import user as a person
$ePerson = new ePerson();
$status = $ePerson->import($_POST['aid']);
if (!$status)
{
	// Show error
	$info = array();
	$info['status'] = 0;
	$info['message'] = "An error occurred while trying to import the user account. Please try again later.";
}
else
{
	// Successfull update
	$info = array();
	$info['status'] = 1;
	$info['message'] = "User account imported successfully!";
	$info['contact_id'] = $ePerson->getPersonID();
	$info['contact_info'] = $ePerson->info();
	$info['user_info'] = $ePerson->getAccountInfo();
}

// Return report
return $jsonContent->getReport($info, $allowOrigin = "", $withCredentials = FALSE, $key = "import");
//#section_end#
?>