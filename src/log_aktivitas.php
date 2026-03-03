<?php
include "koneksi.php";

$result = mysqli_query($conn, 
"SELECT l.*, u.username 
 FROM log_aktivitas l 
 JOIN users u ON l.user_id=u.id 
 ORDER BY l.id DESC");
?>
<h2>Log Aktivitas</h2>
<table border="1" cellpadding="6">
<tr><th>User</th><th>Aktivitas</th><th>Waktu</th></tr>

<?php while($l = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $l['username'] ?></td>
    <td><?= $l['aktivitas'] ?></td>
    <td><?= $l['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>
