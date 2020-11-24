<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Incomes;
use \App\Models\Expenses;

/**
 * Balance controller
 *
 * PHP version 7.0
 */
//class Balance extends \Core\Controller
class Balance extends Authenticated
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
     * Show default balance view (current month)
     *
     * @return void
     */
   public function showAction()
   {
       
       $incomeSumsByCategory = Incomes::getSumsGroupedByCategory($this->user->id);
       
       $incomesSum = 0;
       
       foreach  ($incomeSumsByCategory as $sum) {
           $incomesSum += $sum['categorySum'];
       }
       
       $incomesSum = number_format($incomesSum, 2, '.', ' ');
           
       View::renderTemplate('Balance/show.html', [
           'categorySums' => $incomeSumsByCategory,
           'totalIncome' => $incomesSum
       ]);
   
   }
    

}
