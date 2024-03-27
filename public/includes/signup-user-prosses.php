<?php
session_start();
/*--------------------------------------------------------------------------
| Create New User Prosses::
|--------------------------------------------------------------------------*/
include_once("../admin/Models/DataBaseConnection.php");
require_once '../vendor/autoload.php';
/*--------------------------------------------------------------------------------------------------------
| ISO3166 This library was used, which contains a list of countries according to the ISO-3166-1 standard .
|-------------------------------------------------------------------------------------------------------*/
        use League\ISO3166\ISO3166;
        $iso3166 = new ISO3166();
        $countries = $iso3166->all();


 $msg = ''; // Initialize the message variable

 if (isset($_POST['submit'])) {
        $fullname = $_POST['fullname'];
        $country = $_POST['country'];
        $gender = $_POST['gender'];
        $username = strip_tags($_POST['username']);
        $email = $_POST['email'];
        $password = password_hash($_POST['password_1'],PASSWORD_DEFAULT);
      // Get image name
      $profile = $_FILES['profile']['name'];
      // image file directory
      $target = "../assets/images/users/".basename($profile);
      $imageFileType = strtolower(pathinfo($target,PATHINFO_EXTENSION));
    // Form Validation
    if(empty($fullname)){
        $msg ='<div class="alert alert-danger" role="alert">Please enter your full name</div>';
        }elseif(empty($country)){ 
            $msg ='<div class="alert alert-danger" role="alert">Please enter your country</div>';
        }elseif(empty($gender)){ 
            $msg ='<div class="alert alert-danger" role="alert">Please select your gender</div>';
        }elseif(empty($username)){ 
            $msg ='<div class="alert alert-danger" role="alert">Please enter your userame</div>';
        }elseif(empty($email)){ 
            $msg = '<div class="alert alert-danger" role="alert">Please enter your email</div>';
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){ 
            $msg ='<div class="alert alert-danger" role="alert">Please enter valid email</div>';
        }elseif(empty($_POST['password_1'])){ 
            $msg = '<div class="alert alert-danger" role="alert">Please enter your password</div>';
        }elseif(strlen($password) < 8) {   
            $msg = '<div class="alert alert-danger" role="alert">Password must be minimum of 8 characters</div>';
        }elseif(empty($_POST['password_2'])){ 
            $msg = '<div class="alert alert-danger" role="alert">Please confirm your password</div>';
        }elseif($_POST['password_1'] != $_POST['password_2']){ 
            $msg = '<div class="alert alert-danger" role="alert">Password does not match</div>';
        
        // Validate Image type
        }else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $msg = '<div class="alert alert-danger" role="alert">Choose Corred image, *.jpg .jpeg .png .gif</div>';
        // Validate Image Size
        }else if ($_FILES["profile"]["size"] > 500000) {
        $msg = '<div class="alert alert-danger" role="alert">Image size very big</div>';
        // Validate if this uploaded before or not
        }else if (file_exists($target)) {
        $msg = '<div class="alert alert-danger" role="alert">Sory this file was uploaded before</div>';  
        // validate username and email not registerd
        }else{
            // validate username and email not registerd
            $chick_username = mysqli_query($connection, "SELECT `username` FROM `users` WHERE `username` = '$username'");
            $chick_email = mysqli_query($connection, "SELECT `email` FROM `users` WHERE `email` = '$email'");
            if (mysqli_num_rows($chick_username) > 0) {
                    $msg = '<div class="alert alert-danger" role="alert">Sorry, but the username is already in use</div>';
                }elseif (mysqli_num_rows($chick_email) > 0) {
                    $msg = '<div class="alert alert-danger" role="alert">Sorry, but the email is already in use</div>';
        }else { 
        $stmt =  "INSERT INTO `users` (`fullname`,`country`,`gender`,`username`,`email`,`password`,`profile`,`role`,`user_status`,`scholarship_name`,`group_name`,`created_at`,`updated_at`)
                            VALUES ('$fullname','$country','$gender','$username','$email','$password','$profile','user','blocked','-','-',NOW(),NOW())";
        mysqli_query($connection,$stmt)or die(mysqli_error($connection)) ;

        if (move_uploaded_file($_FILES['profile']['tmp_name'], $target)) {
            if(isset($stmt)){
                $user_info = mysqli_query($connection,"SELECT * FROM `users` WHERE `username` = '$username'");
                $user = mysqli_fetch_assoc($user_info);
                $_SESSION['id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['country'] = $user['country'];
                $_SESSION['gender'] = $user['gender'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['profile'] = $user['profile'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_status'] = $user['user_status'];
                $msg = '<div class="alert alert-success" role="alert">You have been registered successfully.</div>
                <meta http-equiv="refresh"content="2; \'user-activation-form.php\' "/>';
            }
            }else{
            $msg = '<div class="alert alert-danger" role="alert">Sorry, an unexpected error occurred</div>';
        }
    }
    }
 }




