<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role !== 'admin') {
    include_once("../Models/DataBaseConnection.php");

    $msg = '';

    /*--------------------------------------------------------------------------
    | Delete referral (Prepared Statement).
    |--------------------------------------------------------------------------*/
    if (isset($_GET['delete'])) {
        $del_id = (int) $_GET['delete'];
        if ($del_id > 0) {
            $del = $connection->prepare("DELETE FROM referrals WHERE id = ?");
            $del->bind_param("i", $del_id);
            if ($del->execute()) {
                $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">Referral Deleted Successfully</div>';
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
			            <h1 class="app-page-title mb-0">Show All Referrals</h1>
				    </div>
                    <div class="row g-4 mb-4 col-md-5">
                    <?php echo $msg; ?>
			        </div><!--//row-->
				    <div class="col-auto">
					     <div class="page-utilities">
						    <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                                <div class="col-auto">						    
								    <a class="btn app-btn-primary" href="create-referral.php">
                                        Create Referral
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-add" viewBox="0 0 16 16">
  <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0 4 2 2 0 0 0 0 4"/>
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
                <th class="cell">Student</th>
                <th class="cell">Referral Reason</th>
                <th class="cell">Date</th>
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
        | Main query: referrals + student's full name.
        | JOIN referrals.student_id → users.id to display the student's fullname.
        |--------------------------------------------------------------------------*/
        $referralsQuery = mysqli_query(
            $connection,
            "SELECT r.*, u.fullname AS student_name
             FROM referrals r
             LEFT JOIN users u ON r.student_id = u.id
             ORDER BY r.id DESC
             LIMIT " . (int)$start_from . ", " . (int)$per_page
        );

        if ($referralsQuery && mysqli_num_rows($referralsQuery) > 0) {
            while ($referral = mysqli_fetch_assoc($referralsQuery)) {
                // Fallback: if the JOIN fails (orphan FK), show numeric ID
                $student_display = $referral['student_name']
                    ?? ('ID: ' . $referral['student_id']);

                echo '<tr>
                    <td class="cell">'.(int)$referral['id'].'</td>
                    <td class="cell">'.htmlspecialchars($student_display).'</td>
                    <td class="cell">'.htmlspecialchars($referral['referral_reason'] ?? '—').'</td>
                    <td class="cell">'.htmlspecialchars($referral['date'] ?? '—').'</td>
                    <td class="cell">'.$referral['created_at'].'</td>
                    <td class="cell">'.$referral['updated_at'].'</td>';

                echo '<td class="cell">'
                    . '<a href="Referrals.php?delete='.(int)$referral['id'].'&page='.$page.'" class="btn btn-danger btn-sm">delete</a>'
                    . '</td>';

                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="7" class="cell">No referrals found.</td></tr>';
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
    $page_sql = mysqli_query($connection, "SELECT COUNT(*) AS total FROM `referrals`");
    $count_page = $page_sql ? (int)mysqli_fetch_assoc($page_sql)['total'] : 0;
    $total_page = (int) ceil($count_page / $per_page);
?>
     <nav class="app-pagination">
          <ul class="pagination justify-content-center">
            <?php
                for ($i = 1; $i <= $total_page; $i++) {
                    $active = ($page == $i) ? ' active' : '';
                    echo '<li class="page-item'.$active.'"><a class="page-link" href="Referrals.php?page='.$i.'">'.$i.'</a></li>';
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
	header("Location: login.php");
	exit;
}
?>
