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
       $balancePeriod = 1; //default balance perion 1 = current month
       if(isset($_POST['balance_period'])){
           $balancePeriod = $_POST['balance_period'];
       }
       
       switch($balancePeriod){
           case 1:
               $balanceHeader = 'bierzący miesiąc';
            break;
               
           case 2:
               $balanceHeader = 'poprzedni miesiąc';
            break;
               
           case 3:
               $balanceHeader = 'bierzący rok';
            break;
               
           default:
               $balanceHeader = 'cos poszlo nie tak';
            break; 
       }
       
       $incomeSumsByCategory = Incomes::getSumsGroupedByCategory($this->user->id, $balancePeriod);
       
       $incomesSum = 0;
       
       foreach  ($incomeSumsByCategory as $sum) {
           $incomesSum += $sum['categorySum'];
       }
       
       //$incomesSum = number_format($incomesSum, 2, '.', ' ');
       
       $expenseSumsByCategory = Expenses::getSumsGroupedByCategory($this->user->id, $balancePeriod);
       
       $expensesSum = 0;
       
       foreach  ($expenseSumsByCategory as $sum) {
           $expensesSum += $sum['categorySum'];
       }
       
      //$expensesSum = number_format($expensesSum, 2, '.', ' ');
           
       View::renderTemplate('Balance/show.html', [
           'incomeSumsByCategory' => $incomeSumsByCategory,
           'expenseSumsByCategory' => $expenseSumsByCategory,
           'totalIncome' => $incomesSum,
           'totalExpense' => $expensesSum,
           'header' => $balanceHeader
       ]);
   
   }
    

}
