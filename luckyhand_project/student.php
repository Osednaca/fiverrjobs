<?php
session_start();
if(empty($_SESSION["userid"])):
    header("Location: index.php");
endif;
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

    <div id="wrapper">

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
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?= $_SESSION["username"] ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li class="divider"></li>
                        <li>
                            <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li>
                        <a href=""><i class="fa fa-fw fa-dashboard"></i> Student</a>
                    </li>
                    <li>
                        <a href="grades.php"><i class="fa fa-fw fa-bar-chart-o"></i> Grades</a>
                    </li>
                    <li>
                        <a href="report.php"><i class="fa fa-fw fa-table"></i> Report</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            New Student
                        </h1>
                        <ol class="breadcrumb">
                            <li>
                                <i class="fa fa-dashboard"></i>  <a href="dashboard.php">Dashboard</a>
                            </li>
                            <li class="active">
                                <i class="fa fa-edit"></i> Forms
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- /.row -->

                <div class="row">
                    <div class="col-lg-6">

                        <form role="form" id="form1">
                            <input type="hidden" id="request_type" name="request_type">
                            <input type="hidden" id="id" name="id">
                            <div class="form-group">
                                <label>ID</label>
                                <input class="form-control" id="student_id" name="student_id" placeholder="Student ID">
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input class="form-control" id="name" name="name" placeholder="Name">
                            </div>

                            <div class="form-group">
                                <label>Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option></option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Birthday</label>
                                <input class="form-control" type="date" id="birthday" name="birthday">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input class="form-control" placeholder="Phone Number" id="phone" name="phone">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" placeholder="Email" id="email" name="email">
                            </div>

                            <div id="messages"></div>
                            <button type="button" class="btn btn-default" onclick="save_data()">Save</button>
                            <button type="reset" class="btn btn-default">Cancel</button>
                        </form>

                    </div>
                </div>
                <!-- /.row -->
<table class="table table-bordered table-striped table-condensed table-responsive" style="margin-top: 20px;">
    <thead>
        <tr>
            <th>N</th>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="students">
    </tbody>
</table>
            </div>
            <!-- /.container-fluid -->

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
        if($("#name").val()==""){
            $('#messages').removeClass()
            $("#messages").addClass("alert alert-danger");
            $("#messages").html("You must complete the Name field.");
            return false;
        }
        $.post("student_data.php",$("#form1").serialize(),function(response){
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
        $("#id").val("");
        $("#student_id").val("");
        $("#name").val("");
        $("#email").val("");
        $("#phone").val("");
        $("#birthday").val("");
        $("#request_type").val("save_student");      
        get_students(); 
    }

    function get_students(){
        $("#request_type").val("list_student");
        $("#students").html("");
        $.post("student_data.php",$("#form1").serialize(),function(response){
            json = eval('('+response+')');
            if(json.result==true){
                n = 1;
                $.each(json.data,function(k,v){
                    $("#students").append("<tr><td>"+n+"</td><td>"+v.student_id+"</td><td>"+v.name+"</td><td>"+v.email+"</td><td><button title='Delete Student' onclick='delete_student("+v.idstudent+")'>D</button><button title='Edit Student' onclick='edit("+v.idstudent+",\""+v.student_id+"\",\""+v.name+"\",\""+v.email+"\",\""+v.phone+"\",\""+v.birthday+"\",\""+v.gender+"\")'>E</button></td></tr>");
                    n++;
                })
            }else{
                $('#messages').removeClass();
                $('#messages').addClass("alert alert-danger");
                $("#messages").html(json.msg);
                $('#messages').show('slow');
            }
            $("#request_type").val("save_student");
        });
    }

    function delete_student(id){
        if(confirm("Are you sure you want to delete this student?")){
            $("#id").val(id);
            $("#request_type").val("delete_student");
            $.post("student_data.php",$("#form1").serialize(),function(response){
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
    }

    function edit(id,student_id,name,email,phone,birthday,gender){
        $("#id").val(id);
        $("#student_id").val(student_id);
        $("#name").val(name);
        $("#gender").val(gender);
        $("#email").val(email);
        $("#phone").val(phone);
        $("#birthday").val(birthday);
        $("#request_type").val("edit_student");
    }

    get_students();
    </script>

</body>

</html>
