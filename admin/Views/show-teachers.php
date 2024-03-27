<?php
session_start();
if ($_SESSION['role']  !== 'admin') { 
   include_once("../Models/DataBaseConnection.php");
    // Delete User Where User Id => userId
    $msg= '';
    if (isset($_GET['user_status']) AND isset($_GET['user'])) {
        $sql = mysqli_query($connection, "UPDATE `users` SET `user_status` = '$_GET[user_status]' WHERE `id` = '$_GET[user]'");
            if (isset($sql)) {
            $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">User Updated Successfuly</div>';
        }
    }
    // Delete User 
    if (isset($_GET['delete'])) {
        $sql = mysqli_query($connection, "DELETE FROM `users` WHERE `id` = '$_GET[delete]'");
        if (isset($sql)) {
        $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">User Deleted Successfuly</div>';
        }
    }
               
	include_once("../includes/header.php");
?>

<body class="app">   	
<?php include_once("../includes/sidepanel.php"); ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <div class="row g-3 mb-4 align-items-center justify-content-between">
				    <div class="col-auto">
			            <h1 class="app-page-title mb-0">Show All Teacher</h1>
				    </div>
                    <div class="row g-4 mb-4 col-md-5">
                    <?php echo $msg; ?>
			        </div><!--//row-->
				    <div class="col-auto">
					     <div class="page-utilities">
						    <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                                <div class="col-auto">						    
								    <a class="btn app-btn-primary" href="#">
                                        Create Teacher
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-add" viewBox="0 0 16 16">
  <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
  <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
</svg>
									</a>
							    </div>
						    </div><!--//row-->
					    </div><!--//table-utilities-->
				    </div><!--//col-auto-->
			    </div><!--//row-->

				
				
<div class="tab-content" id="orders-table-tab-content">
<div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
<div class="app-card app-card-orders-table shadow-sm mb-5">
<div class="app-card-body">
    <div class="table-responsive">
        <table class="table app-table-hover mb-0 text-left">
            <thead>
                <tr>
                    <th class="cell" >Order</th>
                    <th class="cell">Profile</th>
                    <th class="cell">full name</th>
                    <th class="cell">Country</th>
                    <th class="cell">gender</th>
                    <th class="cell">username</th>
                    <th class="cell">email</th>
                    <th class="cell">Scholarship Name</th>
                    <th class="cell">role</th>
                    <th class="cell">Status</th>
                    <th class="cell">Created at</th>
                    <th class="cell">Updated at</th>
                    <th class="cell" >Actions</th>
                </tr>
            </thead>
            <tbody>
<?php
        // Pagination
        $per_page = 5;
        if (!isset($_GET['page'])) {
          $page = 1;
        }else {
          $page = (int)$_GET['page'];
        }
        $start_from = ($page-1) * $per_page;
        
        //GET All users with role Teacher
        $users = mysqli_query($connection, "SELECT * FROM `users` WHERE `role` = 'teacher' ORDER BY `id` DESC LIMIT $start_from , $per_page");
        $num = 1;
        if (mysqli_num_rows($users) > 0) {
        while ($show_user = mysqli_fetch_assoc($users)) {
        echo '<tr>
            <td class="cell">'.$show_user['id'].'</td>
            <td class="cell"><img src="../../assets/images/users/'.$show_user['profile'].'" class="img-rounded" width="30px"/></td>
            <td class="cell">'.$show_user['fullname'].'</td>
            <td class="cell">'.$show_user['country'].'</td>
            <td class="cell">'.($show_user['gender'] == 'male' ? '<i class="fa fa-male"><i/>' : '<i class="fa fa-female"><i/>' ).'</td>        
            <td class="cell">'.$show_user['username'].'</td>
            <td class="cell">'.$show_user['email'].'</td>
            <td class="cell">'.$show_user['scholarship_name'].'</td>
            <td class="cell">'.$show_user['role'].'</td>
            <td class="cell">'.$show_user['user_status'].'</td>
            <td class="cell">'.$show_user['created_at'].'</td>
            <td class="cell">'.$show_user['updated_at'].'</td>';
            if (!$_SESSION['role']  == 'user') {
                echo '<td class="cell">'.($show_user['user_status'] == 'blocked' ? '<a href="show-teachers.php?user_status=active&user='.$show_user['id'].'&page='.$page.'" class="btn btn-success btn-sm"><span>Activat</span></a>' : '<a href="show-teachers.php?user_status=blocked&user='.$show_user['id'].'&page='.$page.'" class="btn btn-info btn-sm">block</a>').'</td>
                      <td class="cell"><a href="show-teachers.php?delete='.$show_user['id'].'&page='.$page.'" class="btn btn-danger btn-sm">delete</i></a></td>';
            }else{
                echo '<td class="cell"><kfjlsk</td>';          
                echo '<td class="cell"><i class="bi bi-trash3"></i></td>';
                }
                echo '</tr>';
            $num++;
        }
    } else {
        echo '<tr><td colspan="13" class="cell">No teacher found.</td></tr>';
    }
?>
		
										</tbody>
									</table>
</div><!--//table-responsive-->
</div><!--//app-card-body-->		
</div><!--//app-card-->
<!--------------------------------------------------
|   Pagination
--------------------------------------------------->
<?php
    $page_sql = mysqli_query($connection, "SELECT * FROM `users` WHERE `role` = 'teacher'");
    $count_page = mysqli_num_rows($page_sql);
    $total_page = ceil($count_page / $per_page);
?>
     <nav class="app-pagination">
          <ul class="pagination justify-content-center">
            <?php
                for ($i = 1; $i <= $total_page; $i++) {
                  echo '<li class="page-item" '.($page == $i ? 'class="active"' : '').'><a class="page-link" href="show-users.php?page='.$i.'">'.$i.'</a></li>';
                }
            ?>
          </ul>
     </nav><!--//app-pagination-->
<!--------------------------------------------------
|   Pagination
--------------------------------------------------->
						        
									</div><!--//tab-content-->
				
				
			    
		    </div><!--//container-fluid-->
	    </div><!--//app-content-->
	    
<?php
	include_once("../includes/footer.php");	
}else{
	header("Location:login.php");
}
?>
