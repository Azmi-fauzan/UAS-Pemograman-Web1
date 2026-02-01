<?php
include "../../backend/auth/check_session.php";
require "../../backend/config/database.php";

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

/* Query role-based */
if ($role === 'pusat') {
    $query = mysqli_query($conn, "
        SELECT a.waktu, a.aktivitas, u.username
        FROM activity_log a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.waktu DESC
        LIMIT 100
    ");
} else {
    $query = mysqli_prepare($conn, "
        SELECT waktu, aktivitas
        FROM activity_log
        WHERE user_id = ?
        ORDER BY waktu DESC
        LIMIT 100
    ");
    mysqli_stmt_bind_param($query, "i", $user_id);
    mysqli_stmt_execute($query);
    $query = mysqli_stmt_get_result($query);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Activity Log</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-secondary">
  <div class="container-fluid">
    <span class="navbar-brand">Activity Log Sistem</span>
    <a href="dashboard_<?= $role; ?>.php" class="btn btn-outline-light btn-sm">Kembali</a>
  </div>
</nav>

<div class="container mt-4">
  <h4 class="mb-3">Riwayat Aktivitas</h4>

  <div class="card">
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead class="table-light">
          <tr>
            <?php if ($role === 'pusat'): ?>
              <th>User</th>
            <?php endif; ?>
            <th>Aktivitas</th>
            <th>Waktu</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($query)) { ?>
          <tr>
            <?php if ($role === 'pusat'): ?>
              <td><?= $row['username']; ?></td>
            <?php endif; ?>
            <td><?= $row['aktivitas']; ?></td>
            <td><?= $row['waktu']; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if (mysqli_num_rows($query) == 0): ?>
    <div class="alert alert-info mt-3">
      Belum ada aktivitas tercatat.
    </div>
  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include "../partials/footer.php"; ?>
</body>
</html>