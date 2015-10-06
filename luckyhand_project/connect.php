<?php

$link = mysqli_connect('localhost', 'root', '', 'php_project');

if (!$link) {
    die('Error: (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}

?>