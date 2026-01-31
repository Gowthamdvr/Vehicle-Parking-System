<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

if (!isset($_GET['slot_id'])) {
    header("Location: select_slot.php");
    exit;
}

$slot_id = $_GET['slot_id'];
$stmt = $pdo->prepare("SELECT * FROM parking_slots WHERE slot_id = ? AND is_available = 1");
$stmt->execute([$slot_id]);
$slot = $stmt->fetch();

if (!$slot) {
    header("Location: select_slot.php?err=Slot not available");
    exit;
}

include '../includes/header.php';
?>

<div class="row justify-content-center fade-in">
    <div class="col-md-6">
        <div class="card p-4">
            <h3 class="fw-bold mb-4 text-center">Vehicle Details</h3>
            <div class="alert alert-info">
                You are booking Slot: <strong><?php echo $slot['slot_number']; ?></strong> (<?php echo $slot['slot_type']; ?>)
            </div>

            <form action="payment.php" method="POST">
                <input type="hidden" name="slot_id" value="<?php echo $slot_id; ?>">
                <input type="hidden" name="vehicle_type" value="<?php echo $slot['slot_type']; ?>">
                
                <div class="mb-3">
                    <label class="form-label">Vehicle Number</label>
                    <input type="text" name="vehicle_number" class="form-control" placeholder="e.g. MH 12 AB 1234" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Proceed to Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
