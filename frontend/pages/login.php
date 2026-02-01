<!DOCTYPE html>
<html>
<head>
  <title>Login - Franchise Finance System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">

  <!-- KONTEN LOGIN -->
  <div class="container flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="card p-4 shadow" style="width: 350px;">
      <h4 class="text-center mb-4">Login Sistem Keuangan</h4>

      <form action="../../backend/auth/login.php" method="POST">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
          Login
        </button>
      </form>
    </div>
  </div>

  <!-- FOOTER -->
  <?php include "../partials/footer.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>