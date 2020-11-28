<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Incomes;
use \App\Models\Expenses;
use \App\Flash;

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
               
            case 4:
               $balanceHeader = "od " . $_POST['balance_start_date'] . ' do ' . $_POST['balance_end_date'];
            break;
               
           default:
               $balanceHeader = 'cos poszlo nie tak';
            break; 
       }
        
       if($balancePeriod != 4) {
           $incomeSumsByCategory = Incomes::getSumsGroupedByCategory($this->user->id, $balancePeriod);
       } else if ($this->isDataRangeValid($_POST['balance_start_date'], $_POST['balance_end_date'])) {
           $incomeSumsByCategory = Incomes::getSumsGroupedByCategory($this->user->id, $balancePeriod, $_POST['balance_start_date'], $_POST['balance_end_date']);
       } else {
           Flash::addMessage('Wybrano niepoprawne daty, spróbuj ponownie', Flash::WARNING);
           $this->redirect('/balance/show');
       }
       
       $incomesSum = 0;
       
       foreach ($incomeSumsByCategory as $sum) {
           $incomesSum += $sum['categorySum'];
       }
       
       //$incomesSum = number_format($incomesSum, 2, '.', ' ');
       
       if ($balancePeriod != 4) {
           $expenseSumsByCategory = Expenses::getSumsGroupedByCategory($this->user->id, $balancePeriod);
       } else if ($this->isDataRangeValid($_POST['balance_start_date'], $_POST['balance_end_date'])) {
           $expenseSumsByCategory = Expenses::getSumsGroupedByCategory($this->user->id, $balancePeriod, $_POST['balance_start_date'], $_POST['balance_end_date']);
       } else {
           Flash::addMessage('Wybrano niepoprawne daty, spróbuj ponownie', Flash::WARNING);
           $this->redirect('/balance/show');
       }
       
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
    
    public function isDataRangeValid($startDate, $endDate)
    {
        if($startDate == "" || $endDate == "") return false;
        
        else if($endDate < $startDate) return false;
        
        return true;
        
    }
    

}
