<?php
session_start();

if(empty($_SESSION["userid"])):
    header("Location: index.php");
endif;
include_once("connect.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Grades</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bootstrap-select.css" rel="stylesheet">
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
                        <a href="student.php"><i class="fa fa-fw fa-dashboard"></i> Student</a>
                    </li>
                    <li>
                        <a href=""><i class="fa fa-fw fa-bar-chart-o"></i> Grades</a>
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
                            Grades
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
                            <input type="hidden" id="request_type" name="request_type" value="save_grade">
                            <input type="hidden" id="id" name="id">
                            <div class="form-group">
                                <label>Student</label>
                                <select class='selectpicker' id='idstudent' name='idstudent' data-live-search='true'>
                                    <option value=''>SELECCIONE: </option>
                                    <?php
                                    $sql = "SELECT * FROM student";
                                    $result = mysqli_query($link,$sql);
                                    while ($rs = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <option value="<?= $rs["id"] ?>"><?= $rs["name"] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Grade 1</label>
                                <input class="form-control" id="grade1" name="grade1" placeholder="grade 1" onkeypress="numbers()">
                            </div>
                            <div class="form-group">
                                <label>Grade 2</label>
                                <input class="form-control" id="grade2" name="grade2" placeholder="grade 2" onkeypress="numbers()">
                            </div>
                            <div class="form-group">
                                <label>Grade 3</label>
                                <input class="form-control" id="grade3" name="grade3" placeholder="grade 3" onkeypress="numbers()">
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
            <th>Name</th>
            <th>Grade 1</th>
            <th>Grade 2</th>
            <th>Grade 3</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="grades">
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
    <script type="text/javascript" src="js/bootstrap-select.js"></script>
    <script type="text/javascript">
function numbers()
{
    if ((event.keyCode < 48) || (event.keyCode > 57)) 
    event.preventDefault();
}


    function save_data(){
        if($("#idstudent").val()==""){
            $('#messages').removeClass()
            $("#messages").addClass("alert alert-danger");
            $("#messages").html("You must select a Student.");
            return false;
        }
        if($("#grade1").val()==""){
            $('#messages').removeClass()
            $("#messages").addClass("alert alert-danger");
            $("#messages").html("You must complete the grade 1 field.");
            return false;
        }
        if($("#grade2").val()==""){
            $('#messages').removeClass()
            $("#messages").addClass("alert alert-danger");
            $("#messages").html("You must complete the grade 2 field.");
            return false;
        }
        if($("#grade3").val()==""){
            $('#messages').removeClass()
            $("#messages").addClass("alert alert-danger");
            $("#messages").html("You must complete the grade 3 field.");
            return false;
        }
        $.post("grades_data.php",$("#form1").serialize(),function(response){
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
        $("#idstudent").val("");
        $("#grade1").val("");
        $("#grade2").val("");
        $("#grade3").val("");
        $("#request_type").val("save_grade");      
        get_grades(); 
    }

    function get_grades(){
        $("#request_type").val("list_grades");
        $("#grades").html("");
        $.post("grades_data.php",$("#form1").serialize(),function(response){
            json = eval('('+response+')');
            if(json.result==true){
                n = 1;
                $.each(json.data,function(k,v){
                    $("#grades").append("<tr><td>"+n+"</td><td>"+v.name+"</td><td>"+v.exam1+"</td><td>"+v.exam2+"</td><td>"+v.exam3+"</td><td><button title='Delete Student' onclick='delete_grade("+v.idgrade+")'>D</button><button title='Edit Student' onclick='edit("+v.idgrade+","+v.idstudent+",\""+v.exam1+"\",\""+v.exam2+"\",\""+v.exam3+"\")'>E</button></td></tr>");
                    n++;
                })
            }else{
                $('#messages').removeClass();
                $('#messages').addClass("alert alert-danger");
                $("#messages").html(json.msg);
                $('#messages').show('slow');
            }
            $("#request_type").val("save_grade");
        });
    }

    function delete_grade(id){
        if(confirm("Are you sure you want to delete this grade?")){
            $("#id").val(id);
            $("#request_type").val("delete_grade");
            $.post("grades_data.php",$("#form1").serialize(),function(response){
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

    function edit(id,idstudent,grade1,grade2,grade3){
        $("#id").val(id);
        $("#idstudent").val(idstudent);
        $("#grade1").val(grade1);
        $("#grade2").val(grade2);
        $("#grade3").val(grade3);
        $("#request_type").val("edit_grade");
    }

    get_grades();
    </script>

</body>

</html>
