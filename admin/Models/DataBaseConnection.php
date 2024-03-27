<?php
    $_host = "localhost";
    $_user = "root";
    $_password = null;
    $_DB_name = "portal";
    // initialize DB configration 
    $connection = mysqli_connect($_host,$_user,$_password,$_DB_name);
        if (!$connection) {
          echo mysqli_connect_error("Error connecting to the database");
        }else {
          echo "";
        }
        function close_DB(){
          global $connection;
          mysqli_close($connection);
        }
 ?>