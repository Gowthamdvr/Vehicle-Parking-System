<?php
session_start();
require_once '../includes/db_connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM staff WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['staff_id'] = $user['staff_id'];
        $_SESSION['staff_name'] = $user['fullname'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid staff credentials!";
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5 fade-in">
        <div class="card p-4 shadow">
            <div class="text-center mb-4">
                <i class="fas fa-user-tie fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold">Staff Login</h3>
                <p class="text-muted">Enter your credentials to manage parking</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Staff Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" name="login" class="btn btn-primary btn-lg">Sign In</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="../index.php" class="text-muted small">← Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
