<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../../functions.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['task_id']) || empty($_POST['task_id'])) {
        echo "Task ID tidak ditemukan!";
        exit;
    }

    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['user_id'];

    $conn = connectDB();

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Task berhasil dihapus!";
        header("Location: mytask.php");
    } else {
        $_SESSION['error_message'] = "Gagal menghapus task!";
        header("Location: detail_task.php?id=$task_id");
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Akses tidak valid!";
    exit;
}
?>