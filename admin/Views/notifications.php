<?php
session_start();

$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role !== 'admin') {
    include_once("../includes/header.php");
    include_once(__DIR__ . "/../Models/DataBaseConnection.php");
    
    $msg = '';
    
    // Delete notification
    if (isset($_GET['delete'])) {
        $del_id = (int) $_GET['delete'];
        if ($del_id > 0) {
            $del = $connection->prepare("DELETE FROM announcements WHERE id = ?");
            $del->bind_param("i", $del_id);
            if ($del->execute()) {
                $msg = '<div class="alert alert-success">Notification deleted</div>';
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
			    <h1 class="app-page-title mb-3">Notifications</h1>
                <?php echo $msg; ?>
                
                <?php
                // Pagination
                $per_page = 5;
                $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                $start_from = ($page - 1) * $per_page;
                
                $query = mysqli_query(
                    $connection,
                    "SELECT * FROM announcements ORDER BY id DESC LIMIT " . (int)$start_from . ", " . (int)$per_page
                );
                
                if ($query && mysqli_num_rows($query) > 0) {
                    while ($notif = mysqli_fetch_assoc($query)) {
                        $badge_class = 'bg-info';
                        // Simple heuristic based on notif_loop
                        if ($notif['notif_loop'] > 3) $badge_class = 'bg-warning';
                        elseif ($notif['notif_loop'] > 0) $badge_class = 'bg-success';
                ?>
                
                <div class="app-card app-card-notification shadow-sm mb-4">
                    <div class="app-card-header px-4 py-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-lg-auto text-center text-lg-start">
                                <div class="app-icon-holder">
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-bell" fill="currentColor">
                                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2z"/>
                                        <path fill-rule="evenodd" d="M8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="col-12 col-lg-auto text-center text-lg-start">
                                <div class="notification-type mb-2">
                                    <span class="badge <?php echo $badge_class; ?>">Notification</span>
                                </div>
                                <h4 class="notification-title mb-1"><?php echo htmlspecialchars($notif['title']); ?></h4>
                                <ul class="notification-meta list-inline mb-0">
                                    <li class="list-inline-item"><?php echo htmlspecialchars($notif['notif_time']); ?></li>
                                    <li class="list-inline-item">|</li>
                                    <li class="list-inline-item"><?php echo htmlspecialchars($notif['username']); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="app-card-body p-4">
                        <div class="notification-content"><?php echo htmlspecialchars($notif['notif_msg']); ?></div>
                    </div>
                    <div class="app-card-footer px-4 py-3">
                        <a class="action-link text-danger" href="notifications.php?delete=<?php echo (int)$notif['id']; ?>&page=<?php echo $page; ?>">Delete</a>
                    </div>
                </div>
                
                <?php
                    }
                } else {
                    echo '<div class="alert alert-info">No notifications found.</div>';
                }
                ?>
                
                <!-- Pagination -->
                <?php
                $countQuery = mysqli_query($connection, "SELECT COUNT(*) AS total FROM announcements");
                $countRow = mysqli_fetch_assoc($countQuery);
                $total_page = (int) ceil($countRow['total'] / $per_page);
                ?>
                <nav class="app-pagination">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_page; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="notifications.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
		    </div>
	    </div>
	    
<?php
    include_once("../includes/footer.php");
} else {
    header("Location: login.php");
    exit;
}
?>
