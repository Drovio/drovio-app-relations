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
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPMIMEContent;

// Create MIMEContent to download the file
$mimeContent = new APPMIMEContent();

$filename = engine::getVar("fname");
$shared = (engine::getVar("shared") == 1);

// Download contacts file
$mimeContent->set($filename, $type = APPMIMEContent::CONTENT_APP_STREAM, $mode = APPMIMEContent::TEAM_MODE, $shared);
return $mimeContent->getReport($suggestedFileName = basename($filename), $ignore_user_abort = FALSE, $removeFile = FALSE);
//#section_end#
?>