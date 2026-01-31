<?php 
require_once 'includes/db_connect.php';

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_entry'])) {
    $vehicle_number = strtoupper(trim($_POST['vehicle_number']));
    $owner_name = trim($_POST['owner_name']);
    $vehicle_type = $_POST['vehicle_type'];

    // Check if vehicle is already parked
    $check = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_number = ? AND status = 'Parked'");
    $check->execute([$vehicle_number]);
    
    if ($check->rowCount() > 0) {
        $message = "This vehicle is already parked!";
        $messageType = "danger";
    } else {
        // Find available slot of that type
        $slotQuery = $pdo->prepare("SELECT slot_id, slot_number FROM parking_slots WHERE slot_type = ? AND is_available = 1 LIMIT 1");
        $slotQuery->execute([$vehicle_type]);
        $slot = $slotQuery->fetch();

        if ($slot) {
            $slot_id = $slot['slot_id'];
            
            // Start transaction
            $pdo->beginTransaction();
            try {
                // Insert vehicle record
                $stmt = $pdo->prepare("INSERT INTO vehicles (vehicle_number, owner_name, vehicle_type, slot_id, entry_time) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$vehicle_number, $owner_name, $vehicle_type, $slot_id]);
                
                // Update slot status
                $updateSlot = $pdo->prepare("UPDATE parking_slots SET is_available = 0 WHERE slot_id = ?");
                $updateSlot->execute([$slot_id]);
                
                $pdo->commit();
                $lastId = $pdo->lastInsertId();
                $message = "Vehicle registered successfully! Assigned Slot: " . $slot['slot_number'] . " <br><a href='ticket.php?vid=$lastId' target='_blank' class='btn btn-light btn-sm mt-2'>Print Ticket</a>";
                $messageType = "success";
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "Error: " . $e->getMessage();
                $messageType = "danger";
            }
        } else {
            $message = "No slots available for " . $vehicle_type;
            $messageType = "warning";
        }
    }
}

include 'includes/header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-md-6 fade-in">
        <div class="card p-4">
            <h3 class="fw-bold mb-4 text-center text-primary">Vehicle Entry Registration</h3>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-bold">Vehicle Number</label>
                    <input type="text" name="vehicle_number" class="form-control" placeholder="e.g. MH 12 AB 1234" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Owner Name (Optional)</label>
                    <input type="text" name="owner_name" class="form-control" placeholder="John Doe">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Vehicle Type</label>
                    <select name="vehicle_type" class="form-select" required>
                        <option value="Four-Wheeler">Four-Wheeler (Car/SUV)</option>
                        <option value="Two-Wheeler">Two-Wheeler (Bike/Scooter)</option>
                    </select>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" name="register_entry" class="btn btn-primary btn-lg">Register Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
