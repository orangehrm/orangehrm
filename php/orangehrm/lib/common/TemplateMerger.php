<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

require_once ROOT_PATH . '/lib/common/Language.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

class TemplateMerger {

	private $templatePath;
	private $templateHeader;
	private $templateFooter;
	private $obj;
	private $error;

	public function __construct($obj, $templatePath, $templateHeader='header.php', $templateFooter='footer.php') {

		$baseDir = pathinfo($templatePath, PATHINFO_DIRNAME);

		$this->setObj($obj);
		$this->setTemplatePath($templatePath);

        if (!empty($templateHeader)) {
            $this->setTemplateHeader($baseDir."/".$templateHeader);
        }
        if (!empty($templateFooter)) {
            $this->setTemplateFooter($baseDir."/".$templateFooter);
        }

	}

	public function setTemplatePath($path) {
		$this->templatePath = $path;
	}

	public function getTemplatePath() {
		return $this->templatePath;
	}

	public function setTemplateHeader($path) {
		$this->templateHeader = $path;
	}

	public function getTemplateHeader() {
		return $this->templateHeader;
	}

	public function setTemplateFooter($path) {
		$this->templateFooter = $path;
	}

	public function getTemplateFooter() {
		return $this->templateFooter;
	}

	public function setObj($obj) {
		$this->obj = $obj;
	}

	public function getObj() {
		return $this->obj;
	}

	public function setError($error) {
		$this->error = $error;
	}

	public function getError() {
		return $this->error;
	}

	public function display($modifier=null) {
		@ob_clean();
		require_once ROOT_PATH . '/lib/common/xajax/xajax.inc.php';
		require_once ROOT_PATH . '/lib/common/xajax/xajaxElementFiller.php';
		require_once ROOT_PATH . '/language/default/lang_default_full.php';
                require_once ROOT_PATH . '/plugins/fpdf2/html2pdf.php';

                $printPdf = (isset($_GET['printPdf']) && $_GET['printPdf'] == 1);

                if ($printPdf) {
                        ob_start();
                }

		$lan = new Language();
		require_once($lan->getLangPath("full.php"));

		$records = $this->getObj();

		if (isset($this->error)) {
		    $errorFlag = true;
		}

		$styleSheet = CommonFunctions::getTheme();

        if (!empty($this->templateHeader)) {
            require_once ROOT_PATH.$this->getTemplateHeader();
        }
		require_once ROOT_PATH.$this->getTemplatePath();
        if (!empty($this->templateFooter)) {
            require_once ROOT_PATH.$this->getTemplateFooter();
        }

                if ($printPdf) {
                        $html = ob_get_clean();

                        $pdf = new PDF();
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', '', 10);

                        if(ini_get('magic_quotes_gpc') == '1')
                                $html = stripslashes($html);

                        $pdf->WriteHTML($html);
                        $pdf->Output($_GET['pdfName'] . '.pdf', 'F');

						// TODO: Replace this with xhtml
						echo '<html>' .
								'<head>' .
								'<link href="../../themes/' . $_SESSION['styleSheet'] . '/css/style.css" type="text/css" rel="stylesheet" />' .
								'<style type="text/css">' .
								'body { margin: 10px; }' .
								'</style>' .
								'</head>' .
								'<body>';
                        echo '<input type="button" class="backbutton" value="' . $lang_Common_Back . '"' .
                        		' onclick="history.back();" ' .
                        		'onmouseover="moverButton(this)" onmouseout="moutButton(this)"  />';
                        echo '</body></html>';

                        echo "<script>window.open('".$_SESSION['WPATH']."/lib/controllers/{$_GET['pdfName']}.pdf');</script>";
                }

	}
}
?>
