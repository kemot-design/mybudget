<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expenses;

/**
 * Settings controller
 *
 * PHP version 7.0
 */
class Settings extends \Core\Controller
{
    
     /**
     * Require the user to be authenticated before giving access to all methods in the controller
     *
     * @return void
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
     * Show the settings page
     *
     * @return void
     */
    public function showAction()
    {
        $userId = $this->user->id;
        
        $userDefinedPaymentMethods = Expenses::getUserDefinedPaymentMethods($userId);
        $userDefinedCategories = Expenses::getUserAssignedCategories($userId); 
        
        View::renderTemplate('Settings/show.html',[
            'paymentMethods' => $userDefinedPaymentMethods,
            'expenseCategories' => $userDefinedCategories
        ]);
    }
}
