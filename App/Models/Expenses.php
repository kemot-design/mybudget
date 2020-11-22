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
        
        $sql = 'SELECT id, name FROM expenses_category_assigned_to_users
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
        //$this->validate();

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

        if (!isset($this->income_category_assigned_to_user_id)) {
            $this->errors[] = 'Category is required';
        } else if ($this->income_category_assigned_to_user_id == '0') {
            $this->errors[] = 'Category is required';
        }
        
        if ($this->amount == 0) {
            $this->errors[] = 'Amount is required';
        } else if ($this->amount < 0) {
            $this->errors[] = 'Amount cannot be less than 0';
        } else if ( $this->amount > 999999.99) {
            $this->errors[] = 'Amount cannot be greater than 999 999.99';
        }
        
        $today = date('Y-m-d');

        if ($this->date_of_income < "2000-01-01" || $this->date_of_income == 0 || $this->date_of_income > $today) {
            $this->errors[] = 'Select correct date'; 
        }
        
        if(strlen($this->income_comment) > 100){
            $this->errors[] = 'Comment cannot be longer than 100 chars';
        }   
    }
    
}