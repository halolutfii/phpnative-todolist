<?php
session_start();
include '../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

class Task {
    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    public function getTask($task_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT task, status FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    public function updateTask($task_id, $user_id, $task, $status) {
        $stmt = $this->conn->prepare("UPDATE tasks SET task = ?, status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $task, $status, $task_id, $user_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Task ID tidak ditemukan!");
}

$task_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$taskHandler = new Task();
$taskData = $taskHandler->getTask($task_id, $user_id);

if (!$taskData) {
    die("Task tidak ditemukan!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task = trim($_POST['task']);
    $status = trim($_POST['status']);

    if (!empty($task) && !empty($status)) {
        if ($taskHandler->updateTask($task_id, $user_id, $task, $status)) {
            header("Location: mytask.php");
            exit;
        } else {
            echo "Gagal memperbarui tugas!";
        }
    } else {
        echo "Tugas dan status tidak boleh kosong!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update My Task</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="../../assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Start Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a href="/view/pages/mytask.php" class="navbar-brand">BPJS Productivity Hub</a>
        </div>
    </nav>
    <!-- End Header -->

    <div class="wrapper">
        <!-- Start Sidebar -->
        <nav id="sidebar" class="bg-light sidebar">
            <div class="text-center py-3">
                <img src="../../assets/images/favicon.png" alt="Logo" class="img-fluid" style="max-width: 80px;">
            </div>
            <div class="list-group">
                <a href="/view/pages/dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="/view/pages/mytask.php" class="list-group-item list-group-item-action active">My Task</a>
            </div>
        </nav>
        <!-- End Sidebar -->

        <!-- Start Content -->
        <div id="content">
            <div class="overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-12 col-xl-12 mb-4 mb-lg-0">
                        <div class="card">
                            <div class="card-header bg-primary text-white">Update Task</div>
                            <form method="post">
                                <div class="card-body">
                                    <div class="m-2">
                                        <label for="task" class="form-label">My Task</label>
                                        <input type="text" class="form-control" name="task" required value="<?= htmlspecialchars($taskData['task']) ?>">
                                    </div>
                                    <div class="m-2">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" name="status" required>
                                            <option value="Pending" <?= $taskData['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="On Progress" <?= $taskData['status'] == 'On Progress' ? 'selected' : '' ?>>On Progress</option>
                                            <option value="Completed" <?= $taskData['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-end">
                                    <a href="mytask.php" class="btn btn-primary m-1">Cancel</a>
                                    <button type="submit" class="btn btn-success btn-sm w-auto m-1">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Content -->
    </div>              
</body>
</html>