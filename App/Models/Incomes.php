<?php

namespace App\Models;

use PDO;

/**
 * Income model
 *
 * PHP version 7.0
 */
class Incomes extends \Core\Model
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
     * Find user assigned income categories
     *
     * @param string $userId usser Id
     *
     * @return assoc table of income categoires
     */
    public static function getUserAssignedCategories($userId)
    {
        
        $sql = 'SELECT id, name FROM incomes_category_assigned_to_users
                WHERE user_id = :id';
        
        $db = static::getDB();
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        $stmt->execute();
        
        $incomeCategories = [];
        
        while($resultRow = $stmt->fetch()){
            $incomeCategories[] = $resultRow;
        }
        
        return $incomeCategories;
    }
    
    /**
     * Save the Incomes model with the current property values
     *
     * @return boolean  True if the income was saved, false otherwise
     */       
    public function save($userId)
    {
        $this->validate();

        if (empty($this->errors)) {

            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
                    VALUES (:id, :category_id, :amount, :date, :comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':category_id', $this->income_category_assigned_to_user_id, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date_of_income, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->income_comment, PDO::PARAM_STR);

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

        if ($this->income_category_assigned_to_user_id == '0') {
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