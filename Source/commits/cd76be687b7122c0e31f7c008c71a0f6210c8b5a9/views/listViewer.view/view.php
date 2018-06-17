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
importer::import("UI", "Apps");
importer::import("UI", "Forms");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Identity\account;
use \ENP\Relations\ePerson;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "relationsListViewContainer", TRUE);

// Get all relation persons
$personRelations = ePerson::getPersons();
$allAccounts = account::getInstance()->getAllAccounts($start = 0, $count = 100000);

// Get all accounts
$allRelations = array();
foreach ($personRelations as $personInfo)
{
	$id = (isset($personInfo['identity_account_id']) ? $personInfo['identity_account_id'] : $personInfo['id']);
	$personInfo['rel_type'] = (empty($personInfo['identity_account_id']) ? "contact" : "user");
	$allRelations[$id] = $personInfo;
}
foreach ($allAccounts as $accountInfo)
	if (!isset($allRelations[$accountInfo['id']]))
	{
		$accountInfo['rel_type'] = "user";
		$allRelations[$accountInfo['id']] = $accountInfo;
	}
uasort($allRelations, "sort_relations");

// Get all person relations
$contactsListContainer = HTML::select(".listContainer")->item(0);
foreach ($allRelations as $relationInfo)
{
	// Create person list item
	$listItem = DOM::create("div", "", "", "listItem");
	DOM::append($contactsListContainer, $listItem);
	HTML::addClass($listItem, $relationInfo['rel_type']);
	
	// Set action
	$attr = array();
	$attr['pid'] = $relationInfo['id'];
	if ($relationInfo['rel_type'] == "user" && !isset($relationInfo['identity_account_id']))
		$attr['aid'] = $relationInfo['id'];
	$actionFactory->setAction($listItem, "relations/relationCard", ".relationsListView .detailsContainer .wbox.details", $attr, $loading = TRUE);
	
	// Ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($listItem, $ico);
	
	// Name
	$relationName = (empty($relationInfo['title']) ? $relationInfo['firstname']." ".$relationInfo['lastname'] : $relationInfo['title']);
	$name = DOM::create("div", $relationName, "", "name");
	DOM::append($listItem, $name);
}

if (empty($allRelations))
{
	// Clear relations list container
	HTML::innerHTML($contactsListContainer, "");
	
	// Add header
	$title = $appContent->getLiteral("relations.list", "hd_noRelations");
	$hd = DOM::create("h2", $title, "", "hd");
	DOM::append($contactsListContainer, $hd);
}

// Return output
return $appContent->getReport();

function sort_relations($relA, $relB)
{
	$relAtitle = (isset($relA['title']) ? $relA['title'] : $relA['firstname']." ".$relA['lastname']);
	$relBtitle = (isset($relB['title']) ? $relB['title'] : $relB['firstname']." ".$relB['lastname']);
	if ($relAtitle == $relBtitle)
		return 0;
	
	return ($relAtitle < $relBtitle) ? -1 : 1;
}
//#section_end#
?>