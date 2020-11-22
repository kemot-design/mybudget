<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Incomes;
use \App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
//class Items extends \Core\Controller
class Income extends Authenticated
{

    public $userDefinedCategories = [];
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
        
        $userDefinedCategories = Incomes::getUserAssignedCategories($userId);
        
        View::renderTemplate('Income/new.html',[
            'incomeCategories' => $userDefinedCategories
        ]);
    }
    
    public function createAction()
    {
        $income = new Incomes($_POST);
        
        if ($income->save($this->user->id)) {
            
            Flash::addMessage('Income added');
            $this->redirect('/Income/new');
            
        } else {
            Flash::addMessage('Incorrect data, please correct the form', Flash::WARNING);
            View::renderTemplate('Income/new.html',[
                'income' => $income,
                'incomeCategories' => Incomes::getUserAssignedCategories($this->user->id)
            ]);
        }
    }


}
