<?php 
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_rates'])) {
    $two_rate = $_POST['two_wheeler_rate'];
    $four_rate = $_POST['four_wheeler_rate'];

    $stmt = $pdo->prepare("UPDATE settings SET two_wheeler_rate = ?, four_wheeler_rate = ? WHERE id = 1");
    if($stmt->execute([$two_rate, $four_rate])) {
        $message = "Rates updated successfully!";
    }
}

$settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();

include '../includes/header.php'; 
?>

<div class="row mb-4 fade-in">
    <div class="col-8">
        <h3 class="fw-bold">System Settings</h3>
        <p class="text-muted">Configure parking rates per hour.</p>
    </div>
    <div class="col-4 text-end">
        <a href="dashboard.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back Dashboard</a>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4">
            <h5 class="fw-bold mb-4">Parking Rates (per hour)</h5>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-bold">Two-Wheeler Rate ($)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="two_wheeler_rate" class="form-control" value="<?php echo $settings['two_wheeler_rate']; ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Four-Wheeler Rate ($)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="four_wheeler_rate" class="form-control" value="<?php echo $settings['four_wheeler_rate']; ?>" required>
                    </div>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" name="update_rates" class="btn btn-primary">Update Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
