<?php 
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

$search = "";
$where = "1=1";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $where = "v.vehicle_number LIKE '%$search%' OR v.owner_name LIKE '%$search%'";
}

$history = $pdo->query("SELECT v.*, s.slot_number FROM vehicles v JOIN parking_slots s ON v.slot_id = s.slot_id WHERE $where ORDER BY v.entry_time DESC")->fetchAll();

include '../includes/header.php'; 
?>

<div class="row mb-4 fade-in">
    <div class="col-md-8">
        <h3 class="fw-bold">Parking History & Reports</h3>
        <p class="text-muted">Search and view all parking records.</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="dashboard.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back Dashboard</a>
    </div>
</div>

<div class="card p-4 mb-4 shadow-sm">
    <form method="GET" action="">
        <div class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search by vehicle number or owner name..." value="<?php echo $search; ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle text-center">
            <thead class="bg-light">
                <tr>
                    <th>Vehicle #</th>
                    <th>Type</th>
                    <th>Slot</th>
                    <th>Entry</th>
                    <th>Exit</th>
                    <th>Duration (hrs)</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($history as $row): ?>
                <tr>
                    <td class="fw-bold"><?php echo $row['vehicle_number']; ?></td>
                    <td><?php echo $row['vehicle_type']; ?></td>
                    <td><span class="badge bg-secondary"><?php echo $row['slot_number']; ?></span></td>
                    <td class="small"><?php echo date('d M, H:i', strtotime($row['entry_time'])); ?></td>
                    <td class="small">
                        <?php echo $row['exit_time'] ? date('d M, H:i', strtotime($row['exit_time'])) : '-'; ?>
                    </td>
                    <td>
                        <?php 
                        if ($row['exit_time']) {
                            $entry = new DateTime($row['entry_time']);
                            $exit = new DateTime($row['exit_time']);
                            $interval = $entry->diff($exit);
                            echo ceil(($interval->days * 24) + $interval->h + ($interval->i / 60));
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td class="fw-bold text-success">
                        <?php echo $row['amount'] > 0 ? '$'.number_format($row['amount'], 2) : '-'; ?>
                    </td>
                    <td>
                        <?php if($row['status'] == 'Parked'): ?>
                            <span class="badge bg-danger">Parked</span>
                        <?php else: ?>
                            <span class="badge bg-success">Exited</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($history) == 0): ?>
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No records found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
