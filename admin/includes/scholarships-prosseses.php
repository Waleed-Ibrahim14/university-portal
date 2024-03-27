<?php 

if (isset($_POST['submit'])) {
    if (empty($_POST['class_name'])) {
        $msg = '<div class="alert alert-danger" role="alert">Please Enter Class Name</div>';
    }else {
        $insert = mysqli_query($connection, "INSERT INTO `classes` (`class_name`,`created_at`,`updated_at`) VALUES ('$class_name',NOW(),NOW())");
            if (isset($insert)) {
                $msg = '<div class="alert alert-success" role="alert">Create Clase Successfly</div>';
            }
    }
}
if (isset($_GET['delete'])) {
    $del_section = mysqli_query($connection, "DELETE FROM `classes` WHERE `id` = '$_GET[delete]'");
        if (isset($del_section)) {
            $msg = '<div class="alert alert-success" role="alert">Class Name deleted successsfuly</div>';
        }
    }       
?>