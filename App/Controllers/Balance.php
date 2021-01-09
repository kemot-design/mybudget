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
     * Show  balance  (current month as default)
     *
     * @return void
     */
   public function showAction()
   {
             
       $balancePeriod = isset($_POST['balance_period']) ? $_POST['balance_period'] : 1;
       
       $balanceHeader = $this->setBalanceHeader($balancePeriod);
        
       $incomeSumsByCategory = $this->getIncomesByCategory($balancePeriod);

       $expenseSumsByCategory = $this->getExpensesByCategory($balancePeriod);
       
       $userExpenses = $this->getAllUserExpenses($balancePeriod);
       
       $userIncomes = $this->getAllUserIncomes($balancePeriod);
       
       $userId = $this->user->id;
       
        $userDefinedIncomeCategories = Incomes::getUserAssignedCategories($userId); 
        $userDefinedExpenseCategories = Expenses::getUserAssignedCategories($userId);
        $userDefinedPaymentMethods = Expenses::getUserDefinedPaymentMethods($userId);       
           
       View::renderTemplate('Balance/show.html', [
           'incomeSumsByCategory' => $incomeSumsByCategory,
           'expenseSumsByCategory' => $expenseSumsByCategory,
           'header' => $balanceHeader,
           'expenses' => $userExpenses,
           'incomes' => $userIncomes,
           'incomeCategories' => $userDefinedIncomeCategories,
           'paymentMethods' => $userDefinedPaymentMethods,
           'expenseCategories' => $userDefinedExpenseCategories
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
        
            Flash::addMessage('Proszę wybrać datę początkową i końcową bilansu', Flash::WARNING);
            
            return false;  
            
        } else if ($endDate < $startDate) {
        
            Flash::addMessage('Data końcowa nie może być wcześniej niż początkowa', Flash::WARNING);
            
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
               $balanceHeader = 'bieżący miesiąc';
            break;

           case 2:
               $balanceHeader = 'poprzedni miesiąc';
            break;

           case 3:
               $balanceHeader = 'bieżący rok';
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
    
    /**
    * Get all user's expenses from selected period from data base
    *
    * @param int $balancePeriod number that indicate balance dates range
    *
    * @return mixed array or redirect to balance/show 
    */
    private function getAllUserExpenses($balancePeriod)
    {
         
        if ($balancePeriod != 4) {
            
            $userExpenses = Expenses::getExpensesFromSelectedPeriod($this->user->id, $balancePeriod);

        } else if ($this->isDataRangeValid($_POST['balance_start_date'], $_POST['balance_end_date'])) {
            
            $userExpenses = Expenses::getExpensesFromSelectedPeriod($this->user->id, $balancePeriod, $_POST['balance_start_date'], $_POST['balance_end_date']);

        } else {

           $this->redirect('/balance/show');
           exit;
        }

        return $userExpenses;
    }   
    
    /**
    * Get all user's incomes from selected period from data base
    *
    * @param int $balancePeriod number that indicate balance dates range
    *
    * @return mixed array or redirect to balance/show 
    */
    private function getAllUserIncomes($balancePeriod)
    {
         
        if ($balancePeriod != 4) {
            
            $userIncomes = Incomes::getIncomesFromSelectedPeriod($this->user->id, $balancePeriod);

        } else if ($this->isDataRangeValid($_POST['balance_start_date'], $_POST['balance_end_date'])) {
            
            $userIncomes = Incomes::getIncomesFromSelectedPeriod($this->user->id, $balancePeriod, $_POST['balance_start_date'], $_POST['balance_end_date']);

        } else {

           $this->redirect('/balance/show');
           exit;
        }

        return $userIncomes;
    }      

}
