<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

/*--------------------------------------------------------------------------
| NOTE: The original logic (show page if NOT admin) is preserved.
|--------------------------------------------------------------------------*/
if ($current_role === 'admin') {
    include_once("../Models/DataBaseConnection.php");

    $msg = '';

    /*--------------------------------------------------------------------------
    | Update user status (Prepared Statement).
    |--------------------------------------------------------------------------*/
    if (isset($_GET['user_status'], $_GET['user'])) {
        $new_status = $_GET['user_status'];
        $user_id    = (int) $_GET['user'];

        if (in_array($new_status, ['active', 'blocked'], true) && $user_id > 0) {
            $upd = $connection->prepare("UPDATE users SET user_status = ? WHERE id = ?");
            $upd->bind_param("si", $new_status, $user_id);
            if ($upd->execute()) {
                $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">User Updated Successfully</div>';
            }
            $upd->close();
        }
    }

    /*--------------------------------------------------------------------------
    | Delete user (Prepared Statement).
    |--------------------------------------------------------------------------*/
    if (isset($_GET['delete'])) {
        $del_id = (int) $_GET['delete'];
        if ($del_id > 0) {
            $del = $connection->prepare("DELETE FROM users WHERE id = ?");
            $del->bind_param("i", $del_id);
            if ($del->execute()) {
                $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">User Deleted Successfully</div>';
            }
            $del->close();
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
			            <!-- Pluralized: "Teachers" -->
			            <h1 class="app-page-title mb-0">Show All Teachers</h1>
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
                    <th class="cell">Order</th>
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
                    <th class="cell">Actions</th>
                </tr>
            </thead>
            <tbody>
<?php
        /*--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------*/
        $per_page = 5;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $start_from = ($page - 1) * $per_page;

        /*--------------------------------------------------------------------------
        | Main query: JOIN with roles AND scholarships.
        | Previously filtered by the (now removed) `role` text column and read
        | `scholarship_name` (also removed). Now uses role_name + scholarship name.
        |--------------------------------------------------------------------------*/
        $sql = "SELECT
                    u.*,
                    r.role_name          AS role_name,
                    s.scholarship_name   AS scholarship_display_name
                FROM users u
                LEFT JOIN roles r        ON u.role_id        = r.id
                LEFT JOIN scholarships s ON u.scholarship_id = s.id
                WHERE r.role_name = 'teacher'
                ORDER BY u.id DESC
                LIMIT " . (int)$start_from . ", " . (int)$per_page;

        $users = mysqli_query($connection, $sql);

        if ($users && mysqli_num_rows($users) > 0) {
            while ($show_user = mysqli_fetch_assoc($users)) {
                echo '<tr>
                    <td class="cell">'.$show_user['id'].'</td>
                    <td class="cell"><img src="../../assets/images/users/'.$show_user['profile'].'" class="img-rounded" width="30px"/></td>
                    <td class="cell">'.htmlspecialchars($show_user['fullname']).'</td>
                    <td class="cell">'.htmlspecialchars($show_user['country']).'</td>
                    <td class="cell">'.($show_user['gender'] == 'male' ? '<i class="fa fa-male"></i>' : '<i class="fa fa-female"></i>').'</td>
                    <td class="cell">'.htmlspecialchars($show_user['username']).'</td>
                    <td class="cell">'.htmlspecialchars($show_user['email']).'</td>
                    <td class="cell">'.htmlspecialchars($show_user['scholarship_display_name'] ?? '—').'</td>
                    <td class="cell">'.htmlspecialchars($show_user['role_name'] ?? '—').'</td>
                    <td class="cell">'.htmlspecialchars($show_user['user_status']).'</td>
                    <td class="cell">'.$show_user['created_at'].'</td>
                    <td class="cell">'.$show_user['updated_at'].'</td>';

                if ($current_role !== 'user') {
                    echo '<td class="cell">'
                        . ($show_user['user_status'] == 'blocked'
                            ? '<a href="show-teachers.php?user_status=active&user='.$show_user['id'].'&page='.$page.'" class="btn btn-success btn-sm"><span>Activate</span></a>'
                            : '<a href="show-teachers.php?user_status=blocked&user='.$show_user['id'].'&page='.$page.'" class="btn btn-info btn-sm">block</a>')
                        . '</td>
                          <td class="cell"><a href="show-teachers.php?delete='.$show_user['id'].'&page='.$page.'" class="btn btn-danger btn-sm">delete</a></td>';
                } else {
                    echo '<td class="cell"></td>';
                    echo '<td class="cell"></td>';
                }
                echo '</tr>';
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
    /*--------------------------------------------------------------------------
    | Pagination count: matches the main query (JOIN + role filter).
    | FIXED: previously used role = 'teacher' but linked to show-users.php.
    |--------------------------------------------------------------------------*/
    $page_sql = mysqli_query(
        $connection,
        "SELECT COUNT(*) AS total
         FROM users u
         LEFT JOIN roles r ON u.role_id = r.id
         WHERE r.role_name = 'teacher'"
    );
    $count_page = $page_sql ? (int)mysqli_fetch_assoc($page_sql)['total'] : 0;
    $total_page = (int) ceil($count_page / $per_page);
?>
     <nav class="app-pagination">
          <ul class="pagination justify-content-center">
            <?php
                for ($i = 1; $i <= $total_page; $i++) {
                    $active = ($page == $i) ? ' active' : '';
                    /* FIXED: was linking to show-users.php */
                    echo '<li class="page-item'.$active.'"><a class="page-link" href="show-teachers.php?page='.$i.'">'.$i.'</a></li>';
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
</div><!--//app-wrapper-->

<?php
    include_once("../includes/footer.php");
} else {
    header("Location:login.php");
}
?>
