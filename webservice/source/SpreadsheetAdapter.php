<?php
		/**
		 * @copyright	Copyright (C) René Martin, 2012. All rights reserved.
		 * @license		GNU General Public License version 2 or later; see LICENSE.txt
		 **/

	require_once("../config/config.php");

	class ssAdapter {
		private $dataObject;

		// Including multiple constructors according to a comment at http://php.net/manual/de/language.oop5.decon.php
		public function __construct() { 
			$this->dataObject = array();
			$args = func_get_args(); 
			$argsStr = ''; 
			foreach ($args as $arg) { 
				$argsStr .= '_' . gettype($arg); 
			} 
			if (method_exists($this, $constructor = '_construct' . $argsStr)) 
				call_user_func_array(array($this, $constructor), $args); 
		}

		private function _construct() {
			$this->_intit_data_object(array());
		}

		private function _construct_string($a) {
			$this->_intit_data_object(array($a));
		}

		private function _construct_array($a) {
			$this->_intit_data_object($a);
		}

		// These next 2 constructors are used to find the the row where the first column equals $date in
		// $worksheet and set the second column of this row to $booker
		private function _construct_string_string_string($worksheet, $date, $booker) {
			if (isset($GLOBALS["globalgoogleusername"]) && $GLOBALS["globalgoogleusername"] != "" && isset($GLOBALS["globalgooglepassword"]) && $GLOBALS["globalgooglepassword"] != "") {
				$this->_set_data_with_client($GLOBALS["globalgoogleusername"], $GLOBALS["globalgooglepassword"], $worksheet, $date, $booker);
			} else {
				$this->dataObject["result"] = "Error-1";
			}
		}

		private function _construct_string_string_string_string_string($user, $password, $worksheet, $date, $booker) {
			$this->_set_data_with_client($user, $password, $worksheet, $date, $booker);
		}

		private function _set_data_with_client($user, $pass, $worksheet, $date, $booker) {
			// Include GData from the Zend Framework for the task of editing a cell
			// Authorization with GData is pretty slow and therefor the direct feeds
			// and cached files are used everywhere else
			$clientLibraryPath = $_SERVER["DOCUMENT_ROOT"].'/'.$GLOBALS["pathfromserverroot"].'webservice/source/';
			$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
			require_once '/source/zend/Loader.php';
			Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
			Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');

			// Authorize with Google
			$service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
			try {
				$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);	
			} catch (Exception $e) {
				$this->dataObject["result"] = "Error-4";
				return;
			}
			$spreadsheetService = new Zend_Gdata_Spreadsheets($client);
				
			$query = new Zend_Gdata_Spreadsheets_CellQuery();
			$query->setSpreadsheetKey($GLOBALS['pupblicSpreadSheetKey']);
			$query->setWorksheetId($worksheet);
			$cellFeed = $spreadsheetService->getCellFeed($query);
			$foundrow = -2;
			// Find row containing the given date and check if it is still free
			foreach($cellFeed as $cellEntry) {
				$row = $cellEntry->cell->getRow();
				$col = $cellEntry->cell->getColumn();
				$val = $cellEntry->cell->getText();
				if($foundrow == $row && $col == 2) {
					if($val == "")
						break;
					else
						$foundrow = -3;
				}
				if($val == $date && $col == 1)
				{
					$foundrow = $row;
				}
			}

			if ($foundrow > -1) {
				// If a row has been found edit the worksheet
				$updatedCell = $spreadsheetService->updateCell($foundrow, 2, $booker, $GLOBALS['pupblicSpreadSheetKey'], $worksheet);
				// Delete the cached file for this worksheet since it is defenately outdated now
				if(file_exists($GLOBALS['cachefilepath'].$worksheet))
					unlink($GLOBALS['cachefilepath'].$worksheet);
				$this->dataObject["result"] = "Success";
			} else {
				$this->dataObject["result"] = "Error".$foundrow;
			}
		}

		private function _intit_data_object($wsArrayToInclude) {
			// Get all worksheets
			$json_string = $this->_file_get_contents_utf8("https://spreadsheets.google.com/feeds/worksheets/".$GLOBALS['pupblicSpreadSheetKey']."/public/basic?alt=json");
			$obj = json_decode($json_string);
			foreach ($obj->{'feed'}->{'entry'} as $entry)
			{
				$wsname = preg_replace("~.*/(.*)~","$1",$entry->{'id'}->{'$t'});
				// Include all requested worksheets from the spreadsheet
				if(count($wsArrayToInclude) == 0 || in_array($wsname, $wsArrayToInclude)) {
					$this->dataObject[$wsname] = array("title"=>$entry->{'title'}->{'$t'});
					$this->_read_worksheet_into_data_object($wsname);
				}
			}
		}

		private function _read_worksheet_into_data_object($wsname) {
			$cachefilename = $GLOBALS['cachefilepath'].$wsname;
			// Use cached files if they are not older than the time defined in config file
			if(file_exists($cachefilename) && strtotime("now")-filemtime($cachefilename) < $GLOBALS['filecachetime']) {
				$this->dataObject[$wsname] = json_decode(file_get_contents($cachefilename), true);
			} else {
				// Get cell feed of current worksheet
				$json_string = $this->_file_get_contents_utf8("https://spreadsheets.google.com/feeds/cells/".$GLOBALS['pupblicSpreadSheetKey']."/".$wsname."/public/basic?alt=json");
				$obj = json_decode($json_string);
				$cellArray = array();
				// Convert the entry list into a two dimensional cell array
				foreach ($obj->{'feed'}->{'entry'} as $entry)
				{
					$cellColumn = preg_replace("~(\D)*\d*~","$1",$entry->{'title'}->{'$t'});
					$cellRow = preg_replace("~\D*(\d*)~","$1",$entry->{'title'}->{'$t'});
					if(!isset($cellArray[$cellRow]))
					{
						$cellArray[$cellRow] = array();
					}
					$cellArray[$cellRow][$cellColumn] = $entry->{'content'}->{'$t'};
				}
				// Set default return data
				$this->dataObject[$wsname]["name"] = "unknown";
				$this->dataObject[$wsname]["street"] = "unknown";
				$this->dataObject[$wsname]["postalcode"] = "unknown";
				$this->dataObject[$wsname]["city"] = "unknown";
				$this->dataObject[$wsname]["position"] = array(0=>"unknown", 1=>"unknown");
				$this->dataObject[$wsname]["dates"] = array();
				// Iterate each row of the worksheet
				foreach($cellArray as $row)
				{
					if(isset($row["A"]))
					{
						if(!isset($row["B"])) $row["B"] = "";
						// See which kind of data we can find in this row by looking at their identifiers
						switch($row["A"])
						{
							case "Bezeichnung:": $this->dataObject[$wsname]["name"] = $row["B"]; break;
							case "Stra&szlig;e:": $this->dataObject[$wsname]["street"] = $row["B"]; break;
							case "PLZ:": $this->dataObject[$wsname]["postalcode"] = $row["B"]; break;
							case "Ort:": $this->dataObject[$wsname]["city"] = $row["B"]; break;
							case "Lage:": $this->dataObject[$wsname]["position"] = preg_split("~,\s*~",$row["B"]); break;
							default:
								// If none of the static identifiers are found, check if the identifier is a date
								if(preg_match("~^1?\d/[1-3]?\d/\d{4}~", $row["A"]) == 1) $this->dataObject[$wsname]["dates"][$row["A"]] = $row["B"];
								break;
						}
					}
				}
				file_put_contents($cachefilename, json_encode($this->dataObject[$wsname]));
			}
		}

		public function GetData() 
		{
			return $this->dataObject;
		}

		/* Thanks to http://stackoverflow.com/questions/2236668/file-get-contents-breaks-up-utf-8-characters */
		private function _file_get_contents_utf8($fn) {
			$content = file_get_contents($fn);
			return mb_convert_encoding($content, "HTML-ENTITIES", "UTF-8");
		}
	}
?>