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

    <title>Report</title>

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
                <a class="navbar-brand" href="index.html">Students - Report</a>
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

                            <div id="messages"></div>
                            <button type="button" class="btn btn-default" onclick="get_grades()">Search</button>
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
            <th>grade 3</th>
            <th>Average</th>
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
        $.post("report_data.php",$("#form1").serialize(),function(response){
            json = eval('('+response+')');
            if(json.result==true){
                n = 1;
                $.each(json.data,function(k,v){
                    avg = (parseInt(v.exam1)+parseInt(v.exam2)+parseInt(v.exam3))/3;
                    $("#grades").append("<tr><td>"+n+"</td><td>"+v.name+"</td><td>"+v.exam1+"</td><td>"+v.exam2+"</td><td>"+v.exam3+"</td><td>"+avg.toFixed(2)+"</td></tr>");
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

    get_grades();
    </script>

</body>

</html>
