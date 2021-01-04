<?php

namespace App\Models;

use PDO;

/**
 * Expenses model
 *
 * PHP version 7.0
 */
class Expenses extends \Core\Model
{
    
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];
    
    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
    
    /**
     * Find user assigned expenses categories
     *
     * @param string $userId usser Id
     *
     * @return assoc table of expense categoires
     */
    public static function getUserAssignedCategories($userId)
    {
        
        $sql = 'SELECT id, name, monthly_limit FROM             expenses_category_assigned_to_users
                WHERE user_id = :id';
        
        $db = static::getDB();
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        $stmt->execute();
        
        $expenseCategories = [];
        
        while($resultRow = $stmt->fetch()){
            $expenseCategories[] = $resultRow;
        }
        
        return $expenseCategories;
    }
          
      /**
     * Find user assigned payment methods
     *
     * @param string $userId usser Id
     *
     * @return assoc table of payment methods
     */
    public static function getUserDefinedPaymentMethods($userId)
    {
        
        $sql = 'SELECT id, name FROM payment_methods_assigned_to_users
                WHERE user_id = :id';
        
        $db = static::getDB();
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        $stmt->execute();
        
        $paymentMethods = [];
        
        while($resultRow = $stmt->fetch()){
            $paymentMethods[] = $resultRow;
        }
        
        return $paymentMethods;
    }      
    
    /**
     * Save the Expenses model with the current property values
     *
     * @return boolean  True if the expense was saved, false otherwise
     */       
    public function save($userId)
    {
        $this->validate();

        if (empty($this->errors)) {

            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
                    VALUES (:id, :category_id, :payment_meth_id, :amount, :date, :comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':category_id', $this->expense_category, PDO::PARAM_INT);
            $stmt->bindValue(':payment_meth_id', $this->payment_method, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date_of_expense, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->expense_comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        if ($this->amount == 0) {
            $this->errors[] = 'Amount is required';
        } else if ($this->amount < 0) {
            $this->errors[] = 'Amount cannot be less than 0';
        } else if ( $this->amount > 999999.99) {
            $this->errors[] = 'Amount cannot be greater than 999 999.99';
        }
        
        $today = date('Y-m-d');

        if ($this->date_of_expense < "2000-01-01" || $this->date_of_expense == 0 || $this->date_of_expense > $today) {
            $this->errors[] = 'Select correct date'; 
        }

        if (!isset($this->payment_method)) {
            $this->errors[] = 'Payment method is required';
        } else if ($this->payment_method == '0') {
            $this->errors[] = 'Payment method is required';
        }
        
        if (!isset($this->expense_category)) {
            $this->errors[] = 'Category is required';
        } else if ($this->expense_category == '0') {
            $this->errors[] = 'Category is required';
        }
        
        if(strlen($this->expense_comment) > 100){
            $this->errors[] = 'Comment cannot be longer than 100 chars';
        }   
    }
    
    /**
     * Get the sums of expenses grouped by category of a specyfic user
     *
     * @return associaive array category name as key, sum as value
     */
    public static function getSumsGroupedByCategory($userId, $balancePeriod = 1, $startDate = NULL, $endDate = NULL)
    {
        
        switch($balancePeriod){

            case 1:
                $date_query = " date_of_expense >= LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 1 MONTH AND date_of_expense < LAST_DAY(CURDATE()) + INTERVAL 1 DAY ";
                break;

            case 2:
                $date_query = " date_of_expense >= (LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 2 MONTH) AND date_of_expense < (LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 1 MONTH) ";
                break;

            case 3:
                $date_query = " YEAR(date_of_expense) = YEAR(CURDATE()) ";
                break;
                
            case 4:
                $date_query = " date_of_expense >= :startDate AND date_of_expense <= :endDate ";
                break;    
        }
        
        $sql = "SELECT name, SUM(amount) AS categorySum FROM expenses_category_assigned_to_users, expenses WHERE expenses.user_id = :user_id AND " . $date_query . " AND expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id GROUP BY name ORDER BY categorySum DESC";
        
        $db = static::getDB();
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        
        if ($balancePeriod == 4) {
            $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
            $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);   
        }
        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        $stmt->execute();
        
        $expenseSums = [];
        
        while($resultRow = $stmt->fetch()){
            $expenseSums[] = $resultRow;
        }
        
        return $expenseSums;

    }
    
    public static function isNewCategoryNameUnique($newName, $userId)
    {
        $db = static::getDB();
        
        $sql = "SELECT * FROM expenses_category_assigned_to_users
                WHERE user_id = :userId AND name = :newCategoryName";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":newCategoryName", $newName, PDO::PARAM_STR);
        
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return false;
        } else {
            return true;
        }
    }
    
    public static function editExpenseCategory($categoryToEdit) 
    {
        if($categoryToEdit['newCtgName'] != $categoryToEdit['oldCtgName']) {
            $userId = $categoryToEdit['userId'];
            $newName = $categoryToEdit['newCtgName'];
            
            if(Expenses::isNewCategoryNameUnique($newName, $userId) == false) {
                return "Name uniqness fail";
            }
        }
        
        $db = static::getDB();
        
        $sql = "UPDATE expenses_category_assigned_to_users
                SET name = :newName, monthly_limit = :newLimit
                WHERE user_id = :userId AND id = :ctgId";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":newName", $categoryToEdit['newCtgName'], PDO::PARAM_STR);
        $stmt->bindValue(":newLimit", $categoryToEdit['newLimit'], PDO::PARAM_STR);
        $stmt->bindValue(":userId", $categoryToEdit['userId'], PDO::PARAM_INT);
        $stmt->bindValue(":ctgId", $categoryToEdit['ctgId'], PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return "Ok";
        } else {
            return "DB fail";
        }
        
    }
    
    public static function deleteExpenseCategory($userId, $ctgId) 
    {
        
        $db = static::getDB();
        
        $sql = "DELETE FROM expenses_category_assigned_to_users
                WHERE user_id= :userId AND id = :ctgId";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":ctgId", $ctgId, PDO::PARAM_INT);
        
        return $stmt->execute();
        
    }
    
    public static function addExpenseCategory($userId, $ctgName, $ctgLimit)
    {
        $db = static::getDB();
        
        $sql = "INSERT INTO expenses_category_assigned_to_users (user_id, name, monthly_limit)
                VALUES(:userId, :ctgName, :ctgLimit)";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":ctgName", $ctgName, PDO::PARAM_STR);
        $stmt->bindValue(":ctgLimit", $ctgLimit, PDO::PARAM_STR);
        
        if($stmt->execute()) {
            
            $sql = "SELECT id FROM expenses_category_assigned_to_users
                    WHERE user_id = :userId AND name = :ctgName";
            
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
            $stmt->bindValue(":ctgName", $ctgName, PDO::PARAM_STR);
            
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            
            $result = $stmt->fetch();
            $newCtgId = $result['id'];
            
            return $newCtgId;
        }
    }
    

    
}