<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expenses;
use \App\Flash;

/**
 * Expense controller (example)
 *
 * PHP version 7.0
 */
//class Items extends \Core\Controller
class Expense extends Authenticated
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
     * Show add new income form
     *
     * @return void
     */
    public function newAction()
    {
        $userId = $this->user->id;
        
        $userDefinedPaymentMethods = Expenses::getUserDefinedPaymentMethods($userId);
        $userDefinedCategories = Expenses::getUserAssignedCategories($userId);
        
        View::renderTemplate('Expense/new.html',[
            'paymentMethods' => $userDefinedPaymentMethods,
            'expenseCategories' => $userDefinedCategories
        ]);
    }
    
    public function createAction()
    {
        $expense = new Expenses($_POST);
        
        if ($expense->save($this->user->id)) {
            
            Flash::addMessage('Wydatek dodany');
            
            $this->redirect('/Expense/new');
            
        } else {
            Flash::addMessage('Niepoprawne dane, popraw formularz', Flash::WARNING);
            
            View::renderTemplate('Expense/new.html',[
                'expense' => $expense,
                'paymentMethods' => Expenses::getUserDefinedPaymentMethods($this->user->id),
                'expenseCategories' => Expenses::getUserAssignedCategories($this->user->id)
            ]);
        }
    }

    public function checkCategoryLimitAction()
    {
        $userId = $this->user->id;
        
        $categoryLimit = Expenses::getCategoryLimit($_POST['categoryId'], $userId);
        
        if(is_numeric($categoryLimit)) {
            if($categoryLimit == 0) {
                echo "No limit";
            } else {
                echo $categoryLimit;
            }
        } else {
            echo "Serwer error";
        }
    }
    
    public function getCurrentMonthCategoryExpensesAction()
    {
        $userId = $this->user->id;
        $categoryId = $_POST['categoryId'];
        
        $expensesSum = Expenses::getCurrentMonthExpensesSum($userId, $categoryId);
        
        //$expensesSum = ($expensesSum == NULL ? 0 : $expensesSum);
        
        echo $expensesSum;
    }

}
