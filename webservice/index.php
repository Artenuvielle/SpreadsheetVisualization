<?php
		/**
		 * @copyright	Copyright (C) René Martin, 2012. All rights reserved.
		 * @license		GNU General Public License version 2 or later; see LICENSE.txt
		 **/

	require_once("source/SpreadsheetAdapter.php");
	// Are details on worksheet requested?
	if(isset($_GET["dow"]) && $_GET["dow"] != "") {
		$spreadsheetAdapter = new ssAdapter(explode("+",$_GET["dow"]));
	} else {
		// A list of all flats is requested
		$spreadsheetAdapter = new ssAdapter();
	}

	// Preparing data for requested output
	$output = array();
	$objarr = array();
	if (!isset($_GET["date"]) ||  $_GET["date"] == "") {
		$fromdate = date("n/j/Y");
	} else {
		$fromdate = $_GET["date"];
	}
	if (!isset($_GET["todate"]) ||  $_GET["todate"] == "") {
		$todate = $fromdate;
	} else {
		$todate = $_GET["todate"];
	}

	// Convert encoding to fit the spreadsheet data
	if(isset($_GET["city"]) && $_GET["city"] != "") {
		$_GET["city"] = mb_convert_encoding($_GET["city"], "HTML-ENTITIES", "UTF-8");
	}

	// Convert the read spreadsheet data into an array which is requested
	foreach ($spreadsheetAdapter->GetData() as $ws => $wsinfo) {
		$objarr[$ws] = array(
			"name" => $wsinfo["name"],
			"position" => $wsinfo["position"],
			"fitsfilter" => "1");
		if(isset($_GET["detail"]) && $_GET["detail"] != "") {
			$objarr[$ws]["adress"] = $wsinfo["street"]." ".$wsinfo["postalcode"]." ".$wsinfo["city"];
			$objarr[$ws]["dates"] = array();
		}
		if(isset($_GET["city"]) && $_GET["city"] != "") {
			if((strtolower($_GET["city"]) != strtolower($wsinfo["city"])) && (strtolower($_GET["city"]) != strtolower($wsinfo["postalcode"]))) {
				// The worksheet does not fit if neither the city nor the postal code equals the given string
				$objarr[$ws]["fitsfilter"] = "0";
			}
		}
		$hasFreeDate = 0;
		if (!isset($_GET["date"]) ||  $_GET["date"] == "") {
			$hasFreeDate = 1;
		}
		// Cycle through requested dates and see if there is one free
		if(($fromtime = strtotime($fromdate)) && ($totime = strtotime($todate))) {
			foreach ($wsinfo["dates"] as $date => $occupied) {
				if ($current = strtotime($date)) {
					if($current >= $fromtime && $current <= $totime) {
						if(isset($_GET["detail"]) && $_GET["detail"] != "") {
							$objarr[$ws]["dates"][$date] = $occupied;
						}
						if($occupied == "") {
							$hasFreeDate = 1;
						}
					}
				}
			}
		}
		if($hasFreeDate == 0) {
			$objarr[$ws]["fitsfilter"] = "0";
		}
	}
	$output["obj"] = $objarr;

	// Calculate card zoom
	$minx =  180;
	$maxx = -180;
	$miny = 90;
	$maxy = -90;
	foreach ($objarr as $wsname => $ws) {
		if($ws["fitsfilter"] == "1" || count($objarr) == 1) {
			if($ws["position"][0] < $minx)
				$minx = $ws["position"][0];
			if($ws["position"][0] > $maxx)
				$maxx = $ws["position"][0];
			if($ws["position"][1] < $miny)
				$miny = $ws["position"][1];
			if($ws["position"][1] > $maxy)
				$maxy = $ws["position"][1];
		}
	}

	switch ($minx-$maxx) {
		case 0:
			// only one point fits
			$output["camerazoom"] = array(
				"xmin" => $minx + 0.005,
				"xmax" => $maxx - 0.005,
				"ymin" => $miny + 0.005,
				"ymax" => $maxy - 0.005
			);
			break;
		case 360:
			// no point fits
			// One could possibly use http://wiki.openstreetmap.org/wiki/Nominatim to get the position
			// of the requested city or postal code but there was not enough time to implement this
			//break;
		default:
			$output["camerazoom"] = array(
				"xmin" => $minx + ($minx - $maxx) / 2,
				"xmax" => $maxx - ($minx - $maxx) / 2,
				"ymin" => $miny + ($miny - $maxy) / 2,
				"ymax" => $maxy - ($miny - $maxy) / 2
			);
			break;
	}
	//print_r($output);
	echo json_encode($output);
?>