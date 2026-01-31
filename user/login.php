<?php
session_start();
require_once '../includes/db_connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['fullname'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username/email or password!";
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5 fade-in">
        <div class="card p-4">
            <h3 class="fw-bold mb-4 text-center text-primary">User Login</h3>
            
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username or Email</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
