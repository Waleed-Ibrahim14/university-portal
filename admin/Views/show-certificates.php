<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role !== 'admin') {
    include_once("../includes/header.php");
    include_once("../Models/DataBaseConnection.php");

    $msg = '';

    /*--------------------------------------------------------------------------
    | Update user status (Prepared Statement).
    | NOTE: Inherited from show-users.php; no UI trigger on this page.
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
    | Delete certificate (Prepared Statement).
    |--------------------------------------------------------------------------*/
    if (isset($_GET['delete'])) {
        $del_id = (int) $_GET['delete'];
        if ($del_id > 0) {
            $del = $connection->prepare("DELETE FROM certificates WHERE id = ?");
            $del->bind_param("i", $del_id);
            if ($del->execute()) {
                $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">Certificate Deleted Successfully</div>';
            }
            $del->close();
        }
    }
?>

<body class="app">   	
<?php include_once("../includes/sidepanel.php"); ?>
<div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <div class="row g-3 mb-4 align-items-center justify-content-between">
				    <div class="col-auto">
			            <h1 class="app-page-title mb-0">Show All Certifications</h1>
				    </div>
                    <div class="row g-4 mb-4 col-md-5">
                    <?php echo $msg; ?>
			        </div><!--//row-->
				    <div class="col-auto">
					     <div class="page-utilities">
						    <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                                <div class="col-auto">						    
								    <a class="btn app-btn-primary" href="create-certificate.php">
                                        Create Certificate
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
			    </div><!--//row-->
			    
			    <div class="row g-4">
<?php
    /*--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------*/
    $per_page = 5;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $start_from = ($page - 1) * $per_page;

    /*--------------------------------------------------------------------------
    | Main query: certificates + student name + teacher name + course name.
    |
    | Uses 3 LEFT JOINs:
    |   - u_student ON c.student_id = u_student.id
    |   - u_teacher ON c.teacher_id = u_teacher.id
    |   - co        ON c.course_id  = co.id
    |--------------------------------------------------------------------------*/
    $sql = "SELECT 
                c.*,
                u_student.username  AS student_username,
                u_teacher.username  AS teacher_username,
                co.course_name      AS course_name
            FROM certificates c
            LEFT JOIN users u_student   ON c.student_id = u_student.id
            LEFT JOIN users u_teacher   ON c.teacher_id = u_teacher.id
            LEFT JOIN courses co        ON c.course_id  = co.id
            ORDER BY c.id DESC
            LIMIT " . (int)$start_from . ", " . (int)$per_page;

    $users = mysqli_query($connection, $sql);

    if ($users && mysqli_num_rows($users) > 0) {
        while ($show_user = mysqli_fetch_assoc($users)) {
            $certificate_file = $show_user['certificate_file'];
            $temp_pdf_path    = "../../assets/images/certificates/" . basename($certificate_file);

            // File type and size (safe: check existence first)
            $imageFileType = strtolower(pathinfo($temp_pdf_path, PATHINFO_EXTENSION));
            $filesize = file_exists($temp_pdf_path) ? filesize($temp_pdf_path) : 0;

            echo '
            <div class="col-6 col-md-4 col-xl-3 col-xxl-2">
                <div class="app-card app-card-doc shadow-sm h-100">
                    <div class="app-card-thumb-holder p-4">
                        <span class="icon-holder">
                            <i class="fas fa-file-pdf pdf-file"></i>
                        </span>
                            <a class="app-card-link-mask" href="../../assets/images/certificates/'.htmlspecialchars($certificate_file).'"></a>
                    </div>
                    <div class="app-card-body p-3 has-card-actions">
                        
                        <h4 class="app-doc-title truncate mb-0"><a href="#file-link">'.htmlspecialchars($show_user['certificate_name']).'</a></h4>
                        <div class="app-doc-meta">
                            <ul class="list-unstyled mb-0">
                                <li><span class="text-muted">Type:</span> '.htmlspecialchars($imageFileType).'</li>
                                <li><span class="text-muted">Size:</span> '.number_format($filesize / 1024, 1).' KB</li>
                                <li><span class="text-muted">Student:</span> '.htmlspecialchars($show_user['student_username'] ?? '—').'</li>
                                <li><span class="text-muted">Teacher:</span> '.htmlspecialchars($show_user['teacher_username'] ?? '—').'</li>
                                <li><span class="text-muted">Course:</span> '.htmlspecialchars($show_user['course_name'] ?? '—').'</li>
                                <li><span class="text-muted">Issue Date:</span> '.htmlspecialchars($show_user['issue_date'] ?? '—').'</li>
                                <li><span class="text-muted">Created: </span>'.$show_user['created_at'].'</li>
                            </ul>
                        </div>
                        
            <div class="app-card-actions">
                <div class="dropdown">
                    <div class="dropdown-toggle no-toggle-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-three-dots-vertical" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                </svg>
                                </div>
                        <ul class="dropdown-menu">
                        
                        <li><a class="dropdown-item" href="update-certificate.php?certificateId='.(int)$show_user['id'].'"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil me-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                        </svg>Edit</a></li>
                        <li><hr class="dropdown-divider"></li>

                        <li><a class="dropdown-item" href="show-certificates.php?delete='.(int)$show_user['id'].'&page='.$page.'">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash me-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                        </svg>Delete</a>
                        </li>
                    </ul>
                            </div>
                        </div>
                            
                    </div>

                </div>
            </div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">No certificate found</div>';
    }
    ?>	  
    <!--------------------------------------------------
    |   Pagination
    --------------------------------------------------->
    <?php
    $page_sql = mysqli_query($connection, "SELECT COUNT(*) AS total FROM `certificates`");
    $count_page = $page_sql ? (int)mysqli_fetch_assoc($page_sql)['total'] : 0;
    $total_page = (int) ceil($count_page / $per_page);
    ?>
    <nav class="app-pagination">
    <ul class="pagination justify-content-center">
    <?php
    for ($i = 1; $i <= $total_page; $i++) {
        $active = ($page == $i) ? ' active' : '';
        echo '<li class="page-item'.$active.'"><a class="page-link" href="show-certificates.php?page='.$i.'">'.$i.'</a></li>';
    }
    ?>
    </ul>
    </nav><!--//app-pagination-->
    <!--------------------------------------------------
    |   Pagination
    --------------------------------------------------->  			    
		    </div><!--//container-fluid-->
	    </div><!--//app-content-->
	    
<?php
	include_once("../includes/footer.php");	
} else {
    header("Location: login.php");
    exit;
}
?>
