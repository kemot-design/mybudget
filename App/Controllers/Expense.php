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
            
            Flash::addMessage('Expense added');
            $this->redirect('/Expense/new');
            
        } else {
            Flash::addMessage('Incorrect data, please correct the form', Flash::WARNING);
            View::renderTemplate('Income/new.html',[
                'expense' => $expense,
                'expenseCategories' => Expenses::getUserAssignedCategories($this->user->id)
            ]);
        }
    }


}
