<?php 
require_once 'includes/db_connect.php';

if (!isset($_GET['vid'])) {
    die("Invalid request");
}

$vid = $_GET['vid'];
$stmt = $pdo->prepare("SELECT v.*, s.slot_number FROM vehicles v JOIN parking_slots s ON v.slot_id = s.slot_id WHERE v.vehicle_id = ?");
$stmt->execute([$vid]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    die("Vehicle not found");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Ticket - <?php echo $vehicle['vehicle_number']; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background: #fff; padding: 20px; text-align: center; }
        .ticket { border: 2px dashed #000; padding: 20px; width: 300px; margin: 0 auto; }
        .header { border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom: 10px; }
        .details { text-align: left; }
        .footer { margin-top: 20px; border-top: 2px solid #000; padding-top: 10px; font-size: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="ticket">
        <div class="header">
            <h3>PARKEASE</h3>
            <p>Vehicle Parking Ticket</p>
        </div>
        <div class="details">
            <p><strong>Vehicle #:</strong> <?php echo $vehicle['vehicle_number']; ?></p>
            <p><strong>Type:</strong> <?php echo $vehicle['vehicle_type']; ?> </p>
            <p><strong>Slot:</strong> <span style="font-size: 20px;"><?php echo $vehicle['slot_number']; ?></span></p>
            <p><strong>Entry:</strong> <?php echo date('d-m-Y H:i', strtotime($vehicle['entry_time'])); ?></p>
        </div>
        <div class="footer">
            <p>Please keep this ticket safely.</p>
            <p>Scan to exit. Standard rates apply.</p>
        </div>
    </div>
    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()">Print Again</button>
        <a href="index.php">Back to Dashboard</a>
    </div>
</body>
</html>
