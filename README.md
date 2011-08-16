Panda MVC Framework, PHP 5.3.3+
=============

Configurations
---------------------
    $config = new stdClass;

    $config->debug = true;
    $config->defaultController = 'Index';
    $config->defaultMethod = 'Index';
    
    /* Database Connection String, Username, Password and init Command
       If you dont need init command comment it out */
    $config->dbHost = 'mysql:host=127.0.0.1;dbname=test;port=3306';
    $config->dbUser = 'root';
    $config->dbPass = 'root';
    $config->dbInitCmd = 'SET NAMES \'UTF8\'';
    
    /* To enable sessions to be stored within the Application folder 
       uncomment this line and create a folder /AppName/Sessions */
    //$config->sessionClass = 'SessionHandler';

    /* Example of Rewrites */
    $config->rewrites = array(
        'pattern' => array(
            '/^profile\/(.*?)/i',
            '/^news\/(.*?)\/([0-9]{1,11}$)/i',
            '/^topic\/(.*?)\/([0-9]{1,11}$)/i',
        ),
        'replacement' => array(
            'profile/index/$1',
            'news/article/$1/$2',
            'news/topic/$1/$2',
        ),
    );