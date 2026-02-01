<!DOCTYPE html>
<html>
<head>
  <title>Tambah Cabang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body  class="d-flex flex-column min-vh-100">

 <main class="flex-fill container my-4">
<h4>Tambah Cabang Baru</h4>

<form action="../../backend/cabang/store.php" method="POST">

  <div class="mb-3">
    <label>Nama Cabang</label>
    <input type="text" name="nama_cabang" class="form-control" required>
  </div>

  <div class="mb-3">
    <label>Lokasi</label>
    <input type="text" name="lokasi" class="form-control" required>
  </div>

  <div class="mb-3">
    <label>Username Cabang</label>
    <input type="text" name="username" class="form-control" required>
  </div>

  <div class="mb-3">
    <label>Password Awal</label>
    <input type="password" name="password" class="form-control" required>
  </div>

  <button class="btn btn-success">Simpan</button>
</form>
</main>
<?php include "../partials/footer.php"; ?>
</body>
</html>