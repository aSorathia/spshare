<?php
    include 'class_handler.php';

    $status = $baseController->checkLoginState();
    if($status[0]){
        if($status[1]==1){
            echo 'present user';
            header("location: dashboard.php");
        }else{
            echo 'present admin';
            header("location: admin_dashboard.php");
        }                
    }
   
    $notific = '';
    if(isset($_POST['u_login']) && isset($_POST['u_pass'])){        
        $notific =  $baseController -> userLogin($_POST['u_login'],$_POST['u_pass']);
    }
?>
<html>
    <head>
        <script src="web_resources/js/jquery.min.js"></script>
        <script src="web_resources/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="web_resources/css/bootstrap.min.css">
        <link rel="stylesheet" href="web_resources/css/signin.css" >
    </head>
    <body>
        <div class="container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <h2 class="form-signin-heading">SP Share</h2>
            <label for="u_login" class="sr-only">Login Name</label>
            <input type="text" id="u_login" class="form-control" placeholder="Login name" name="u_login" required autofocus>
            <label for="u_pass" class="sr-only">Password</label>
            <input type="password" id="u_pass" class="form-control" placeholder="Password" name="u_pass" required>                        
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            <a href="register.php" class="btn btn-info" role="button">Register</a>
            
        </form> 
        <h4><i><?php echo $notific; ?></i></h4> 
        </div>    
    </body>
</html>