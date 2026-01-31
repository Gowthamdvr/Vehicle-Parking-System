<?php
require_once '../includes/db_connect.php';

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, fullname, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $fullname, $phone]);
        header("Location: login.php?msg=Registration successful! Please login.");
        exit;
    } catch (PDOException $e) {
        $message = "Error: Username or Email already exists!";
        $messageType = "danger";
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5 fade-in">
        <div class="card p-4">
            <h3 class="fw-bold mb-4 text-center text-primary">User Registration</h3>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="fullname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" name="register" class="btn btn-primary">Register</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
