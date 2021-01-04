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
        $expCtgToEdit = array(
            "userId" => $_POST['userId'],
            "ctgId" => $_POST['ctgId'],
            "oldCtgName" => $_POST['oldCtgName'],
            "newCtgName" => $_POST['newCtgName'],
            "newLimit" => $_POST['newCtgLimit']
        );
        
        
        //$expCtgToEdit += ['userId', $_POST['userId']];
        //$$expCtgToEdit += ['ctgId', $_POST['ctgId']];
        //$$expCtgToEdit['oldCtgName'] = $_POST['oldCtgName'];
        //$expCtgToEdit['newCtgName'] = $_POST['newCtgName'];
        //$expCtgToEdit['newLimit'] = $_POST['newCtgLimit'];
        
        $editingResponse = Expenses::editExpenseCategory($expCtgToEdit);
         
        if($editingResponse == "Ok") {
            echo "success";
        } else if($editingResponse == "Name uniqness fail"){
            echo "Taka kategoria już istnieje";
        } else if($editingResponse == "DB fail"){
            echo "Awaria bazy danych, przepraszamy";
        } else {
            echo "Nieznany błąd, przepraszamy";
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
    
    public function deleteUser()
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
    
}


