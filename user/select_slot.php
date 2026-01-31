<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

// Check if already parked
$stmt = $pdo->prepare("SELECT vehicle_id FROM vehicles WHERE user_id = ? AND status = 'Parked'");
$stmt->execute([$_SESSION['user_id']]);
if ($stmt->fetch()) {
    header("Location: dashboard.php");
    exit;
}

$slots = $pdo->query("SELECT * FROM parking_slots ORDER BY slot_number ASC")->fetchAll();

include '../includes/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12 text-center">
        <h2 class="fw-bold">Select Your Parking Slot</h2>
        <p class="text-muted">Choose an available spot from the list below.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card p-4">
            <div class="d-flex flex-wrap justify-content-center">
                <?php foreach ($slots as $slot): ?>
                    <div class="parking-slot <?php echo $slot['is_available'] ? 'slot-available' : 'slot-occupied'; ?>" 
                         onclick="<?php echo $slot['is_available'] ? "location.href='vehicle_details.php?slot_id=".$slot['slot_id']."'" : "alert('Slot already occupied!')"; ?>">
                        <div class="text-center">
                            <i class="fas <?php echo $slot['slot_type'] == 'Two-Wheeler' ? 'fa-motorcycle' : 'fa-car'; ?>"></i>
                            <div class="slot-label"><?php echo $slot['slot_number']; ?></div>
                            <div class="small" style="font-size: 0.6rem;"><?php echo $slot['slot_type']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-4 border-top pt-3 text-center">
                <span class="me-3"><span class="badge bg-success" style="width: 20px; height: 15px;">&nbsp;</span> Available</span>
                <span><span class="badge bg-danger" style="width: 20px; height: 15px;">&nbsp;</span> Occupied</span>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
