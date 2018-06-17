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
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Identity\account;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "userCardContainer", TRUE);

// Get account id to show detail for
$accountID = engine::getVar("aid");
$accountInfo = account::getInstance()->info($accountID);

// Set name
$name = HTML::select(".userCard .sidebar .name")->item(0);
HTML::innerHTML($name, $accountInfo['title']);

// Contact info section
$detailsContainer = HTML::select(".userCard .detailsContainer")->item(0);
$section = DOM::create("div", "", "", "section user_info");
DOM::append($detailsContainer, $section);

// Load view
$attr = array();
$attr['aid'] = $accountID;
$viewContainer = $appContent->getAppViewContainer($viewName = "users/userInfo", $attr, $startup = FALSE, $containerID = "userInfoViewContainer", $loading = FALSE, $preload = TRUE);
DOM::append($section, $viewContainer);

// Delete relation button
$importButton = HTML::select(".userCard .abutton.import")->item(0);
$attr = array();
$attr['aid'] = $accountID;
$actionFactory->setAction($importButton, $viewName = "users/importUser", $holder = "", $attr, $loading = TRUE);

// Action to switch to details view
$appContent->addReportAction($name = "listviewer.switchto.details");

// Return output
return $appContent->getReport();
//#section_end#
?>