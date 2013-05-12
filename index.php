<?php
    /**
     * @copyright Copyright (C) René Martin, 2012. All rights reserved.
     * @license   GNU General Public License version 2 or later; see LICENSE.txt
     **/

  require_once("php/classes/PageRenderer.php");
  require_once("php/classes/ExternRequireTypes.php");

  // This site requires ssl due to user data send with js.
  if($_SERVER["HTTPS"] != "on")
  {
      header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
      exit();
  }

  // Initializing class which renders the whole site
  $render = new PageRenderer();

  // Setting up references to external sources
  $render->requireExternElement(ExternRequireTypes::$TYPE_META,"name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"");
  $render->requireExternElement(ExternRequireTypes::$TYPE_META,"name=\"description\" content=\"Webseite um Daten von Google Spreadsheet zu visualisieren.\"");
  $render->requireExternElement(ExternRequireTypes::$TYPE_META,"name=\"author\" content=\"Rene Martin\"");
  $render->requireExternElement(ExternRequireTypes::$TYPE_CSS,"css/bootstrap.min.css");
  $render->requireExternElement(ExternRequireTypes::$TYPE_CSS,"css/home.css");
  $render->requireExternElement(ExternRequireTypes::$TYPE_CSS,"css/bootstrap-responsive.min.css");
  $render->requireExternElement(ExternRequireTypes::$TYPE_JS,"js/jquery.js");
  $render->requireExternElement(ExternRequireTypes::$TYPE_JS,"js/bootstrap.min.js");

  // Include HTML5 shiv to enable some objects in IE
  $render->addToHeader('<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->');

  if (!isset($_GET["site"]) || $_GET["site"] == "") {
    $_GET["site"] = "start";
  }

  if (file_exists("php/sites/".$_GET["site"].".php")) {
    include("php/sites/".$_GET["site"].".php");
  } else {
    $render->addToContent("Seite nicht gefunden");
  }

  // Finish up all the Rendering
  $render->renderPage();
?>