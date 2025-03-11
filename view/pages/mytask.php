<?php
session_start();
include '../../functions.php';

$conn = connectDB(); 

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please login first.");
}

$user_id = $_SESSION['user_id']; // Ambil ID user dari session

$sql = "
    SELECT tasks.id, tasks.task, tasks.status, users.username 
    FROM tasks 
    JOIN users ON tasks.user_id = users.id
    WHERE tasks.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);  // Gunakan bind_param untuk keamanan
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Task</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="../../assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">My Task</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <a href="/view/pages/addtask.php" class="btn btn-sm btn-outline-primary">Add</a>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Created By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            if ($result->num_rows > 0) {
                                                $no = 1;
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . $no++ . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['task']) . "</td>"; // Menampilkan task dengan aman
                                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                                    echo "<td>
                                                        <a href='/view/pages/detailtask.php?id=" . $row['id'] . "' class='btn btn-sm w-auto m-1 btn-outline-info'>Detail</a>
                                                        <a href='/view/pages/updatetask.php?id=" . $row['id'] . "' class='btn btn-sm w-auto m-1 btn-outline-warning'>Edit</a>

                                                        <form action='/view/pages/deletetask.php' method='POST' onsubmit='return confirm(\"Are you sure delete this task?\")' '>
                                                            <input type='hidden' name='task_id' value='" . $row['id'] . "'>
                                                            <button type='submit' class='btn btn-sm w-auto m-1 btn-outline-danger'>Delete</button>
                                                        </form>
                                                    </td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='5' class='text-center'>No tasks found</td></tr>";
                                            }
                                        ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>              

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>