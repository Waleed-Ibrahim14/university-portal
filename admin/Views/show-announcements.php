<?php
session_start();
if ($_SESSION['role']  !== 'admin') { 
   include_once("../Models/DataBaseConnection.php");
    $msg= '';
    // Delete User 
    if (isset($_GET['delete'])) {
        $stmt = mysqli_query($connection, "DELETE FROM `announcements` WHERE `id` = '$_GET[delete]'");
        if (isset($stmt)) {
        $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">Announcement Deleted Successfuly</div>';
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
			            <h1 class="app-page-title mb-0">Show All Announcements</h1>
				    </div>
                    <div class="row g-4 mb-4 col-md-5">
                    <?php echo $msg; ?>
			        </div><!--//row-->
				    <div class="col-auto">
					     <div class="page-utilities">
						    <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                                <div class="col-auto">						    
								    <a class="btn app-btn-primary" href="create-announcements.php">
                                        Create Announcements
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
                    <th class="cell">Title</th>
                    <th class="cell">Message</th>
                    <th class="cell">Notify time</th>
                    <th class="cell">Notify Repete</th>
                    <th class="cell">notif loop</th>
                    <th class="cell">pulish date</th>
                    <th class="cell">username</th>
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
        $users = mysqli_query($connection, "SELECT * FROM `announcements` ORDER BY `id` DESC LIMIT $start_from , $per_page");
        $num = 1;
        if (mysqli_num_rows($users) > 0) {
        while ($show_notifi = mysqli_fetch_assoc($users)) {
        echo '<tr>
            <td class="cell">'.$show_notifi['id'].'</td>
            <td class="cell">'.$show_notifi['title'].'</td>
            <td class="cell">'.$show_notifi['notif_msg'].'</td>
            <td class="cell">'.$show_notifi['notif_time'].'</td>
            <td class="cell">'.$show_notifi['notif_repeat'].'</td>
            <td class="cell">'.$show_notifi['notif_loop'].'</td>
            <td class="cell">'.$show_notifi['publish_date'].'</td>
            <td class="cell">'.$show_notifi['username'].'</td>
            <td class="cell">'.$show_notifi['updated_at'].'</td>';
            echo '<td class="cell"><a href="show-announcements.php?delete='.$show_notifi['id'].'&page='.$page.'" class="btn btn-danger btn-sm">delete</i></a></td>';
            echo '</tr>';
            $num++;
        }
    } else {
        echo '<tr><td colspan="13" class="cell">No announcements found.</td></tr>';
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
    $page_sql = mysqli_query($connection, "SELECT * FROM `announcements`");
    $count_page = mysqli_num_rows($page_sql);
    $total_page = ceil($count_page / $per_page);
?>
<nav class="app-pagination">
    <ul class="pagination justify-content-center">
    <?php
        for ($i = 1; $i <= $total_page; $i++) {
            echo '<li class="page-item" '.($page == $i ? 'class="active"' : '').'><a class="page-link" href="show-announcements.php?page='.$i.'">'.$i.'</a></li>';
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