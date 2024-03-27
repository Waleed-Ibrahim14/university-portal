<?php 
/*--------------------------------------------------------------------------
| Log-in Prosses::
|--------------------------------------------------------------------------*/
include_once("../Models/DataBaseConnection.php");

  // Initialize message variable
  $msg = "";

  if (isset($_POST['login'])) {
      // Use mysqli_real_escape_string function to avoid SQL injection attacks
      $email = stripcslashes(mysqli_real_escape_string($connection, $_POST['email']));
      $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  
      if (empty($_POST['email'])) {
          $msg = '<div class="alert alert-danger" role="alert">Please Enter Your Email</div>';
      } else if (empty($_POST['password'])) {
          $msg = '<div class="alert alert-danger" role="alert">Please Enter Your Password</div>';
      } else {
          // Retrieve user data based on the provided email
          $sql_select = "SELECT * FROM users WHERE email = '$email'";
          $result = mysqli_query($connection, $sql_select);
  
          if ($result && mysqli_num_rows($result) > 0) {
              $user = mysqli_fetch_assoc($result);
              $hashed_password = $user['password'];
  
              // Verify the password
              if (password_verify($_POST['password'], $hashed_password)) {
                  // Check if the user is not blocked
                  $user_status = $user['user_status'];
                  if ($user_status == 'active') {
                      // Set Session Variables
                      $_SESSION['id'] = $user['id'];
                      $_SESSION['fullname'] = $user['fullname'];
                      $_SESSION['country'] = $user['country'];
                      $_SESSION['gender'] = $user['gender'];
                      $_SESSION['username'] = $user['username'];
                      $_SESSION['email'] = $user['email'];
                      $_SESSION['profile'] = $user['profile'];
                      $_SESSION['role'] = $user['role'];
                      $_SESSION['user_status'] = $user_status;
                      $_SESSION['login'] = true;
                      // Redirect To Dashboard After Successful Login
                      $msg = '<div class="alert alert-success" role="alert">'.$_SESSION['username'].'  Logined Successfuly</div><meta http-equiv="refresh" content="3; \'dashboard.php\'" />';
                  } else {
                      $msg = '<div class="alert alert-danger" role="alert">Your account is blocked. Please contact support.</div>';
                  }
              } else {
                  $msg = '<div class="alert alert-danger" role="alert">Your password is wrong</div>';
              }
          } else {
              $msg = '<div class="alert alert-danger" role="alert">User not found</div>';
          }
      }
  }
  
?>