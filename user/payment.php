<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST' && !isset($_POST['vehicle_number'])) {
    header("Location: select_slot.php");
    exit;
}

$slot_id = $_POST['slot_id'];
$vehicle_number = strtoupper(trim($_POST['vehicle_number']));
$owner_name = trim($_POST['owner_name']);
$vehicle_type = $_POST['vehicle_type'];

// Get rate for initial fee (optional, or just show QR)
// For this simulation, we'll just show a QR to "Confirm" parking
// In a real system, you might pay at the end, but the user asked for QR Payment in flow

if (isset($_POST['confirm_payment'])) {
    // Process the parking entry
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO vehicles (vehicle_number, owner_name, vehicle_type, slot_id, user_id, entry_time) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$vehicle_number, $owner_name, $vehicle_type, $slot_id, $_SESSION['user_id']]);
        
        $updateSlot = $pdo->prepare("UPDATE parking_slots SET is_available = 0 WHERE slot_id = ?");
        $updateSlot->execute([$slot_id]);
        
        $pdo->commit();
        header("Location: status.php?success=Parking confirmed!");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center fade-in">
    <div class="col-md-6 text-center">
        <div class="card p-5">
            <h3 class="fw-bold mb-4">Scan QR to Pay & Confirm</h3>
            <p class="text-muted mb-4">Please scan the QR code below using any UPI app to pay the base parking fee and confirm your slot.</p>
            
            <div class="mb-4">
                <!-- Placeholder for QR Code -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=ParkEase_Payment_Simulation" alt="QR Code" class="img-fluid border p-2 rounded">
            </div>

            <div class="alert alert-warning mb-4">
                <i class="fas fa-info-circle me-2"></i> Only $5.00 base fee will be charged now. Final amount will be calculated upon exit.
            </div>

            <form action="" method="POST">
                <input type="hidden" name="slot_id" value="<?php echo $slot_id; ?>">
                <input type="hidden" name="vehicle_number" value="<?php echo $vehicle_number; ?>">
                <input type="hidden" name="owner_name" value="<?php echo $owner_name; ?>">
                <input type="hidden" name="vehicle_type" value="<?php echo $vehicle_type; ?>">
                
                <div class="d-grid">
                    <button type="submit" name="confirm_payment" class="btn btn-success btn-lg">I Have Paid - Confirm My Parking</button>
                    <a href="select_slot.php" class="btn btn-link mt-2 text-muted">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
