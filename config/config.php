<?php
		/**
		 * @copyright	Copyright (C) René Martin, 2012. All rights reserved.
		 * @license		GNU General Public License version 2 or later; see LICENSE.txt
		 **/

	// The key to the public version of the Google spreadsheet to work with.
	$GLOBALS['pupblicSpreadSheetKey'] = "0AndFUHBiUH0OdGhoQWRpMW80QzQ5TVhkQXZHMDBDTGc";

	// This time (in seconds) indicates how long cached files on the server are allowed to be used again before they must be redownloaded
	$GLOBALS['filecachetime'] = 5 * 60 * 1000;

	// This path indicates the subdirectory of the webservice where the cached files should be saved
	$GLOBALS['cachefilepath'] = "cache/";

	// Put the path from the apache root directory to the acutal source folder here.
	// e.g. if you call the main page with http://www.domain.com/coding/path/index.php
	// put "coding/path/" here.
	$GLOBALS['pathfromserverroot'] = "";

	// If you want that every one can book objects you have to put Google login infomations here.
	// The Google account must have access to the spreadsheet with the key given above.
	// If you want the user to fill in Google login informations himself leave this blank.
	$GLOBALS['globalgoogleusername'] = "";
	$GLOBALS['globalgooglepassword'] = "";
?>