<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 13/9/18
 * Time: 12:58 PM
 */
class purgeCandidateAction extends sfAction {

    /**
     * Execute any application/business logic for this component.
     *
     * In a typical database-driven application, execute() handles application
     * logic itself and then proceeds to create a model instance. Once the model
     * instance is initialized it handles all business logic for the action.
     *
     * A model should represent an entity in your application. This could be a
     * user account, a shopping cart, or even a something as simple as a
     * single product.
     *
     * @param sfRequest $request The current sfRequest object
     *
     * @return mixed     A string containing the view name associated with this action
     */
    function execute($request)
    {
        // TODO: Implement execute() method.
        var_dump("purge candidate working");
    }
}