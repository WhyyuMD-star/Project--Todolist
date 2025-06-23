<?php
require 'koneksi.php'; // Mengimpor file koneksi ke database

if (!isset($_SESSION['email'])) { // Mengecek apakah user sudah login
    header('Location: login.php'); // Jika belum, redirect ke halaman login
    exit();
}

$email = $_SESSION['email']; // Menyimpan email user yang sedang login

if (isset($_POST['submit'])) { // Jika tombol submit ditekan (menambah task)
    $task = $_POST['task']; // Mengambil input task dari form
    $duedate = $_POST['duedate']; // Mengambil input tanggal dari form
    $description = $_POST['description']; // Mengambil input tanggal dari form

    if (empty($task) && empty($duedate) && empty($description)) { // Jika task dan tanggal kosong
        $error = "No task and date added"; // Tampilkan pesan error
    } else if (empty($duedate)) { // Jika tanggal kosong
        $error = "No date added"; // Tampilkan pesan error
    } else if (empty($task)) { // Jika task kosong
        $error = "No task added"; // Tampilkan pesan error
    } else if (empty($description)) { // Jika task kosong
        $error = "No task added";
    } else { // Tampilkan pesan error
        // Jika semua terisi, masukkan data ke database
        $stmt = $koneksi->prepare("INSERT INTO todo (task, duedate, email, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $task, $duedate, $email, $description); // Bind parameter ke query
        $stmt->execute(); // Eksekusi query
        $stmt->close(); // Tutup statement
        header('Location: index.php'); // Refresh halaman
        exit();
    }
}

// Query untuk mengambil semua task user berdasarkan email, urut berdasarkan tanggal
$tambah = $koneksi->prepare("SELECT * FROM todo WHERE email = ? ORDER BY duedate ASC");
$tambah->bind_param("s", $email); // Bind email user
$tambah->execute(); // Eksekusi query
$result = $tambah->get_result(); // Ambil hasil query

// === PENCARIAN ===
$search = $_POST['search'] ?? ''; // Ambil input pencarian (jika ada)
if (!empty($search)) { // Jika ada input pencarian
    $sql = "SELECT * FROM todo WHERE email = ? AND task LIKE ? ORDER BY duedate ASC"; // Query cari task
    $stmt = $koneksi->prepare($sql);
    $like = "%" . $search . "%"; // Format pencarian LIKE
    $stmt->bind_param("ss", $email, $like); // Bind parameter
} else {
    $sql = "SELECT * FROM todo WHERE email = ? ORDER BY duedate ASC"; // Query tampilkan semua task
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $email); // Bind email
}
$stmt->execute(); // Eksekusi query
$result = $stmt->get_result(); // Ambil hasil query

if (isset($_GET['deletetask'])) { // Jika ada permintaan hapus task
    $id = $_GET['deletetask']; // Ambil id task yang akan dihapus
    $stmt = $koneksi->prepare("DELETE FROM todo WHERE id_todo = ?");
    $stmt->bind_param("i", $id); // Bind id task
    $stmt->execute(); // Eksekusi query
    $stmt->close(); // Tutup statement
    header('Location: index.php'); // Refresh halaman
    exit();
}

if (isset($_GET['statustask'])) { // Jika ada permintaan ubah status task
    $id = $_GET['statustask']; // Ambil id task
    // Query untuk toggle status antara pending dan completed
    $stmt = $koneksi->prepare("UPDATE todo SET status = CASE WHEN status = 'pending' THEN 'completed' ELSE 'pending' END WHERE id_todo = ?");
    $stmt->bind_param("i", $id); // Bind id task
    $stmt->execute(); // Eksekusi query
    $stmt->close(); // Tutup statement
    header('Location: index.php'); // Refresh halaman
    exit();
}

if (isset($_POST['update'])) { // Jika ada permintaan update task
    $id = $_POST['id']; // Ambil id task
    $task = $_POST['task']; // Ambil task baru
    $duedate = $_POST['duedate']; // Ambil tanggal baru
    $stmt = $koneksi->prepare("UPDATE todo SET task = ?, duedate = ? WHERE id_todo = ?");
    $stmt->bind_param("ssi", $task, $duedate, $id); // Bind parameter
    $stmt->execute(); // Eksekusi query
    $stmt->close(); // Tutup statement
    header('Location: index.php'); // Refresh halaman
    exit();
}

if (isset($_POST['deleteall'])) { // Jika ada permintaan hapus semua task
    $stmt = $koneksi->prepare("DELETE FROM todo WHERE email = ?");
    $stmt->bind_param("s", $email); // Bind email user
    $stmt->execute(); // Eksekusi query
    $stmt->close(); // Tutup statement
    header('Location: index.php'); // Refresh halaman
    exit();
}

$koneksi->close(); // Tutup koneksi database
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"> <!-- Set karakter encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"> <!-- Import Bootstrap -->
    <link href="style.css" rel="stylesheet"> <!-- Import CSS custom -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'> <!-- Import icon -->
    <title>To Do List</title> <!-- Judul halaman -->
</head>
<body>
    <header class="container-fluid position-fixed"> <!-- Header navbar -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <!-- <a class="navbar-brand" href="../dashboard/todolist.html"><img src="../dashboard/logo.png" alt="Logo" id="logo"></a> -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span> <!-- Tombol toggle navbar (mobile) -->
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link mx-3 my-2" aria-current="page" href="index.php">To-do List</a> <!-- Link ke halaman utama -->
                        </li>
                    </ul>
                    </div>
        <a href="../login/logout.php" class="logout-btn"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
                    <!-- <button class="button my-2" type="submit">Profile</button> Tombol profile -->
                </div>
            </div>
        </nav>
    </header>

    <main class="main-content"> <!-- Konten utama -->
        <div class="container d-flex flex-column align-items-center">

            <!-- FORM TAMBAH TASK & HAPUS SEMUA -->
            <form action="" method="post" class="w-100" id="form"> <!-- Form tambah task -->
                <div class="judul">
                    <h3 class="fs-1 fw-bold text-center">To Do List</h3> <!-- Judul -->
                </div>

                <div class="input-section d-flex justify-content-center mt-3">
                    <input type="text" name="task" placeholder="Add task" class="form-control mx-2"> <!-- Input task -->
                    <input type="date" name="duedate" class="form-control mx-2"> <!-- Input tanggal -->
                    <button class="btn btn-primary mx-2" name="submit" value="submit" id="submit">
                        <i class='bx bxs-edit-alt bx-tada'></i> <!-- Tombol tambah task -->
                    </button>
                </div>
                <!-- Deskripsi di bawah Add Task -->
                <div class="input-section d-flex justify-content-center mt-3">
                    <!-- Input deskripsi tugas -->
                    <input type="text" name="description" placeholder="Deskripsi tugas..." class="form-control mx-2 mb-2" style="max-width: 300px;"> <!-- Input deskripsi (belum digunakan di backend) -->
                    <input type="text" name="search" placeholder="Search task..." value="<?php echo htmlspecialchars($search); ?>" class="form-control mx-2 mb-2" style="max-width: 300px;"> <!-- Input pencarian -->
                    <button type="submit" class="btn btn-secondary mx-2 mb-2" name="cari">
                        <i class='bx bx-search'></i> Search <!-- Tombol cari -->
                    </button>
                </div>

                

                <div class="delete text-center my-3">
                    <button class="btn btn-primary" name="deleteall"><i class='bx bxs-trash bx-tada'></i></button> <!-- Tombol hapus semua task -->
                </div>

                <div class="task-list w-100">
                    <div class="task-header d-flex justify-content-between px-3 py-2 mb-2 bg-light">
                        <div class="task">Task</div> <!-- Header kolom task -->
                        <div class="description">Description</div> <!-- Header kolom task -->
                        <div class="duedate">Due Date</div> <!-- Header kolom tanggal -->
                        <div class="status">Status</div> <!-- Header kolom status -->
                        <div class="action">Action</div> <!-- Header kolom aksi -->
                    </div>
                    <div class="task-container">
                        <?php while ($row = $result->fetch_assoc()) { ?> <!-- Loop setiap task -->
                            <div class="task-item d-flex justify-content-between align-items-center px-3 py-2 mb-2 bg-white">
                                <div class="task"><?php echo htmlspecialchars($row['task']); ?></div> <!-- Tampilkan nama task -->
                                <div class="description"><?php echo htmlspecialchars($row['description']); ?></div> <!-- Tampilkan nama deskripsi -->
                                <div class="duedate"><?php echo htmlspecialchars($row['duedate'] != '0000-00-00' ? $row['duedate'] : 'No Due Date'); ?></div> <!-- Tampilkan tanggal -->
                                <div class="status"><?php echo htmlspecialchars($row['status']); ?></div> <!-- Tampilkan status -->
                                <div class="action d-flex">
                                    <a class="btn btn-primary mx-1" href="update.php?updatetask=<?php echo $row['id_todo']; ?>"><i class='bx bx-edit-alt'></i></a> <!-- Tombol edit -->
                                    <a class="btn btn-primary mx-1" href="index.php?statustask=<?php echo $row['id_todo']; ?>"><i class='bx bx-check'></i></a> <!-- Tombol ubah status -->
                                    <a class="btn btn-primary mx-1" href="index.php?deletetask=<?php echo $row['id_todo']; ?>"><i class='bx bx-trash'></i></a> <!-- Tombol hapus -->
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </form>
            <?php
            if (isset($error)) { // Jika ada error
                echo '<div class="alert alert-warning justify-content-center my-5 w-100" role="alert" id="alert">' . htmlspecialchars($error) . '</div>'; // Tampilkan pesan error
            }
            ?>
        </div>
    </main>

    <script>
        setTimeout(function() { // Script untuk menghilangkan alert setelah 5 detik
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script> <!-- Import JS Bootstrap -->
</body>

</html>