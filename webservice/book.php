<?php
		/**
		 * @copyright	Copyright (C) René Martin, 2012. All rights reserved.
		 * @license		GNU General Public License version 2 or later; see LICENSE.txt
		 **/

	// Possible results:
	// Success
	// Error-0 ... Not enough parameters
	// Error-1 ... No global google user set in config file but access was requested
	// Error-2 ... The given date was not found
	// Error-3 ... The selected object is not free on that date
	// Error-4 ... Username or password incorrect

	// This service requires ssl due to user data send.
	if($_SERVER["HTTPS"] != "on")
	{
	    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	    exit();
	}

	// POST is required on this service
	require_once("source/SpreadsheetAdapter.php");
	if(isset($_POST["dow"]) && $_POST["dow"] != "" && isset($_POST["date"]) && $_POST["date"] != "" && isset($_POST["name"]) && $_POST["name"] != "") {
		if(isset($_POST["usr"]) && $_POST["usr"] != "" && isset($_POST["pwd"]) && $_POST["pwd"] != "") {
			// Edit spreadsheet with user account
			$spreadsheetAdapter = new ssAdapter($_POST["usr"], $_POST["pwd"], $_POST["dow"], $_POST["date"], $_POST["name"]);
		} else {
			// Try to edit spreadsheet with global account
			$spreadsheetAdapter = new ssAdapter($_POST["dow"], $_POST["date"], $_POST["name"]);
		}
		$output = $spreadsheetAdapter->GetData();
	} else {
		$output = array('result' => 'Error-0');
	}
	
	echo json_encode($output);
?>