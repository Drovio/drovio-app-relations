<?php
//#section#[header]
// Namespace
namespace APP\Security;

require_once($_SERVER['DOCUMENT_ROOT'].'/_domainConfig.php');

// Use Important Headers
use \API\Platform\importer;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import application loader
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;
//#section_end#
//#section#[class]
/**
 * @library	APP
 * @package	Security
 * 
 * @copyright	Copyright (C) 2015 Relations. All rights reserved.
 */

importer::import("AEL", "Security", "privateKey");
importer::import("API", "Platform", "engine");

use \AEL\Security\privateKey;
use \API\Platform\engine;

/**
 * Application private key validator.
 * 
 * Extends the Application private key interface and can validate the received key from the API request.
 * 
 * @version	0.1-1
 * @created	November 3, 2015, 19:28 (GMT)
 * @updated	November 3, 2015, 19:28 (GMT)
 */
class privateAppKey extends privateKey
{
	/**
	 * Validate whether the request akey is private and valid for the team it represents.
	 * 
	 * @return	boolean
	 * 		True if valid, false otherwise.
	 */
	public static function validate()
	{
		// Get current key
		$akey = self::getAPIKey();
		
		// Get team id from key
		$teamID = self::getTeamID();
		
		// Validate given key with the team
		return parent::validate($akey, $teamID);
	}
	
	/**
	 * Get the team id from the given api key.
	 * 
	 * @return	integer
	 * 		The team id or NULL on error.
	 */
	public static function getTeamID()
	{
		// Get current key
		$akey = self::getAPIKey();
		
		// Get team id from key
		return parent::getTeamID($akey);
	}
	
	/**
	 * Get the current request api key.
	 * 
	 * @return	string
	 * 		The API key.
	 */
	private static function getAPIKey()
	{
		return engine::getVar("akey");
	}
}
//#section_end#
?>