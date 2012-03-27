<?php 

class ActionFilter extends sfFilter {

    public function execute($filterChain) {

        $request = $this->getContext()->getRequest();
        if (($request['action'] != 'getDatabaseInfo') && !$this->getContext()->getUser()->isAuthenticated()) {
            $this->getContext()->getController()->redirect('upgrade/getDatabaseInfo');
        }
        
        // Execute next filter in filter chain
        $filterChain->execute();
    }

}