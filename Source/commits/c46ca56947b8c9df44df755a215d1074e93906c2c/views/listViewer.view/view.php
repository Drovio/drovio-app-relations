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

// Initialize identity account id lists to skip later
$identityAccounts = array();

// Get all person relations
$contactsListContainer = HTML::select(".listContainer .clist.contacts")->item(0);
foreach ($personRelations as $personInfo)
{
	$personID = $personInfo['id'];
	
	// Create person list item
	$listItem = DOM::create("div", "", "", "listItem");
	DOM::append($contactsListContainer, $listItem);
	
	// Set action
	$attr = array();
	$attr['pid'] = $personID;
	$actionFactory->setAction($listItem, "relations/relationCard", ".relationsListView .detailsContainer .wbox.details", $attr, $loading = TRUE);
	
	// Ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($listItem, $ico);
	
	// Name
	$personName = $personInfo['firstname']." ".$personInfo['lastname'];
	$name = DOM::create("div", $personName, "", "name");
	DOM::append($listItem, $name);
	
	// Add to identity users
	if (!empty($personInfo['identity_account_id']))
		$identityAccounts[$personInfo['identity_account_id']] = 1;
}
if (empty($personRelations))
{
	// Clear relations list container
	HTML::remove($contactsListContainer);
	
	// Add header
	$title = $appContent->getLiteral("relations.list", "hd_noRelations");
	$hd = DOM::create("h2", $title, "", "hd");
	DOM::append($contactsListContainer, $hd);
}


// Get all identity users
$allAccounts = account::getInstance()->getAllAccounts($start = 0, $count = 100000);
$usersListContainer = HTML::select(".listContainer .clist.users")->item(0);
foreach ($allAccounts as $accountInfo)
{
	// Check identity and skip
	if (isset($identityAccounts[$accountInfo['id']]))
		continue;
	
	// Build list item
	$listItem = DOM::create("div", "", "", "listItem");
	DOM::append($usersListContainer, $listItem);
	
	// Set action
	$attr = array();
	$attr['aid'] = $accountInfo['id'];
	$actionFactory->setAction($listItem, "users/userCard", ".relationsListView .detailsContainer .wbox.details", $attr, $loading = TRUE);
	
	$form = new simpleForm();
	$addForm = $form->build("", FALSE)->engageApp("users/importUser")->get();
	HTML::addClass($addForm, "importform");
	DOM::append($listItem, $addForm);

	// Person id
	$input = $form->getInput($type = "hidden", $name = "aid", $value = $accountInfo['id'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);

	// Submit
	$title = $appContent->getLiteral("main.list", "btn_import");
	$button = $form->getSubmitButton($title, $id = "btn_import");
	$form->append($button);
	
	// Ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($listItem, $ico);
	
	// Name
	$name = DOM::create("div", $accountInfo['title'], "", "name");
	DOM::append($listItem, $name);
}
if (empty($allAccounts))
{
	// Clear users list container
	HTML::remove($usersListContainer);
}
//print_r($allAccounts);

// Return output
return $appContent->getReport();
//#section_end#
?>