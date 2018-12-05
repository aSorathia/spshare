<?php 
    require_once 'class_handler.php';
    
    $status = $baseController->checkLoginState();
    if(!$status[0]){
        header("location:index.php");
        exit();
    }else if($status[1]==2){
        header("location: admin_dashboard.php");
        exit();
    }
    date_default_timezone_set('America/Chicago');
    $ui = $userController->getUserInfo($_SESSION['u_id'])[0];

?>
<html>
    <head>
        <script src="web_resources/js/jquery.min.js"></script>
        <script src="web_resources/js/bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
        <link rel="stylesheet" href="web_resources/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="web_resources/css/custom.css">
    
    </head>
    <body>
        <nav class="navbar navbar-inverse">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">SP Share</a>
                    <span style="float: right;">
                    <button type="button" class="requestNewGrp btn btn-lg btn-primary">Request Group</button>
                    <button type="button" class="joinNewGrp btn btn-lg btn-primary">Join Group</button>
                    </span>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="uploadFile.php" target="_blank" >Add</a></li>               
                                <li role="separator" class="divider"></li>
                                <li><a>Last Access: <?php echo htmlspecialchars($ui['last_login'], ENT_QUOTES , 'UTF-8')?></a></li>
                                <li><?php if($baseController->checkLoginState()){echo '<a href="logout.php" class="logout">logout</a>';}?></li>                        
                            </ul>
                        </li>
                        <li class="info-li">User:<span class="info-val"><?php echo htmlspecialchars($ui['u_login'], ENT_QUOTES , 'UTF-8')?></span></li>
                        <li><a><?php echo sprintf("Group space : %.2f/%.2f KB <br> Remaining : %.2f KB", (htmlspecialchars($ui['gUsedSpace'], ENT_QUOTES , 'UTF-8')/1024),(htmlspecialchars($ui['g_limit'], ENT_QUOTES , 'UTF-8')/1024),((htmlspecialchars($ui['g_limit'], ENT_QUOTES , 'UTF-8') - htmlspecialchars($ui['gUsedSpace'], ENT_QUOTES , 'UTF-8'))/1024));?></a></li>
                        <li><a><?php echo sprintf("User Space : %.2f/%.2f KB <br> Remaining : %.2f KB", (htmlspecialchars($ui['userUsedSpace'], ENT_QUOTES , 'UTF-8')/1024),(htmlspecialchars($ui['spaceLimit'], ENT_QUOTES , 'UTF-8')/1024),(htmlspecialchars($ui['spaceLimit'], ENT_QUOTES , 'UTF-8')-htmlspecialchars($ui['userUsedSpace'], ENT_QUOTES , 'UTF-8'))/1024);?></a></li>
                        <li><a><?php echo sprintf("Upload File Size Limit: %.2f KB", (htmlspecialchars($ui['fileLimit'], ENT_QUOTES , 'UTF-8')/1024));?></a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <div class="container custom_container" role="main">
        <!-- Main jumbotron for a primary marketing message or call to action -->
            <table class="table table-hover table-bordered" id="post_table">
                <thead>
                    <tr>
                        <th scope="col">File name</th>
                        <th scope="col">uploaded on</th>                        
                        <th scope="col">Groups</th>
                        <th scope="col">FileSize (KB)</th>
                        <th scope="col">Description</th>
                        <th scope="col">Get</th>
                        <th scope="col">Remove</th>
                        <th scope="col">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php  
                        $stmt = $baseController->getPDO()->prepare("SELECT * from `posts` inner join `groups` on `groups`.`g_id`=`posts`.`g_id` where `posts`.`u_id`=?");                      
                        $stmt->bindParam( 1,$ui['u_id'],PDO::PARAM_INT  );
                        $stmt->execute();
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            echo '
                                <tr>
                                    <td>'.htmlspecialchars($row["p_filename"], ENT_QUOTES , 'UTF-8').'</td>
                                    <td>'.date('m-d-Y H:i:s',htmlspecialchars($row["p_time_create"], ENT_QUOTES , 'UTF-8')).'</td>';                                    
                                    echo '<td>'.htmlspecialchars($row['g_name'], ENT_QUOTES , 'UTF-8').'</td>';
                                    echo sprintf("<td>%.2f</td>", (htmlspecialchars($row['fileSize'], ENT_QUOTES , 'UTF-8')/1024));
                                    echo '<td class="desc">'.htmlspecialchars($row['p_desc'], ENT_QUOTES , 'UTF-8').'</td>';
                                    if(htmlspecialchars($row["u_id"], ENT_QUOTES , 'UTF-8') == htmlspecialchars($ui["u_id"], ENT_QUOTES , 'UTF-8')){
                                        echo '<td class="align"><a href="class_handler.php?download='.htmlspecialchars($row["p_id"], ENT_QUOTES , 'UTF-8').'"><img src="web_resources/images/icon-download.png" title="download" alt="GET" class="icon"></a></td>';
                                        echo '<td class="align"><a data-post_id='.htmlspecialchars($row["p_id"], ENT_QUOTES , 'UTF-8').' class="remove_post"><img src="web_resources/images/icon-delete.png" title="download" alt="GET" class="icon"></a></td>';
                                        echo '<td class="align"><a data-post_id='.htmlspecialchars($row["p_id"], ENT_QUOTES , 'UTF-8').' class="edit_post"><img src="web_resources/images/icon-edit.png" title="download" alt="GET" class="icon"></a></td>';
                                    }
                            echo '</tr>';
                        }
                    ?>
                    
                </tbody>
            </table>
        </div> 
        <div class="modal fade" id="editFileModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span>Select Action</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" method ="POST" id="edit_form">
                            <div class="form-group">
                                <input type="hidden" id="p_id" name="p_id">                                    
                            </div>
                            <div class="form-group">
                                <label for="p_fileName">File Name</label>
                                <input type="text" class="form-control" id="p_fileName" name="p_fileName" placeholder="Enter User Name">
                            </div>
                            <div class="form-group">
                                <label for="p_desc">File Descriptione</label>
                                <textarea class="form-control" id="p_desc" name="p_desc" placeholder="Enter File Description"></textarea>
                            </div>
                            <button type="button" class="btn btn-default btn-success btn-block editFileSubmit"><span class="glyphicon glyphicon-off" ></span>Submit Edit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div> 
        <div class="modal fade" id="requestGroupModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span>Request Group</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="grp_request_form">
                            <div class="form-group">
                                <input type="hidden" id="u_login" name="u_login" value="<?php echo htmlspecialchars($ui['u_login'], ENT_QUOTES , 'UTF-8')?>">
                                <label for="g_name"><span class="glyphicon glyphicon-user"></span> Group Name</label>
                                <input type="text" class="form-control" id="g_name" name="g_name" placeholder="Enter Group Name">
                            </div>
                            <button type="button" class="btn btn-default btn-success btn-block" id="requestGroupSubmit"><span class="glyphicon glyphicon-off" ></span>Request Group</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="joinGroupModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span>Join Group</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="grp_join_form">
                            <div class="form-group">
                                <input type="hidden" id="u_id" name="u_id" value="<?php echo htmlspecialchars($ui['u_id'], ENT_QUOTES , 'UTF-8')?>">
                                <input type="hidden" id="u_login" name="u_login" value="<?php echo htmlspecialchars($ui['u_login'], ENT_QUOTES , 'UTF-8')?>">
                                <label for="g_name"><span class="glyphicon glyphicon-user"></span>Please select group to join:</label>
                                <select multiple class="form-control" id="groups_choice" name="groups_choice[]">
                                    <?php
                                        $stmt = $baseController->getPDO()->prepare("SELECT `g_id`,`g_name` from `groups`");                      
                                        $stmt->bindParam( 1, $ui['u_id'],PDO::PARAM_INT );
                                        $stmt->execute();
                                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                            echo '<option value="'.htmlspecialchars($row['g_id'], ENT_QUOTES , 'UTF-8').'">'.htmlspecialchars($row['g_name'], ENT_QUOTES , 'UTF-8').'</option>';
                                        }
                                    ?>                
                                </select>
                            </div>
                            <button type="button" class="btn btn-default btn-success btn-block" id="joinGroupSubmit"><span class="glyphicon glyphicon-off" ></span>Join Group</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
        //$userController->displayInfo($ui);
        ?>
        <script>  
            $(document).ready(function(){
                $('#post_table').DataTable();
                $(".requestNewGrp").click(function(){
                    $("#requestGroupModal").modal();
                });
                $("#requestGroupSubmit").click(function(){
                    $form = $("#grp_request_form");
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=requestNewGrp",
                        data: $form.serialize(),
                        success: function(response){       
                            if (response == "success"){
                                location.reload();
                            }else{
                                alert(response);
                            }
                        }
                    });
                });
                $(".joinNewGrp").click(function(){
                    $("#joinGroupModal").modal();
                });
                $("#joinGroupSubmit").click(function(){
                    $form = $("#grp_join_form");
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=joinNewGrp",
                        data: $form.serialize(),
                        success: function(response){              
                            if (response == "success"){
                                location.reload();
                            }else{
                                alert(response);
                            }
                        }
                    });
                });
               
                $(".remove_post").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=remove",
                        data: {'pid':$(this).attr("data-post_id")},
                        success: function(response){     
                            console.log(response);                
                            if (response == "success"){
                                location.reload();
                            }else{
                                alert('There was some error in deleting post');
                            }
                        }
                    });
                });

                $(".edit_post").click(function(){
                    $("#edit_form #p_id").val($(this).attr("data-post_id"));       
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=getFileInfo",
                        data: {'pid':$(this).attr("data-post_id")},
                        dataType: "json",
                        success: function(response){ 
                            console.log(response);  
                            $("#editFileModal #p_fileName").val(response['filename']);
                            $("#editFileModal #p_desc").val(response['desc']);
                            $("#editFileModal").modal();            
                        },error: function(response){      
                            alert('There was some error in editing post');

                        }
                    });
                });

                $(".editFileSubmit").click(function(e){                    
                    $form = $("#edit_form");                    
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=editPost",
                        data: $form.serialize(),
                        success: function(response){      
                            console.log(response);                      
                            if (response == "success"){
                                $('#editFileModal').modal('hide');
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
<html>