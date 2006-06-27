<?
class xajaxElementFiller {
	
	function xajaxElementFiller() { 
	}
	
	function cmbFiller ($objResponse,$fillArr,$fele,$form,$element,$defSel=1) {
		$objResponse->addScript("document.".$form.".".$element.".options.length = 0;");

		if($defSel == 1)
	 		$objResponse->addScript("document.".$form.".".$element.".options[0] = new Option('--Select--','0');");
	 		
	 	for($i=0;$fillArr && count($fillArr)>$i;$i++)
	 		$objResponse->addScript("document.".$form.".".$element.".options[".($defSel == 1 ? $i+1 : $i)."] = new Option('" .$fillArr[$i][($fele+1)]. "','".$fillArr[$i][$fele]."');");
		
		return $objResponse;
	}
}
?>