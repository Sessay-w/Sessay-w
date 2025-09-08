<?php
require 'db.php';
require 'header.php';

$user_id = $_SESSION['user_id'];

// Get subjects for dropdown
$subjects = $pdo->prepare("SELECT * FROM subjects WHERE user_id = ?");
$subjects->execute([$user_id]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_id = $_POST['subject_id'];
    $session_date = $_POST['session_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $notes = $_POST['notes'];
    
    $stmt = $pdo->prepare("INSERT INTO study_sessions 
                          (user_id, subject_id, session_date, start_time, end_time, notes) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $subject_id, $session_date, $start_time, $end_time, $notes]);
    
    header("Location: schedule.php");
    exit();
}
?>

<h1>Schedule New Study Session</h1>

<form method="post">
    <div class="form-group">
        <label for="subject_id">Subject</label>
        <select id="subject_id" name="subject_id" required>
            <option value="">-- Select Subject --</option>
            <?php while ($subject = $subjects->fetch()): ?>
                <option value="<?php echo $subject['subject_id']; ?>">
                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="session_date">Date</label>
        <input type="date" id="session_date" name="session_date" required 
               min="<?php echo date('Y-m-d'); ?>">
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="time" id="start_time" name="start_time" required>
        </div>
        
        <div class="form-group">
            <label for="end_time">End Time</label>
            <input type="time" id="end_time" name="end_time" required>
        </div>
    </div>
    
    <div class="form-group">
        <label for="notes">Notes (Optional)</label>
        <textarea id="notes" name="notes" rows="3"></textarea>
    </div>
    
    <button type="submit" class="btn">Schedule Session</button>
    <a href="schedule.php" class="btn secondary">Cancel</a>
</form>

<?php require 'footer.php'; ?>