<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'mybudget';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
    
    /**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = 'd7oXp566nhN7p1SguOkVZb6VsC2zNgGH';
    
    /**
     * Mailgun Api key
     * @var string
     */
    const MAILGUN_API_KEY = '27392fcff077052f6f671d757105cc98-ba042922-effb5305';
    
    /**
     * Mailgun domain name
     * @var string
     */
    const MAILGUN_DOMAIN = 'sandbox512042a505894bc0a43f4ac2a6d5d0cf.mailgun.org';
}
