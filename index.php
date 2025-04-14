<?php
// Database connection
$servername = "localhost";
$username = "xuser";
$password = "Majumder@mysql1";
$database = "password_manager";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add password
if (isset($_POST['add'])) {
    $website = $_POST['website'];
    $username = $_POST['username'];
    $pass = $_POST['password'];
    // check if any field is empty. if empty show alart and return to index.php
    if (empty($website) || empty($username) || empty($pass)) {
        echo "<script>
            alert('Please fill all fields');
            window.location.href = 'index.php';
        </script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO passwords (website, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $website, $username, $pass);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// Delete password
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM passwords WHERE id = $id");
    header("Location: index.php");
    exit();
}

// Update password
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $website = $_POST['website'];
    $username = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("UPDATE passwords SET website=?, username=?, password=? WHERE id=?");
    $stmt->bind_param("sssi", $website, $username, $pass, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// Fetch all passwords
$result = $conn->query("SELECT * FROM passwords ORDER BY id DESC");

// Store all credentials for JavaScript
$allCredentials = [];
while ($row = $result->fetch_assoc()) {
    $allCredentials[] = $row;
}

// Reset result pointer for display loop
$result->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Manager</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Hidden element to store password data -->
<div id="password-data" data-credentials='<?= json_encode($allCredentials) ?>' style="display: none;"></div>

<h2>Password Manager</h2>

<form method="POST">
    <input type="text" name="website" placeholder="Website" required>
    <input type="text" name="username" placeholder="Username" required>
    <div class="password-field">
        <input type="password" name="password" id="addPassword" placeholder="Password" required>
        <button type="button" class="show-password" onclick="togglePassword('addPassword')">
            <i class="far fa-eye"></i>
        </button>
    </div>

    
    <button type="submit" name="add">Add</button>
</form>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= htmlspecialchars($row['website']) ?></h3>
            <div class="actions">
                <button type="button" onclick="openUpdateModal(<?= $row['id'] ?>)" class="edit">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button type="button" onclick="openDeleteModal(<?= $row['id'] ?>)" class="delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
        <div>
            <strong>Username:</strong> <?= htmlspecialchars($row['username']) ?>
        </div>
        <div class="password-field">
            <strong>Password:</strong> 
            <span id="pass<?= $row['id'] ?>">••••••••</span>
            <button type="button" class="show-password" onclick="toggleViewPassword(<?= $row['id'] ?>, '<?= htmlspecialchars($row['password']) ?>')">
                <i class="far fa-eye" id="eye<?= $row['id'] ?>"></i>
            </button>
        </div>
    </div>
<?php endwhile; ?>

<!-- Update Modal -->
<div class="modal-overlay" id="updateModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Update Password</h3>
            <button type="button" class="modal-close" onclick="closeModal('updateModal')">&times;</button>
        </div>
        <form method="POST" id="updateForm">
            <div class="modal-body">
                <input type="hidden" name="id" id="updateId">
                <input type="text" name="website" id="updateWebsite" placeholder="Website" required>
                <input type="text" name="username" id="updateUsername" placeholder="Username" required>
                <div class="password-field">
                    <input type="password" name="password" id="updatePassword" placeholder="Password" required>
                    <button type="button" class="show-password" onclick="togglePassword('updatePassword')">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('updateModal')">Cancel</button>
                <button type="submit" name="update" class="btn-update">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Delete Password</h3>
            <button type="button" class="modal-close" onclick="closeModal('deleteModal')">&times;</button>
        </div>
        <form method="GET" id="deleteForm">
            <div class="modal-body">
                <p>Are you sure you want to delete this password? This action cannot be undone.</p>
                <input type="hidden" name="delete" id="deleteId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="submit" class="btn-delete">Delete</button>
            </div>
        </form>
    </div>
</div>

<script src="scripts.js"></script>

</body>
</html>
