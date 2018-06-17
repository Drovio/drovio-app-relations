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
importer::import("UI", "Content");

// Import APP Packages
application::import("Security");
//#section_end#
//#section#[view]
use \APP\Security\privateAppKey;
use \ENP\Relations\ePerson;
use \ENP\Relations\ePersonAddress;
use \UI\Content\JSONContent;

// Create json content
$jsonContent = new JSONContent();

// Validate the key
if (!privateAppKey::validate())
{
	// Show error
	$error = array();
	$error['message'] = "Your api key is not valid or registered in your settings.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Get person information
$personID = engine::getVar("pid");
$ePerson = new ePerson($personID);

// Check if there is an account id parameter
$accountID = engine::getVar("aid");
if (!empty($accountID))
{
	// Load person from account and import if necessary
	$status = $ePerson->initWithAccount($accountID);
	if (!$status)
		$ePerson->import($accountID);
	
	// Get new person id
	$personID = $ePerson->getPersonID();
}

// Get all addresses
$addressManager = new ePersonAddress($personID);
$addressList = $addressManager->getAllAddresses();

// Check if there is a given address id
$addressID = engine::getVar("add_id");
if (empty($addressID))
	$response = $addressList;
else
	$response[$addressID] = $addressList[$addressID];

// Return report
return $jsonContent->getReport($response, $allowOrigin = "", $withCredentials = FALSE, $key = "address_list");
//#section_end#
?>