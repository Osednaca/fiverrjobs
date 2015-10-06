<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>New Student</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper" style="background: white; padding-top: 50px; padding-bottom: 100px;">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Students - Grades</a>
            </div>
        </nav>

        <div>

                <div class="row">
                    <div class="col-lg-6">
<h3>Register your account</h3>
                        <form role="form" id="form1">
                            <input type="hidden" id="request_type" name="request_type">
                            <input type="hidden" id="id" name="id">
                            <div class="form-group">
                                <label>Username</label>
                                <input class="form-control" id="username" name="username" placeholder="Username">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            </div>
                            <div class="form-group">
                                <label>Repeat Password</label>
                                <input class="form-control" type="password" id="rpassword" name="rpassword">
                            </div>
                            <div id="messages"></div>
                            <button type="button" class="btn btn-default" onclick="save_data()">Save</button>
                            <button type="reset" class="btn btn-default">Cancel</button>
                        </form>

                    </div>
                </div>
                <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function save_data(){
        if($("#username").val()==""){
            $('#messages').removeClass()
            $("#messages").addClass("alert alert-danger");
            $("#messages").html("You must complete the User field.");
            return false;
        }
        if($("#password").val()==""){
            $('#messages').removeClass()
            $("#messages").addClass("alert alert-danger");
            $("#messages").html("You must complete the Password field.");
            return false;
        }
        if($("#password").val()!=$("#rpassword").val()){
            $('#messages').removeClass()
            $("#messages").addClass("alert alert-danger");
            $("#messages").html("The password field must be equal to the repeat password field.");
            return false;
        }
        $("#request_type").val("save_user");
        $.post("user_data.php",$("#form1").serialize(),function(response){
            json = eval('('+response+')');
            if(json.result==true){
                $('#messages').removeClass()
                $('#messages').addClass("alert alert-success");
                clear_form();
            }else{
                $('#messages').removeClass();
                $('#messages').addClass("alert alert-danger");
            }
            $("#messages").html(json.msg);
            $('#messages').show('slow');
            setTimeout("jQuery('#messages').hide('slow');",3000);
        });
    }

    function clear_form(){
        $("#username").val("");
        $("#password").val("");
        $("#rpassword").val("");
        $("#request_type").val("");       
    }

    </script>

</body>

</html>
