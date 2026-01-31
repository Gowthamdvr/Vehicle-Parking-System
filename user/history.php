<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$history = $pdo->prepare("SELECT v.*, s.slot_number FROM vehicles v JOIN parking_slots s ON v.slot_id = s.slot_id WHERE v.user_id = ? ORDER BY v.entry_time DESC");
$history->execute([$user_id]);
$records = $history->fetchAll();

include '../includes/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-8">
        <h3 class="fw-bold">My Parking History</h3>
        <p class="text-muted">Review your previous parking sessions.</p>
    </div>
    <div class="col-4 text-end">
        <a href="dashboard.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left me-1"></i> Dashboard</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="bg-light">
                <tr>
                    <th>Vehicle</th>
                    <th>Slot</th>
                    <th>Entry</th>
                    <th>Exit</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($records as $row): ?>
                <tr>
                    <td><strong><?php echo $row['vehicle_number']; ?></strong></td>
                    <td><span class="badge bg-secondary"><?php echo $row['slot_number']; ?></span></td>
                    <td class="small"><?php echo date('M d, H:i', strtotime($row['entry_time'])); ?></td>
                    <td class="small"><?php echo $row['exit_time'] ? date('M d, H:i', strtotime($row['exit_time'])) : '-'; ?></td>
                    <td class="fw-bold text-success"><?php echo $row['amount'] > 0 ? '$'.number_format($row['amount'], 2) : '-'; ?></td>
                    <td>
                        <?php if($row['status'] == 'Parked'): ?>
                            <span class="badge bg-danger">Parked</span>
                        <?php else: ?>
                            <span class="badge bg-success">Exited</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($records) == 0): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No parking records found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
