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
     * Edit income with values sent by Income object
     *
     * @return boolean  True if the income was edited, false otherwise
     */       
    public function edit()
    {
        $this->validate();

        if (empty($this->errors)) {
            
            $sql = 'UPDATE incomes
                    SET income_category_assigned_to_user_id = :category, amount = :amount, date_of_income = :date, income_comment = :comment
                    WHERE id = :categoryId';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':category', $this->income_category_assigned_to_user_id, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date_of_income, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->income_comment, PDO::PARAM_STR);
            $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);

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
        
        //$today = date('Y-m-d');
        $today = date_create(date('Y-m-d'));
        
        $todayPlusOneMonth = date_add($today, date_interval_create_from_date_string("1 month"));
        
        $todayPlusOneMonth = date_format($todayPlusOneMonth, 'Y-m-d');

        if ($this->date_of_income < "2000-01-01" || $this->date_of_income == 0 || $this->date_of_income > $todayPlusOneMonth) {
            $this->errors[] = 'Select correct date'; 
        }
        
        if(strlen($this->income_comment) > 100){
            $this->errors[] = 'Comment cannot be longer than 100 chars';
        }   
    }
    
    /**
     * Get the sums of incomes grouped by category of a specyfic user
     *
     * @return associaive array category name as key, sum as value
     */
    public static function getSumsGroupedByCategory($userId, $balancePeriod = 1, $startDate = NULL, $endDate = NULL)
    {
        
        switch($balancePeriod){

            case 1:
                $date_query = " date_of_income >= LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 1 MONTH AND date_of_income < LAST_DAY(CURDATE()) + INTERVAL 1 DAY ";
                break;

            case 2:
                $date_query = " date_of_income >= (LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 2 MONTH) AND date_of_income < (LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 1 MONTH) "; 
                break;

            case 3:
                $date_query = " YEAR(date_of_income) = YEAR(CURDATE()) ";
                break;
                
            case 4:
                $date_query = " date_of_income >= :startDate AND date_of_income <= :endDate ";
                break;        
        }
        
        $sql = "SELECT incomes_category_assigned_to_users.name, SUM(incomes.amount) AS categorySum FROM incomes_category_assigned_to_users, incomes WHERE incomes.user_id = :user_id AND " . $date_query . " AND incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id GROUP BY incomes_category_assigned_to_users.name ORDER BY categorySum DESC";
        
        $db = static::getDB();
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        
        if ($balancePeriod == 4) {
            
            $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
            $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);   
        }

        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        $stmt->execute();
        
        $incomeSums = [];
        
        while($resultRow = $stmt->fetch()){
            $incomeSums[] = $resultRow;
        }
        
        return $incomeSums;

    }
    
    /**
     * Get the incomes of a specyfic user from selected period
     *
     * @return associaive array
     */
    public static function getIncomesFromSelectedPeriod($userId, $balancePeriod = 1, $startDate = NULL, $endDate = NULL)
    {
        
        switch($balancePeriod){

            case 1:
                $date_query = " date_of_income >= LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 1 MONTH AND date_of_income < LAST_DAY(CURDATE()) + INTERVAL 1 DAY ";
                break;

            case 2:
                $date_query = " date_of_income >= (LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 2 MONTH) AND date_of_income < (LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 1 MONTH) ";
                break;

            case 3:
                $date_query = " YEAR(date_of_income) = YEAR(CURDATE()) ";
                break;
                
            case 4:
                $date_query = " date_of_income >= :startDate AND date_of_income <= :endDate ";
                break;    
        }
        
        $sql = "SELECT incomes.id, icatu.name AS category, icatu.id AS categoryId, incomes.amount, incomes.date_of_income, incomes.income_comment FROM incomes_category_assigned_to_users AS icatu, incomes WHERE incomes.user_id = :user_id AND incomes.income_category_assigned_to_user_id = icatu.id AND " . $date_query . " ORDER BY date_of_income DESC";
        
        $db = static::getDB();
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        
        if ($balancePeriod == 4) {
            $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
            $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);   
        }
        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        $stmt->execute();
        
        $userIncomes = [];
        
        while($resultRow = $stmt->fetch()){
            $userIncomes[] = $resultRow;
        }
        
        return $userIncomes;

    }    
    
    public static function isCategoryNameUnique($newName, $userId)
    {
        $db = static::getDB();
        
        $sql = "SELECT * FROM incomes_category_assigned_to_users
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
    
    public static function editCategory($categoryToEdit) 
    {
        if($categoryToEdit['newCtgName'] == "") {
            return "Nazwa kategori nie może być pusta";
        } 
        else if($categoryToEdit['newCtgName'] != $categoryToEdit['oldCtgName']) 
        {
            $userId = $categoryToEdit['userId'];
            $newName = $categoryToEdit['newCtgName'];
            
            if(Incomes::isCategoryNameUnique($newName, $userId) == false) 
            {
                return "Nazwa kategori już istnieje";
            }
        }   
        
        $db = static::getDB();
        
        $sql = "UPDATE incomes_category_assigned_to_users
                SET name = :newName
                WHERE user_id = :userId AND id = :ctgId";
        
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(":newName", $categoryToEdit['newCtgName'], PDO::PARAM_STR);
        $stmt->bindValue(":userId", $categoryToEdit['userId'], PDO::PARAM_INT);
        $stmt->bindValue(":ctgId", $categoryToEdit['ctgId'], PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return "success";
        } else {
            return "Błąd serwera, przepraszamy";
        }
    }    
    
    public static function deleteCategory($userId, $ctgId) 
    {
        
        $db = static::getDB();
        
        $sql = "DELETE FROM incomes_category_assigned_to_users
                WHERE user_id= :userId AND id = :ctgId";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":ctgId", $ctgId, PDO::PARAM_INT);
        
        return $stmt->execute();
        
    }    
    
    public static function addNewCategory($userId, $ctgName)
    {
        if($ctgName == "") {
            return "Nazwa kategorie nie może być pusta";
        }
        else if(Incomes::isCategoryNameUnique($ctgName, $userId) == false) {
            return "Nazwa kategori już istnieje";
        }
        
        $db = static::getDB();
        
        $sql = "INSERT INTO incomes_category_assigned_to_users (user_id, name)
                VALUES(:userId, :ctgName)";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":ctgName", $ctgName, PDO::PARAM_STR);
        
        if($stmt->execute()) {            
            return "success";
        } else {
            return "Błąd serwera";
        }
    }
    
    
    public static function getNewCategoryId($categoryName, $userId)
    {
        $db = static::getDB();
        
        $sql = "SELECT id FROM incomes_category_assigned_to_users
                WHERE user_id = :userId AND name = :ctgName";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":ctgName", $categoryName, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        if($stmt->execute()) {
            $result = $stmt->fetch();
            return $result['id']; 
        } else {
            return 'Błąd serwera';
        }
    }    
    
    public static function deleteAllUserIncomes($userId)
    {
        $db = static::getDB();
        
        $sql = "DELETE FROM incomes
                WHERE user_id = :userId";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
        
        return $stmt->execute();        
    }
    
    public static function doesIncomeBelongToUser($incomeId, $userId)
    {
        $sql = 'SELECT amount FROM incomes
                WHERE id = :incomeId AND user_id = :userId';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':incomeId', $incomeId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() == 0 ) {
            return false;
        } else {
            return true;
        }
    }    
    
    public static function deleteIncome($incomeId, $userId)
    {
        if(Incomes::doesIncomeBelongToUser($incomeId, $userId)) {
            
            $sql = 'DELETE FROM incomes
                    WHERE id = :incomeId';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':incomeId', $incomeId, PDO::PARAM_INT);
            
            return $stmt->execute();        
        }
    }    
    
    public static function areThereIncomesInTheCategory($categoryId)
    {
        $sql = 'SELECT amount FROM incomes
                WHERE income_category_assigned_to_user_id = :categoryId';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() == 0 ) {
            return false;
        } else {
            return true;
        }
    }    
    
}