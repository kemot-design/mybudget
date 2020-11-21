<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Incomes;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
//class Items extends \Core\Controller
class Income extends Authenticated
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
        
        $incomeCategories = Incomes::getUserAssignedCategories($userId);
        
        View::renderTemplate('Income/new.html',[
            'incomeCategories' => $incomeCategories
        ]);
    }
    
    public function createAction()
    {
        
        var_dump($_POST);
    }


}
