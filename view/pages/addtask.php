<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../../functions.php';

class Task {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addTask($userId, $task, $status) {
        if (empty($task) || empty($status)) {
            return "Tugas dan status tidak boleh kosong!";
        }
        
        $stmt = $this->conn->prepare("INSERT INTO tasks (user_id, task, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iss", $userId, $task, $status);
        
        if ($stmt->execute()) {
            header("Location: /view/pages/mytask.php");
            exit;
        } else {
            return "Gagal menambahkan tugas!";
        }
        
        $stmt->close();
    }
}

$conn = connectDB();
$taskHandler = new Task($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $taskHandler->addTask($_SESSION['user_id'], trim($_POST['task']), trim($_POST['status']));
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add My Task</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="../../assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a href="/view/pages/mytask.php" class="navbar-brand">BPJS Productivity Hub</a>
        </div>
    </nav>

    <div class="wrapper">
        <nav id="sidebar" class="bg-light sidebar">
            <div class="text-center py-3">
                <img src="../../assets/images/favicon.png" alt="Logo" class="img-fluid" style="max-width: 80px;">
            </div>
            <div class="list-group">
                <a href="/view/pages/dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="/view/pages/mytask.php" class="list-group-item list-group-item-action active">My Task</a>
            </div>
        </nav>

        <div id="content">
            <div class="overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-12 col-xl-12 mb-4 mb-lg-0">
                        <div class="card">
                            <div class="card-header bg-primary text-white">Add Task</div>
                            <form method="post">
                                <div class="card-body">
                                    <?php if (isset($message)) echo "<div class='alert alert-danger'>$message</div>"; ?>
                                    <div class="m-2">
                                        <label for="task" class="form-label">My Task</label>
                                        <input type="text" class="form-control" name="task" required>
                                    </div>
                                    <div class="m-2">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" name="status" required>
                                            <option value="Pending">Pending</option>
                                            <option value="On Progress">On Progress</option>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-end">
                                    <a href="mytask.php" class="btn btn-primary m-1">Cancel</a>
                                    <button type="submit" class="btn btn-success btn-sm w-auto m-1">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>              
</body>
</html>