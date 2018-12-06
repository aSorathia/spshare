<?php
    session_start();
   
    class DBConnect{
        private const SERVERNAME = 'us-cdbr-iron-east-01.cleardb.net';
        private const USERNAME = 'b149556befae85';
        private const PASSWORD = '286a5408';
        private const DBNAME = 'heroku_8c7683c880c7ed8';

        public static function getDbConnect(){
            $pdo = null;
            try {
                $Attributes = [
                    PDO::ATTR_EMULATE_PREPARES   => false, 
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ];
                $pdo = new PDO('mysql:host='.Self::SERVERNAME.';dbname='.Self::DBNAME, Self::USERNAME, Self::PASSWORD,$Attributes);
            }catch(PDOException $e){
                die($e->getMessage());
            }
            return $pdo;
        } 

    }//mysql://b149556befae85:286a5408@us-cdbr-iron-east-01.cleardb.net/heroku_8c7683c880c7ed8?reconnect=true           
?>