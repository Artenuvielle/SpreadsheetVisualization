<?php
    /**
     * @copyright Copyright (C) René Martin, 2012. All rights reserved.
     * @license   GNU General Public License version 2 or later; see LICENSE.txt
     **/
  // Feed data to the navigation
  $render->addToTopbar('
                <li><a href="?site=start">Start</a></li>
                <li class="active"><a href="#">Hilfe</a></li>');

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
                    <tr><td>Bis: </td><td><input type="date" id="todateinput" tabindex="3" value=""></input></td></tr>
                  </table>
                  <button class="btn" id="submitBtn">Anfrage senden</button>
                </li>
                <li class="nav-header" style="width:100%">M&ouml;gliche Wohnungen<i class="pull-right icon-plus-sign" id="openflatlist"></i></li>
                <li>
                  <ul id="possibleflats" class="nav nav-list" style="margin:0 -15px;">
                    <li class="disabled ">
    <a href="#" name="od6">3-Zimmer-Etagenwohnung mit Spreeblick</a>
  </li>
  <li class="disabled ">
    <a href="#" name="od7">1-Zimmer-Wohnung mit guter Verkehrsanbindung</a>
  </li>
  <li class="disabled ">
    <a href="#" name="od4">Ferienwohnung für acht Personen</a>
  </li>
  <li class="">
    <a href="#" name="od5">Solide Wohnung am Ostkreuz</a>
  </li>
  <li class="">
    <a href="#" name="oda">Loft am Kanal</a>
  </li>
  <li class="">
    <a href="#" name="odb">Wohnen mit großer Grünfläche</a>
  </li>
  <li class="disabled ">
    <a href="#" name="od8">Appartement mit internationalem Flair</a>
  </li>
  <li class="">
    <a href="#" name="od9">Shopping im Zentrum der Stadt</a>
  </li>
  <li class="">
    <a href="#" name="ocy">Wohnen im kulturellen Zentrum</a>
  </li>
  <li class="">
    <a href="#" name="ocz">Einbettzimmer im sicherer Lage</a>
  </li>
  <li class="disabled ">
    <a href="#" name="ocw">3-Zimmer-Wohnung mit guter Verkehrsanbindung</a>
  </li>
  <li class="">
    <a href="#" name="ocx">2-Zimmer-Wohnung mit guter Verkehrsanbindung</a>
  </li>
  <li class="">
    <a href="#" name="od2">1-Zimmer-Wohnung in beliebtem Wohnviertel</a>
  </li>
  <li class="">
    <a href="#" name="od3">Ruhige Lage am Kanal</a>
  </li>
  <li class="disabled ">
    <a href="#" name="od0">Geräumige Dachgeschosswohnung</a>
  </li>
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
                        
                      </ul>
                    </li>
                  </ul>
                </li>
              </ul>
              <!-- ajax loader from http://www.ajaxload.info/ -->
              <div id="loader" align="center" style="display:none"><img src="img/ajax-loader.gif"></div>
            </div><!--/.well -->
          </div><!--/span--> IMPEMENP HELP HERE');

  // Render the explanation.
  /*$render->addToContent('<div class="span9" id="mapHolder" style="background:grey">
            <div style="height:100%;width:100%;" id="actualMap">
              <!-- here comes the acutal map to work with -->
            </div>
          </div><!--/span-->');*/
?>