<?php
session_start();
include_once(__DIR__ . '/Push.php');
$push = new Push();
?>
<div class="container">		
	<div class="row">
		<div class="col-sm-6">
			<form method="post" action="">
				<table class="table borderless">
					<tr>
						<td>Title</td>
						<td><input type="text" name="title" class="form-control" required></td>
					</tr>	
					<tr>
						<td>Message</td>
						<td><textarea name="msg" cols="50" rows="4" class="form-control" required></textarea></td>
					</tr>			
					<tr>
						<td>Broadcast time</td>
						<td><select name="time" class="form-control"><option>Now</option></select></td>
					</tr>
					<tr>
						<td>Loop (time)</td>
						<td>
							<select name="loops" class="form-control">
								<?php for ($i = 1; $i <= 5; $i++): ?>
									<option value="<?= $i ?>"><?= $i ?></option>
								<?php endfor; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Loop Every (Minute)</td>
						<td>
							<select name="loop_every" class="form-control">
								<?php for ($i = 1; $i <= 60; $i++): ?>
									<option value="<?= $i ?>"><?= $i ?></option>
								<?php endfor; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>For</td>
						<td>
							<select name="user" class="form-control" required>
								<option value="">-- Select --</option>
								<?php 
								$users = $push->listUsers();
								foreach ($users as $key) {
									echo '<option value="' . htmlspecialchars($key['username']) . '">'
									   . htmlspecialchars($key['username']) . '</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<button name="submit" type="submit" class="btn btn-info">Add Message</button>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>

	<?php 
	if (isset($_POST['submit'])) {
		$title      = trim($_POST['title'] ?? '');
		$msg        = trim($_POST['msg'] ?? '');
		$loop       = (int) ($_POST['loops'] ?? 0);
		$loop_every = (int) ($_POST['loop_every'] ?? 0);
		$user       = trim($_POST['user'] ?? '');
		$time       = date('Y-m-d H:i:s');

		if (empty($title) || empty($msg) || empty($loop) || empty($loop_every) || empty($user)) {
			echo '<div class="alert alert-danger">Please complete all fields</div>';
		} else {
			$isSaved = $push->saveNotification($title, $msg, $time, $loop, $loop_every, $user);
			if ($isSaved === true) {
				echo '<div class="alert alert-success">New notification saved</div>';
			} else {
				echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($isSaved) . '</div>';
			}
		}
	}
	?>

	<h3>Notifications List:</h3>
	<table class="table">
		<thead>
			<tr>
				<th>No</th>
				<th>Next Schedule</th>
				<th>Title</th>
				<th>Message</th>
				<th>Remains</th>
				<th>User</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$a = 1;
			$notifList = $push->listNotification();
			foreach ($notifList as $key):
			?>
			<tr>
				<td><?= $a++ ?></td>
				<td><?= htmlspecialchars($key['notif_time']) ?></td>
				<td><?= htmlspecialchars($key['title']) ?></td>
				<td><?= htmlspecialchars($key['notif_msg']) ?></td>
				<td><?= (int)$key['notif_loop'] ?></td>
				<td><?= htmlspecialchars($key['username']) ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php include_once(__DIR__ . '/../footer.php'); ?>
