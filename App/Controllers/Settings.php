<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expenses;
use \App\Flash;
use \App\Models\User;

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
        $newLimit = $_POST['newCtgLimit'];
                
        if(Expenses::editExpenseCategory($userId, $ctgId, $newCtgName, $newLimit)) {
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
    
    public function addExpenseCategory()
    {
        $this->user = Auth::getUser();
        $userId = $userId = $this->user->id;
        
        $ctgName = $_POST['name'];
        $ctgLimit = $_POST['limit'];
        
        $newCtgId = Expenses::addExpenseCategory($userId, $ctgName, $ctgLimit);
        
        if($newCtgId > 0) {
            echo $newCtgId;
        } else {
            echo "failure";
        }
        
    }
    
    public function updateUserData()
    {
        $user = Auth::getUser();
    
        $responseFromProfileUpdate = $user->updateProfile($_POST);
        
        if($responseFromProfileUpdate == "no errors") {
            echo "success";
        } else {
            echo json_encode($responseFromProfileUpdate);
        }
    }
    
}


