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
    
}