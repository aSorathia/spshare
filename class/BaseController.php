<?php

/*
combine gropname and grp list JOIN
*/ 


    date_default_timezone_set('UTC');

    class BaseController{
        private $pdo;
        public function __construct($pdo){
            if(!is_null($pdo)){
                $this->pdo = $pdo;
            }else{
                echo "db connection erro";
                return false;
            }
        }
        public function requestNewGrp($ulogin,$gname){
            $grp_stmt = $this->pdo->prepare('SELECT `g_name` FROM `requestex` WHERE `g_name`=? LIMIT 1');
            $grp_stmt->bindParam( 1, $gname,PDO::PARAM_STR, 68 );
            $grp_stmt->execute();
            $row = $grp_stmt->fetch();
            $grp_stmt = null;
            if($row){
                echo 'Group request already exists ';
            }else{
                $stmt = $this->pdo->prepare(
                    'INSERT INTO `requestex`(`u_login`, `g_name`) VALUES (?,?)'
                );
                $stmt->bindParam( 1, $ulogin, PDO::PARAM_STR, 16);
                $stmt->bindParam( 2, $gname, PDO::PARAM_STR, 68);
                $stmt->execute();
                $stmt=null;
                echo 'success';
            }
        }

        public function register($name,$login,$pass,$gid){
            if(is_null($this->pdo)){
                return false;
            }
            $hashPass = password_hash($pass, PASSWORD_ARGON2I);
            $stmt = $this->pdo->prepare(
                'INSERT INTO `request`(`r_name`, `r_login`, `r_pass`, `g_id`)
                 VALUES (?,?,?,?)'
            );
            $stmt->bindParam( 1, $name,80 );
            $stmt->bindParam( 2, $login, PDO::PARAM_STR, 16);
            $stmt->bindParam( 3, $hashPass, PDO::PARAM_STR, 100);
            $stmt->bindParam( 4, $gid, PDO::PARAM_INT);
            $stmt->execute();
            $stmt=null;

            $this->return_back();
        }

        public function checkLoginState(){
            if(isset($_COOKIE['u_id']) && isset($_COOKIE['token']) && isset($_COOKIE['key'])){
                $userid = $_COOKIE['u_id'];
                $token = $_COOKIE['token'];
                $key = $_COOKIE['key'];

                $stmt = $this->pdo->prepare("SELECT * from `sessions` where `s_uid` = ? and `s_token` = ? and s_key = ?");
                $stmt->bindParam( 1,$userid,PDO::PARAM_INT);
                $stmt->bindParam( 2,$token,PDO::PARAM_STR,32);
                $stmt->bindParam( 3,$key,PDO::PARAM_STR,32);
                
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt=null;

                $usertype=getUserType($userid,$this->pdo)['r_id'];
                if($row['s_uid']>0){
                    if($row['s_uid']==$_COOKIE['u_id'] && $row['s_token']==$_COOKIE['token'] && $row['s_key']==$_COOKIE['key']){
                        if($row['s_uid']==$_SESSION['u_id'] && $row['s_token']==$_SESSION['token'] && $row['s_key']==$_SESSION['key']){
                            return array(true,$usertype);
                        }else{
                            $this->createSession($_COOKIE['u_login'],$_COOKIE['u_id'],$_COOKIE['token'],$_COOKIE['key']);
                            return array(true,$usertype);
                        }
                    }
                }
            }
        }

        public function userLogin($u_login,$u_pass){
            /**/

            $stmt = $this->pdo->prepare("SELECT * from `users` WHERE `u_login`=? LIMIT 1");
            $stmt->bindParam( 1,$u_login,PDO::PARAM_STR ,16 );
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt=null;
            if (password_verify($u_pass, $row['u_pass'])) {
                if($row['u_id']>0){
                    $user_stmt = $this->pdo->prepare("SELECT * from `user_group` WHERE `u_id`=?");
                    $user_stmt->bindParam( 1,$row['u_id'],PDO::PARAM_INT);
                    $user_stmt->execute();
                    $user_row = $user_stmt->fetch(PDO::FETCH_ASSOC);
                    $user_stmt=null;
                    if($user_row['ug_id']>0){
                        $this->user = $row;
                        $this->createCookieSessionRecord($this->pdo,$row['u_login'],$row['u_id']);
                        if($row['r_id']==1){                   
                            $notific = '';
                            header("location: dashboard.php");
                        }
                    }
                    if($row['r_id']==2){
                        $this->user = $row;
                        $this->createCookieSessionRecord($this->pdo,$row['u_login'],$row['u_id']);
                        $notific ='';
                        header("location: admin_dashboard.php");
                    }else{
                        return 'Admin has deleted the Group for which you were member of. Kindly register again to a new group';
                    }                
                }else{
                    header("location:index.php");
                }
            }
        }

        public function logout(){
            setcookie('u_login','',time()-1,"/");
            setcookie('u_id','',time()-1,"/");
            setcookie('token','',time()-1,"/");
            setcookie('key','',time()-1,"/");
        }

        public function getPDO(){
            return $this->pdo;
        }   

        private function createString($len){
            $string = "13jhdfvFTRDhgvhjedfhvehwfgRRTETCGFCFGFC654898bdfhsdvfhFTYF747hfhgfFTR";
            return substr(str_shuffle($string),0,$len);
        }

        function createCookieSessionRecord($pdo,$u_login,$u_id){
            $stmt = $pdo->prepare("DELETE from sessions where s_uid=?");
            $stmt->bindParam( 1, $u_id,PDO::PARAM_INT );
            $stmt->execute();
            $stmt=null;
            $token = $this->getCryptedString(31);
            $key = $this->getCryptedString(31);
            $this->createCookie($u_login,$u_id,$token,$key);
            $this->createSession($u_login,$u_id,$token,$key);
            $sessionDate = "11/18/2018";
            $stmt = $pdo->prepare('INSERT INTO `sessions`(`s_token`, `s_key`, `s_date`, `s_uid`)
            VALUES (?,?,?,?)'
            );
            $stmt->bindParam( 1, $token,PDO::PARAM_STR, 32 );
            $stmt->bindParam( 2, $key,PDO::PARAM_STR, 32 );
            $stmt->bindParam( 3, $sessionDate,PDO::PARAM_STR, 10 );
            $stmt->bindParam( 4, $u_id,PDO::PARAM_INT );
            $stmt->execute();
            $stmt=null;
        }

        function return_back(){
            if(isset($_REQUEST["destination"])){
                header("Location: {$_REQUEST["destination"]}");
            }else if(isset($_SERVER["HTTP_REFERER"])){
                header("Location: {$_SERVER["HTTP_REFERER"]}");
            }
        }       
        

        private function createCookie($u_login,$u_id,$token,$key){
            setcookie('u_login',$u_login,time()+(86400)*30,"/");
            setcookie('u_id',$u_id,time()+(86400)*30,"/");
            setcookie('token',$token,time()+(86400)*30,"/");
            setcookie('key',$key,time()+(86400)*30,"/");
        }

        private function createSession($u_login,$u_id,$token,$key){
            if(!isset($_SESSION['u_id'])){
                session_start();
            }
            $_SESSION['u_login']=$u_login;
            $_SESSION['u_id']=$u_id;
            $_SESSION['token']=$token;
            $_SESSION['key']=$key;
        }
    
        public function getCryptedString($len){
            return $this->createString($len);
        }
    }

    function getUserType($u_id,$pdo){
        $stmt = $pdo->prepare("SELECT * from `users` WHERE `u_id`=?");
        $stmt->bindParam( 1, $u_id,PDO::PARAM_INT );
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt=null;
        return $row;
    }

    

    function getUserName($pdo,$u_id){
        $stmt = $pdo->prepare("SELECT `u_name` from `users` WHERE `u_id` = ? LIMIT 1");
        $stmt->bindParam( 1, $u_id,PDO::PARAM_INT );
        $stmt->execute();
        $row = $stmt->fetch();
        $stmt = null;
        return $row['u_name'];
    }

    
?>
