<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expenses;
use \App\Models\Incomes;
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
        $userDefinedIncomeCategories = Incomes::getUserAssignedCategories($userId); 
        
        View::renderTemplate('Settings/show.html',[
            'paymentMethods' => $userDefinedPaymentMethods,
            'expenseCategories' => $userDefinedExpenseCategories,
            'incomeCategories' => $userDefinedIncomeCategories
        ]);
    }
    
    public function editExpenseCategoryAction() 
    {
        $expCtgToEdit = array(
            "userId" => $_POST['userId'],
            "ctgId" => $_POST['ctgId'],
            "oldCtgName" => $_POST['oldCtgName'],
            "newCtgName" => $_POST['newCtgName'],
            "newLimit" => $_POST['newCtgLimit']
        );
        
        $editingResponse = Expenses::editExpenseCategory($expCtgToEdit);
        
        echo $editingResponse;
         
    }
    
    public function deleteExpenseCategoryAction() {
        $userId = $_POST['userId'];
        $ctgId = $_POST['ctgId'];
        
        if(Expenses::deleteExpenseCategory($userId, $ctgId)) {
            echo "success";
        } else {
            echo "failure";
        }
    }
    
    public function addExpenseCategoryAction()
    {
        $this->user = Auth::getUser();
        $userId = $this->user->id;
        
        $ctgName = $_POST['name'];
        $ctgLimit = $_POST['limit'] ?? 0;
        
        $addNewCategoryResponse = Expenses::addExpenseCategory($userId, $ctgName, $ctgLimit);
        
        if($addNewCategoryResponse == "success") {
            echo Expenses::getNewCategoryId($ctgName, $userId);
        } else {
            echo $addNewCategoryResponse;
        }
        
    }
    
    public function updateUserDataAction()
    {
        $user = Auth::getUser();
    
        $responseFromProfileUpdate = $user->updateProfile($_POST);
        
        if($responseFromProfileUpdate == "no errors") {
            echo "success";
        } else {
            echo json_encode($responseFromProfileUpdate);
        }
    }
    
    public function deleteUserAction()
    {
        $user = Auth::getUser();
        $user->deleteUser();
        
        Auth::logout();
        $this->redirect('/settings/show-user-delete-message');    
    }
    
    public function showUserDeleteMessageAction()
    {

        Flash::addMessage('Konto zostało usunięte');

        $this->redirect('/');
    }
    
    public function editIncomeCategoryAction() 
    {
        $incCtgToEdit = array(
            "userId" => $_POST['userId'],
            "ctgId" => $_POST['ctgId'],
            "oldCtgName" => $_POST['oldCtgName'],
            "newCtgName" => $_POST['newCtgName']
        );
        
        $editingResponse = Incomes::editCategory($incCtgToEdit);
        
        echo $editingResponse;
         
    }    
    
    public function deleteIncomeCategoryAction() {
        $userId = $_POST['userId'];
        $ctgId = $_POST['ctgId'];
        
        if(Incomes::deleteCategory($userId, $ctgId)) {
            echo "success";
        } else {
            echo "failure";
        }
    }    
    
    public function addIncomeCategoryAction()
    {
        $this->user = Auth::getUser();
        $userId = $this->user->id;
        
        $ctgName = $_POST['name'];
        
        $addNewCategoryResponse = Incomes::addNewCategory($userId, $ctgName);
        
        if($addNewCategoryResponse == "success") {
            echo Incomes::getNewCategoryId($ctgName, $userId);
        } else {
            echo $addNewCategoryResponse;
        }
        
    }    
    
}


