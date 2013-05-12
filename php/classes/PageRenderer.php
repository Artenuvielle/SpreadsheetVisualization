<?php
    /**
     * @copyright Copyright (C) René Martin, 2012. All rights reserved.
     * @license   GNU General Public License version 2 or later; see LICENSE.txt
     **/

	class PageRenderer {
		// This class is used to render a webpage based on a hardcoded body template.
		// It is great if there should be multiple equally looking sites

		private $title;
		private $issent;
		private $requiredExternElements;
		private $headerString;
		private $topbarString;
		private $contentString;
		private $endString;
		private $modalString;
		
		function  __construct($t = "Google Spreadsheet Access") {
			$this->title = $t;
			$this->issent = false;
			$this->requiredExternElements=array();
			$this->headerString = "";
			$this->topbarString = "";
			$this->contentString = "";
			$this->endString = "";
			$this->modalString = "";
		}
		
		// Function to include extern files or set meta data
		public function requireExternElement($type,$value) {
			$this->requiredExternElements[count($this->requiredExternElements)] = array($type,$value);
		}
		
		// Functions to add content to the template at different positions
		public function addToHeader($stringtoadd) {
			$this->headerString.=$stringtoadd;
		}
		
		public function addToTopbar($stringtoadd) {
			$this->topbarString.=$stringtoadd;
		}
		
		public function addToContent($stringtoadd) {
			$this->contentString.=$stringtoadd;
		}

		public function addToEnd($stringtoadd) {
			$this->contentString.=$stringtoadd;
		}
		
		// Function to add Bootstrap modals to the page
		public function addModalToContent($modalid, $modalname, $modalcontent, $modalbuttons) {
			$this->modalString .= '<div id="'.$modalid.'" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">'.$modalname.'</h3>
      </div>
      <div class="modal-body">'.$modalcontent.'</div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Schließen</button>'.$modalbuttons.'
      </div>
    </div>';
		}
		
		public function renderPage() {
			$rend =  $this->_renderDoctype();
			$rend .= $this->_renderHeader();
			$rend .= $this->_renderBody();
			$rend .= $this->_renderEnd();
			echo $rend;
			$this->issent = true;
		}
		
		// Render the HTML5 doctype
		private function _renderDoctype() {
			return('<!doctype html>
					<html lang="de">');
		}

		private function _renderHeader() {
			$result = "<head>";
			$result.='<meta charset="utf-8">
		<title>'.$this->title.'</title>'.$this->headerString;

			// Include extern scripts, stylesheets and metatags
			foreach ($this->requiredExternElements as $element) {
				if (gettype($element)=='array') {
					switch ($element[0]) {
						// Moved JavaScript Loading to footer due to faster loading time
						//case (ExternRequireTypes::$TYPE_JS): $result.=$this->_createJSLink($element[1]); break;
						case (ExternRequireTypes::$TYPE_CSS): $result.=$this->_createCSSLink($element[1]); break;
						case (ExternRequireTypes::$TYPE_META): $result.=$this->_createMETALink($element[1]); break;
					}
				}
			}
			return $result.'</head>';
		}
		
		private function _createJSLink($source) {
			if(gettype($source)=='string')  {
				return '<script type="text/javascript" src="'.$source.'"></script>';
			} else {
				return '';
			}
		}
		
		private function _createCSSLink($source) {
			if(gettype($source)=='string')  {
				return '<link rel="stylesheet" href="'.$source.'">';
			} else {
				return '';
			}
		}
		
		private function _createMETALink($content) {
			if(gettype($content)=='string')  {
				return '<meta '.$content.'>';
			} else {
				return '';
			}
		}
		
		// Render the content into the given template
		private function _renderBody() {
			$result = '<body><div id="wrap">
			<div class="navbar navbar-inverse navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container-fluid">
						<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="brand" href="#">Google Spreadsheet Access</a>
						<div class="nav-collapse collapse">
							<ul class="nav">';
			$result .= $this->topbarString.'
							</ul>
						</div><!--/.nav-collapse -->
					</div>
				</div>
			</div>
			<div class="container-fluid" style="padding-top:60px">
				<div class="row-fluid">';
			$result .= $this->contentString;
			$result .= '</div><!--/row-->
			</div><!--/.fluid-container-->
		</div>
		<div id="footer">
			<p class="pull-right" style="padding-right:60px">&copy; Ren&eacute; Martin 2013</p>
		</div>';
			$result .= $this->modalString.$this->endString;
			foreach ($this->requiredExternElements as $element) {
				if (gettype($element)=='array' && $element[0] == ExternRequireTypes::$TYPE_JS) {
					$result.=$this->_createJSLink($element[1]);
				}
			}
			$result .= "</body>";
			return $result;
		}
		
		private function _renderEnd() {
			return('</html>');
		}

		// If the object is being destroyed without beeing sent to the client it is finished now
		function __destruct() {
			if(!($this->issent))
				$this->renderPage();
		}
	}
?>