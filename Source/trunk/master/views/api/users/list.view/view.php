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

// Validate the key
if (!privateAppKey::validate())
{
	// Show error
	$error = array();
	$error['message'] = "Your api key is not valid or registered in your settings.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Get all user accounts
$start = engine::getVar("start");
$count = engine::getVar("count");
$allAccounts = account::getInstance()->getAllAccounts($start, $count);

// Return report
return $jsonContent->getReport($allAccounts, $allowOrigin = "", $withCredentials = FALSE, $key = "users");
//#section_end#
?>