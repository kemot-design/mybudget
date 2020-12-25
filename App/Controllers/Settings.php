<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expenses;
use \App\Flash;

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
        $userDefinedExpenseCategories = Expenses::getUserAssignedCategories($userId); 
        
        View::renderTemplate('Settings/show.html',[
            'paymentMethods' => $userDefinedPaymentMethods,
            'expenseCategories' => $userDefinedExpenseCategories
        ]);
    }
    
    public function editExpenseCategory() 
    {
        $userId = $_POST['userId'];
        $ctgId = $_POST['ctgId'];
        $newCtgName = $_POST['newCtgName'];
                
        if(Expenses::editExpenseCategory($userId, $ctgId, $newCtgName)) {
            echo "success";
        } else {
            echo "failure";
        }   
    }
    
    public function deleteExpenseCategory() {
        $userId = $_POST['userId'];
        $ctgId = $_POST['ctgId'];
        
        if(Expenses::deleteExpenseCategory($userId, $ctgId)) {
            echo "success";
        } else {
            echo "failure";
        }
    }
    
    public function changeExpenseCategoryLimit()
    {
        $userId = $_POST['userId'];
        $ctgId = $_POST['ctgId'];
        $newLimit = $_POST['ctgLimit'];
        
        if(Expenses::changeExpenseCategoryLimit($userId, $ctgId, $newLimit)) {
            echo "success";
        } else {
            echo "failure";
        }
    }
}


