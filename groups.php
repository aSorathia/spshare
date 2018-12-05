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
                <tr><th>ID</th><th>Name</th><th>GroupLimit</th><th>Edit</th><th>remove</th></tr>
                <?php
                    $grp_stmt = $baseController->getPDO()->prepare("SELECT * FROM `groups`");
                    $grp_stmt->execute();
                    while($grp_row = $grp_stmt->fetch(PDO::FETCH_ASSOC)){
                        echo "<tr><td>".htmlspecialchars($grp_row['g_id'], ENT_QUOTES , 'UTF-8')."</td><td>".htmlspecialchars($grp_row['g_name'], ENT_QUOTES , 'UTF-8')."</td><td>".(htmlspecialchars($grp_row['g_limit'], ENT_QUOTES , 'UTF-8')/1024)." KB</td>";
                        echo '<td><button type="button" class="btn btn-success btn-lg updateGrppop" data-grp-id="'.htmlspecialchars($grp_row['g_id'], ENT_QUOTES , 'UTF-8').'">edit</button></td>';    
                        echo '<td><button type="button" class="btn btn-danger btn-lg removeGroup" data-grp-id="'.htmlspecialchars($grp_row['g_id'], ENT_QUOTES , 'UTF-8').'">remove</button></td>';    
                    }
                    $req_stmt=null;
                ?>
            </table>
        </div>
        <div class="modal fade" id="groupUpdateModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Update Group</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="grp_update_form">
                            <div class="form-group">
                                <input type="hidden" id="gid" name="gid" value="">
                            </div>
                            <div class="form-group">
                                <label for="g_name"><span class="glyphicon glyphicon-user"></span> Group Name</label>
                                <input type="text" class="form-control" id="g_name" name="g_name" placeholder="Enter Group Name">
                            </div>
                            <div class="form-group">
                                <label for="g_limit"><span class="glyphicon glyphicon-eye-open"></span>Max Group Space Limit</label>
                                <input type="number" id="g_limit" class="form-control" name="g_limit" placeholder="Min: 1KB, max: 10240KB" min="1" max="10240" />
                            </div>
                            <button type="button" class="btn btn-default btn-success btn-block" id="update_grp_submit"><span class="glyphicon glyphicon-off" ></span>Update Group</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="removePrompt" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                
                    <p>Are you sure you want to remove group?</p>
                    <form role="form" id="removeGrpForm">
                        <div class="form-group">
                            <input type="hidden" id="gid" name="gid" value="">
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
        <script>
            $(document).ready(function(){
                $(".removeGroup").click(function(){
                    $("#removeGrpForm #gid").val($(this).attr("data-grp-id"));
                    $("#removePrompt").modal();
                });
                $(".removeConfirm").click(function(){                    
                    $form = $("#removeGrpForm");
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=removeGrp",
                        data: $form.serialize(),
                        success: function(response){
                            console.log(response);
                            location.reload();
                        }
                    });
                });

                $(".updateGrppop").click(function(){
                    //$("#g_name").val($(this).attr("data-group-name"))                      
                    console.log($(this).attr("data-grp-id"));
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=getGroupInfo",
                        data: {'gid':$(this).attr("data-grp-id")},
                        dataType: "json",
                        success: function(response){
                            $("#groupUpdateModal #gid").val(response['g_id'])  
                            $("#groupUpdateModal #g_name").val(response['g_name'])  
                            $("#groupUpdateModal #g_limit").val(response['g_limit']/1024)  
                            $("#groupUpdateModal").modal();
                            console.log(response);
                        }
                    }); 
                    
                });

                $("#update_grp_submit").on('click', function(e) {                   
                    $form = $("#grp_update_form");
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=update_group",
                        data: $form.serialize(),
                        success: function(response){
                            console.log(response);
                            if (response == "success"){
                                $('#groupUpdateModal').modal('hide');
                                location.reload();
                            }else{
                                alert('There was some error in creating group');
                            }
                        }
                    });                    
                });
            });
        </script>
    </body>    ​
</html>