<?php

class exportToCSVComponent extends sfComponent {
	public function execute($request){
		$usrObj = $this->getUser()->getAttribute('user');
        $isAdmin = $usrObj->isAdmin();
		if(!$isAdmin){
			return sfView::NONE;
		}
	}
}