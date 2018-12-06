<?php
    session_start();
   
    class DBConnect{
        private const SERVERNAME = 'localhost';
        private const USERNAME = 'root';
        private const PASSWORD = '';
        private const DBNAME = 'test';

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

    }           
?>