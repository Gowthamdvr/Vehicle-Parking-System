<?php 
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_slot'])) {
    $slot_number = strtoupper(trim($_POST['slot_number']));
    $slot_type = $_POST['slot_type'];

    try {
        $stmt = $pdo->prepare("INSERT INTO parking_slots (slot_number, slot_type) VALUES (?, ?)");
        $stmt->execute([$slot_number, $slot_type]);
        $message = "Slot $slot_number added successfully!";
    } catch (PDOException $e) {
        $message = "Error: Slot number might already exist!";
    }
}

if (isset($_GET['delete'])) {
    $slot_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM parking_slots WHERE slot_id = ? AND is_available = 1");
        $stmt->execute([$slot_id]);
        if($stmt->rowCount() > 0)
            $message = "Slot deleted successfully!";
        else
            $message = "Cannot delete occupied slot!";
    } catch (PDOException $e) {
        $message = "Cannot delete slot because it has history!";
    }
}

$slots = $pdo->query("SELECT * FROM parking_slots ORDER BY slot_number ASC")->fetchAll();

include '../includes/header.php'; 
?>

<div class="row mb-4 fade-in">
    <div class="col-md-8">
        <h3 class="fw-bold">Manage Parking Slots</h3>
        <p class="text-muted">Add or remove parking slots from the system.</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="dashboard.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back Dashboard</a>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3">Add New Slot</h5>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Slot Identifier</label>
                    <input type="text" name="slot_number" class="form-control" placeholder="e.g. C5" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Slot Type</label>
                    <select name="slot_type" class="form-select">
                        <option value="Four-Wheeler">Four-Wheeler</option>
                        <option value="Two-Wheeler">Two-Wheeler</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="submit" name="add_slot" class="btn btn-primary">Add Slot</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Current Slots</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Slot #</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($slots as $slot): ?>
                        <tr>
                            <td class="fw-bold"><?php echo $slot['slot_number']; ?></td>
                            <td><?php echo $slot['slot_type']; ?></td>
                            <td>
                                <?php if($slot['is_available']): ?>
                                    <span class="badge bg-success">Available</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Occupied</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($slot['is_available']): ?>
                                <a href="?delete=<?php echo $slot['slot_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled title="Cannot delete occupied slot"><i class="fas fa-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
