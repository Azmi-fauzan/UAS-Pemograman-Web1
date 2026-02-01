<?php
function logActivity($conn, $user_id, $aktivitas) {
    $query = mysqli_prepare(
        $conn,
        "INSERT INTO activity_log (user_id, aktivitas) VALUES (?, ?)"
    );
    mysqli_stmt_bind_param($query, "is", $user_id, $aktivitas);
    mysqli_stmt_execute($query);
}