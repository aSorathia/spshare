<?php
    require_once 'class_handler.php';
    
    $status = $baseController->checkLoginState();
    if(!$status[0]){
        header("location:index.php");
        exit();
    }else if($status[1]==1){
        header("location: dashboard.php");
        exit();
    }
    date_default_timezone_set('America/Chicago');
?>

<html>
    <head>
        <script src="web_resources/js/jquery.min.js"></script>
        <script src="web_resources/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="web_resources/css/bootstrap.min.css">
    </head>
    <body>
        <nav class="navbar bg-primary">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">SP Share</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item"><a href="groups.php" class=" btn btn-lg btn-primary">View Groups</a></li>
                        <li class="nav-item"><a href="users.php" class=" btn btn-lg btn-primary">View Users</a></li>
                        <li class="nav-item"><a href="admin_dashboard.php" class=" btn btn-lg btn-primary">Request</a></li>
                        <?php if($baseController->checkLoginState()){echo '<li class="nav-item"><a href="logout.php" class=" btn btn-lg btn-primary">logout</a></li>';}?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <div class="container-fluid">
            <table border=1>
                <tr><td colspan="7">Groups List</td></tr>
                <tr><th>ID</th><th>Name</th><th>Login id</th><th>Edit</th><th>remove</th></tr>
                <?php
                    $user_stmt = $baseController->getPDO()->prepare("SELECT * FROM `users`");
                    $user_stmt->execute();
                    while($user_row = $user_stmt->fetch(PDO::FETCH_ASSOC)){                     
                        echo "<tr><td>".htmlspecialchars($user_row['u_id'], ENT_QUOTES , 'UTF-8')."</td><td>".htmlspecialchars($user_row['u_name'], ENT_QUOTES , 'UTF-8')."</td><td>".htmlspecialchars($user_row['u_login'], ENT_QUOTES , 'UTF-8')."</td>";
                        echo '<td><button type="button" class="btn btn-success btn-lg updateUserpop" data-user-id="'.htmlspecialchars($user_row['u_id'], ENT_QUOTES , 'UTF-8').'">edit</button></td>'; 
                        echo '<td><button type="button" class="btn btn-danger btn-lg removeUser" data-user-id="'.htmlspecialchars($user_row['u_id'], ENT_QUOTES , 'UTF-8').'">remove</button></td></tr>'; 
                    }
                    $req_stmt=null;
                ?>
            </table>
        </div>
        <div class="modal fade" id="userUpdateModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Update User</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="user_update_form">
                            <div class="form-group">
                                <input type="hidden" id="uid" name="uid" value="">
                            </div>
                            <div class="form-group">
                                <label for="u_login"><span class="glyphicon glyphicon-user"></span> User Login</label>
                                <input type="text" class="form-control" id="u_login" name="u_login" placeholder="Enter User Name">
                            </div>
                            <div class="form-group">
                                <label for="u_type"><span class="glyphicon glyphicon-eye-open"></span>User Type</label>
                                <select id="u_type" class="form-control" name="u_type">
                                    <option value="1" selected>general-user</option>
                                    <option value="2">admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ug_file_limit"><span class="glyphicon glyphicon-user"></span>Max Per File Limit</label>
                                <input type="number" id="fileLimit" class="form-control" name="fileLimit" placeholder="Min: 1KB, max: 10240KB" min="1" max="10240" />
                            </div>
                            <div class="form-group">
                                <label for="ug_file_limit"><span class="glyphicon glyphicon-user"></span>Max User Space Limit</label>
                                <input type="number" id="spaceLimit" class="form-control" name="spaceLimit" placeholder="Min: 1KB, max: 10240KB" min="1" max="10240" />
                            </div>
                            <button type="button" class="btn btn-default btn-success btn-block" id="update_usr_submit"><span class="glyphicon glyphicon-off" ></span>Update User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="removePrompt" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove user</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                
                    <p>Are you sure you want to remove user?</p>
                    <form role="form" id="removeUserForm">
                        <div class="form-group">
                            <input type="hidden" id="uid" name="uid" value="">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger removeConfirm">yes</button>
                    <button type="button" class="btn btn-primary removeDeny" data-dismiss="modal">no</button>
                </div>
                </div>
            </div>
        </div>
        ​<script>
            $(document).ready(function(){            
                $(".removeUser").click(function(){
                    $("#removeUserForm #uid").val($(this).attr("data-user-id"));
                    $("#removePrompt").modal();
                });  

                $(".removeConfirm").click(function(){
                    $form = $("#removeUserForm");
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=removeUser",
                        data: $form.serialize(),
                        success: function(response){
                            location.reload();
                        }
                    });
                });

                $(".removeDeny").click(function(){
                    $("#removePrompt").modal('hide');
                }); 

                $(".updateUserpop").click(function(){                
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=getUserInfo",
                        data: {'uid':$(this).attr("data-user-id")},
                        dataType: "json",
                        success: function(response){
                            $("#userUpdateModal #uid").val(response['u_id'])  
                            $("#userUpdateModal #u_login").val(response['u_login'])  
                            $("#userUpdateModal #u_type").val(response['r_id'])  
                            $("#userUpdateModal #fileLimit").val(response['fileLimit']/1024)  
                            $("#userUpdateModal #spaceLimit").val(response['spaceLimit']/1024)  
                            $("#userUpdateModal").modal();
                        }
                    });                    
                });

                $("#update_usr_submit").on('click', function(e) {
                    $form = $("#user_update_form");
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=update_user",
                        data: $form.serialize(),
                        success: function(response){                    
                            if (response == "success"){
                                $('#userCreateModal').modal('hide');
                                location.reload();
                            }else{
                                alert('There was some error in creating Users');
                            }
                        }
                    });                    
                });
            });
        </script>
    </body>    
</html>