<?php
require 'koneksi.php';

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {
        $task = $_POST['task'];
        $duedate = $_POST['duedate'];
        $description = $_POST['description'];

        if (empty($task) && empty($duedate) && empty($description)) {
            $error = "No task and date added";
        } elseif (empty($duedate)) {
            $error = "No date added";
        } elseif (empty($task)) {
            $error = "No task added";
        } elseif (empty($description)) {
            $error = "No description added";
        } else {
            $stmt = $koneksi->prepare("INSERT INTO todo (task, duedate, email, description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $task, $duedate, $email, $description);
            $stmt->execute();
            $stmt->close();
            header('Location: index.php');
            exit();
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $task = $_POST['task'];
        $duedate = $_POST['duedate'];
        $description = $_POST['description'];

        $stmt = $koneksi->prepare("UPDATE todo SET task = ?, duedate = ?, description = ? WHERE id_todo = ?");
        $stmt->bind_param('sssi', $task, $duedate, $description, $id);
        $stmt->execute();
        $stmt->close();
        header('Location: index.php');
        exit();
    } elseif (isset($_POST['deleteall'])) {
        $koneksi->query("DELETE FROM todo WHERE email = '$email'");
        header("Location: index.php");
        exit();
    }
}

// Handle task deletions
if (isset($_GET['deletetask'])) {
    $id = $_GET['deletetask'];
    $stmt = $koneksi->prepare("DELETE FROM todo WHERE id_todo = ? AND email = ?");
    $stmt->bind_param('is', $id, $email);
    $stmt->execute();
    $stmt->close();
    header('Location: index.php');
    exit();
}

// Handle task status changes
if (isset($_GET['statustask'])) {
    $id = $_GET['statustask'];
    $stmt = $koneksi->prepare("UPDATE todo SET status = CASE WHEN status = 'pending' THEN 'completed' ELSE 'pending' END WHERE id_todo = ? AND email = ?");
    $stmt->bind_param('is', $id, $email);
    $stmt->execute();
    $stmt->close();
    header('Location: index.php');
    exit();
}

// Handle task updates
$data = null;
if (isset($_GET['updatetask'])) {
    $id = $_GET['updatetask'];
    $stmt = $koneksi->prepare("SELECT * FROM todo WHERE id_todo = ? AND email = ?");
    $stmt->bind_param('is', $id, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}

// Fetch tasks
$stmt = $koneksi->prepare("SELECT * FROM todo WHERE email = ? ORDER BY duedate ASC");
$stmt->bind_param('s', $email);
$stmt->execute();
$tambah = $stmt->get_result();
$stmt->close();

$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <title>To Do List</title>
</head>
<body>

    <!-- Header -->
    <header class="container-fluid position-fixed">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <!-- <a class="navbar-brand" href="#">LOGO</a> -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link mx-3 my-2" href="index.php">To-do List</a>
                        </li>
                    </ul>
                    <!-- <button class="button my-2" type="submit">Profile</button> -->
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container d-flex flex-column align-items-center">
            <form action="" method="post" class="w-100" id="form">
                <div class="judul">
                    <h3 class="fs-1 fw-bold text-center">To Do List</h3>
                </div>
                
                <div class="input-section d-flex justify-content-center mt-3">
                    <input type="hidden" name="id" value="<?php echo $data['id_todo'] ?? ''; ?>">
                    <input type="text" name="task" placeholder="Add task" class="form-control mx-2" value="<?php echo $data['task'] ?? ''; ?>">
                    <input type="date" name="duedate" class="form-control mx-2" value="<?php echo $data['duedate'] ?? ''; ?>">
                    <button class="btn btn-primary mx-2" name="<?php echo isset($data) ? 'update' : 'submit'; ?>" value="<?php echo isset($data) ? 'update' : 'submit'; ?>" id="<?php echo isset($data) ? 'update' : 'submit'; ?>">
                        <i class='bx bxs-edit-alt bx-tada'></i>
                    </button>
                </div>

                <div class="input-section d-flex justify-content-center mt-3">
                    <!-- Input deskripsi tugas -->
                     <input type="text" name="description" placeholder="Deskripsi tugas..." value="<?php echo $data['description']; ?>" class="form-control mx-2 mb-2" style="max-width: 300px;">
                    <button type="submit" class="btn btn-secondary mx-2 mb-2" name="cari">
                        <i class='bx bx-search'></i> Search <!-- Tombol cari -->
                    </button>
                </div>
                
                <div class="delete text-center my-3">
                    <button class="btn btn-primary" name="deleteall"><i class='bx bxs-trash bx-tada'></i></button>
                </div>

                <div class="task-list w-100">
                    <div class="task-header d-flex justify-content-between px-3 py-2 mb-2 bg-light">
                        <div class="task">Task</div>
                        <div class="description">Description</div>
                        <div class="duedate">Due Date</div>
                        <div class="status">Status</div>
                        <div class="action">Action</div>
                    </div>
                    <div class="task-container">
                        <?php while ($row = $tambah->fetch_assoc()) { ?>
                            <div class="task-item d-flex justify-content-between align-items-center px-3 py-2 mb-2 bg-white">
                                <div class="task"><?php echo htmlspecialchars($row['task']); ?></div>
                                <div class="description"><?php echo htmlspecialchars($row['description']); ?></div>
                                <div class="duedate"><?php echo $row['duedate'] != '0000-00-00' ? htmlspecialchars($row['duedate']) : 'No Due Date'; ?></div>
                                <div class="status"><?php echo htmlspecialchars($row['status']); ?></div>
                                <div class="action d-flex">
                                    <a class="btn btn-primary mx-1" href="index.php?updatetask=<?php echo $row['id_todo']; ?>"><i class='bx bx-edit-alt'></i></a>
                                    <a class="btn btn-primary mx-1" href="index.php?statustask=<?php echo $row['id_todo']; ?>"><i class='bx bx-check'></i></a>
                                    <a class="btn btn-primary mx-1" href="index.php?deletetask=<?php echo $row['id_todo']; ?>"><i class='bx bx-trash'></i></a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </form>

            <?php
            if (isset($error)) {
                echo '<div class="alert alert-warning justify-content-center my-5 w-100" role="alert" id="alert">' . htmlspecialchars($error) . '</div>';
            }
            ?>
        </div>
    </main>

    <script>
        // Display the alert  
        setTimeout(function() {
            var alert = document.getElementById('alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }
        }, 5000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
