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
             
       $balancePeriod = isset($_POST['balance_period']) ? $_POST['balance_period'] : 1;
       
       $balanceHeader = $this->setBalanceHeader($balancePeriod);
        
       $incomeSumsByCategory = $this->getIncomesByCategory($balancePeriod);
       
       $incomesSum = 0;
       
       foreach ($incomeSumsByCategory as $sum) {
           $incomesSum += $sum['categorySum'];
       }
       
       //$incomesSum = number_format($incomesSum, 2, '.', ' ');

       $expenseSumsByCategory = $this->getExpensesByCategory($balancePeriod);
       
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
    
    /**
     * Check if selected dates range is valid to balance, adds flash message if data range is invalid
     *
     * @param int $startDate balance start date
     *
     * @param int $endDate balance end date
     *
     * @return boolean
     */
    public function isDataRangeValid($startDate, $endDate)
    {
        if($startDate == "" || $endDate == "") {
        
            Flash::addMessage('PLease select start and end date of balance period', Flash::WARNING);
            
            return false;  
            
        } else if ($endDate < $startDate) {
        
            Flash::addMessage('End date cannot be earlier than start date', Flash::WARNING);
            
            return false;    
        } 
        
        return true;
        
    }
    
    
    /**
     * Set balance header depend on balance dates range)
     *
     * @param int $balancePeriod number that indicate balance dates range
     *
     * @return string
     */
    private function setBalanceHeader($balancePeriod)
    {
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
        
        return $balanceHeader;
    }
    
    /**
    * Get the incomes grouped by category from selected period from data base
    *
    * @param int $balancePeriod number that indicate balance dates range
    *
    * @return mixed arrey or redirect to balance/show 
    */
    private function getIncomesByCategory($balancePeriod)
    {
        
        if ($balancePeriod != 4) {

           $incomeSumsByCategory = Incomes::getSumsGroupedByCategory($this->user->id, $balancePeriod);

        } else if ($this->isDataRangeValid($_POST['balance_start_date'], $_POST['balance_end_date'])) {

           $incomeSumsByCategory = Incomes::getSumsGroupedByCategory($this->user->id, $balancePeriod, $_POST['balance_start_date'], $_POST['balance_end_date']);

        } else {

           $this->redirect('/balance/show');
           exit;
        }

        return $incomeSumsByCategory;
    }
    
    /**
    * Get the expenses grouped by category from selected period from data base
    *
    * @param int $balancePeriod number that indicate balance dates range
    *
    * @return mixed array or redirect to balance/show 
    */
    private function getExpensesByCategory($balancePeriod)
    {
        
        if ($balancePeriod != 4) {

           $expenseSumsByCategory = Expenses::getSumsGroupedByCategory($this->user->id, $balancePeriod);

        } else if ($this->isDataRangeValid($_POST['balance_start_date'], $_POST['balance_end_date'])) {

           $expenseSumsByCategory = Expenses::getSumsGroupedByCategory($this->user->id, $balancePeriod, $_POST['balance_start_date'], $_POST['balance_end_date']);

        } else {

           $this->redirect('/balance/show');
           exit;
        }

        return $expenseSumsByCategory;
    }    

}
