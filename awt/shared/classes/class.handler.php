<?php

define("DBHOST",isset($_ENV["DBHOST"]) ? $_ENV["DBHOST"] : "db");
define("DBUSER",isset($_ENV["DBUSER"]) ? $_ENV["DBUSER"] : "root");
define("DBPWD",isset($_ENV["DBPWD"]) ? $_ENV["DBPWD"] : "!woot");
define("DBNAME",isset($_ENV["DBNAME"]) ? $_ENV["DBNAME"] : "awt");
// TODO: uncomment when submitting homework and re-comment the local machine code.
define("RLHOST",isset($_ENV["RLHOST"]) ? $_ENV["RLHOST"] : "172.19.0.1");

// For my local machine
//define("RLHOST",isset($_ENV["RLHOST1"]) ? $_ENV["RLHOST1"] : "172.19.0.1");
define("RLPORT",isset($_ENV["RLPORT"]) ? $_ENV["RLPORT"] : "awt");
define("RLPWD",isset($_ENV["RLPWD"]) ? $_ENV["RLPWD"] : "awt");
define("RL_MAX",isset($_ENV["RL_MAX"]) ? $_ENV["RL_MAX"] : 40);
define("RL_SECS",isset($_ENV["RL_SECS"]) ? $_ENV["RL_SECS"] : 60);


require_once 'class.applicants.php';
require_once 'class.applications.php';
require_once 'class.studycost.php';
require_once 'class.request.php';
require_once 'inc/composer/vendor/autoload.php';

use Monolog\Level;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class Handler {
    public $db;
    public $log;
    public $rl = NULL;

    function __construct() {
        $logFilename = date('Y-m-d') . "_activity.log";

        $this->log = new Logger('AWT');
        $handler = new StreamHandler("log/$logFilename");
        $handler->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n"));
        $this->log->pushHandler($handler);

        $this->db = new mysqli(DBHOST, DBUSER, DBPWD, DBNAME);
        if (mysqli_connect_error()) {
            $this->log->error("Error connecting to MySQL Error: [" . mysqli_connect_error() . "]");
            $this->db = null;
        }
        
        
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
       
    }

    function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }
}

?>
