<?php
    /**
     * @copyright Copyright (C) René Martin, 2012. All rights reserved.
     * @license   GNU General Public License version 2 or later; see LICENSE.txt
     **/
  $render->requireExternElement(ExternRequireTypes::$TYPE_JS,"js/openlayers/OpenLayers.js");
  $render->requireExternElement(ExternRequireTypes::$TYPE_JS,"js/maplogic.js");
  $render->requireExternElement(ExternRequireTypes::$TYPE_JS,"js/ajaxlogic.js");
  
  // Feed data to the navigation
  $render->addToTopbar('
                <li class="active"><a href="#">Start</a></li>
                <li><a href="?site=help">Hilfe</a></li>');

  // Set content which shall be rendered.
  // Mainly the sidebar is added here
  $render->addToContent('<div class="span3">
            <div class="well sidebar-nav">
              <ul class="nav nav-list">
                <li class="nav-header">Objekt suchen</li>
                <li>
                  <table>
                    <tr><td>PLZ / Ort: </td><td><input type="text" id="cityinput" tabindex="1" value=""></input></td>
                    <tr><td>Am / Vom: </td><td><input type="date" id="fromdateinput" tabindex="2" value=""></input></td></tr>
                    <tr id="todatetablerow" style="display:none"><td>Bis: </td><td><input type="date" id="todateinput" tabindex="3" value=""></input></td></tr>
                  </table>
                  <button class="btn" id="submitBtn">Anfrage senden</button>
                </li>
                <li class="nav-header" style="width:100%">M&ouml;gliche Wohnungen<i class="pull-right icon-plus-sign" id="openflatlist"></i></li>
                <li>
                  <ul id="possibleflats" class="nav nav-list" style="margin:0 -15px;">
                    
                  </ul>
                </li>
                <li class="nav-header activateOnDetail" style="width:100%">Details<i class="pull-right icon-plus-sign" id="opendetails"></i></li>
                <li>
                  <ul id="alldetails" class="nav nav-list" style="margin:0 -15px;">
                    <li class="nav-header sub activateOnDetail">Adresse:</li>
                    <li class="sub-text activateOnDetail" id="adress"></li>
                    <li class="nav-header sub activateOnDetail">Belegungsstatus:</li>
                    <li class="sub-text activateOnDetail">
                      <ul id="dates" class="nav nav-list" style="margin:0 -15px;">
                        <!-- Dates and if available booking buttons will be listed here -->
                      </ul>
                    </li>
                  </ul>
                </li>
              </ul>
              <!-- ajax loader from http://www.ajaxload.info/ -->
              <div id="loader" align="center" style="display:none"><img src="img/ajax-loader.gif"></div>
            </div><!--/.well -->
          </div><!--/span-->');

  // Render the place for the map.
  $render->addToContent('<div class="span9" id="mapHolder" style="background:grey">
            <div style="height:100%;width:100%;" id="actualMap">
              <!-- here comes the acutal map to work with -->
            </div>
          </div><!--/span-->');

  // Add Bootstrap modals to the site
  $render->addModalToContent("myModal", "Buchung", '<p>Bitte geben sie Ihren Namen an und bestätigen Sie die Buchung.</p>
        <p>Name: <input type="text" id="username"></input></p>
        <p>
          Details:<ul id="modaldetails"></ul>
        </p>', '<button class="btn btn-primary" id="acceptbook">Buchung bestätigen</button>');
  $render->addModalToContent("loginModal", "Login", '<p>Bitte geben sie Ihren Google Nutzernamen und Passwort für den Acount an, welcher Zugang zum gewünschten Spreadsheet hat.</p>
        <p>Nutzername: <input type="text" id="googleusername"></input></p>
        <p>Passwort: <input type="password" id="googlepassword"></input></p>', '<button class="btn btn-primary" id="loginforbook">Login</button>');
  $render->addModalToContent("answerModal", "Buchung", '<p id="message"></p>', '');

  // Thanks to http://webcheatsheet.com/PHP/get_current_page_url.php
  $pageURL = 'http';
  if ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") || $allwaysssl) {$pageURL .= "s";}
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  $pagePath = preg_replace("~^(.*/).*~", "$1", $pageURL);

  // The webservice is suppoesed to be in the "webservice"-subfolder
  // If not: change below!
  $render->addToEnd('<script type="text/javascript">
      // Used for finding the webservice
      var codebase = "'.$pagePath.'webservice/";
    </script>');
?>