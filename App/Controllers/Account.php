<?php

namespace App\Controllers;

use \App\Models\User;

/**
 * Account controller
 *
 * PHP version 7.0
 */
class Account extends \Core\Controller
{

    /**
     * Validate if email is available (AJAX) for a new signup or an existing user.
     * The ID of an existing user can be passed in in the querystring to ignore when
     * checking if an email already exists or not.
     *
     * @return void
     */
    public function validateEmailAction()
    {
        $is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);

        //The serverside response must be a JSON string that must be "true" for valid elements, and can be "false", undefined, or null for invalid elements. That is why we change content-type to json, and encode $is_valid to json
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }
}
