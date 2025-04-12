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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Manager</title>
    <style>
        body {
            font-family: Arial;
            background: #f7f7f7;
            padding: 30px;
        }
        h2 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
            text-align: center;
        }
        input, button {
            padding: 8px;
            margin: 4px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            background: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        .card {
            background: white;
            padding: 15px;
            margin: 10px auto;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .actions form {
            margin: 0;
        }
    </style>
</head>
<body>

<h2>Password Manager</h2>

<form method="POST">
    <input type="text" name="website" placeholder="Website" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="text" name="password" placeholder="Password" required>
    <button type="submit" name="add">Add</button>
</form>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="card">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="text" name="website" value="<?= htmlspecialchars($row['website']) ?>" required>
            <input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>" required>
            <input type="text" name="password" value="<?= htmlspecialchars($row['password']) ?>" required>
            <div class="actions">
                <button type="submit" name="update">Update</button>
        </form>
        <form method="GET" onsubmit="return confirm('Are you sure you want to delete this?')">
            <input type="hidden" name="delete" value="<?= $row['id'] ?>">
            <button type="submit" style="background:#dc3545;">Delete</button>
        </form>
            </div>
    </div>
<?php endwhile; ?>

</body>
</html>
