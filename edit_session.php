<?php
require 'db.php';
require 'header.php';

if (!isset($_GET['id'])) {
    header("Location: schedule.php");
    exit();
}

$session_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch session data
$session = $pdo->prepare("
    SELECT s.*, sub.subject_name 
    FROM study_sessions s
    JOIN subjects sub ON s.subject_id = sub.subject_id
    WHERE s.session_id = ? AND s.user_id = ?
");
$session->execute([$session_id, $user_id]);
$session = $session->fetch();

if (!$session) {
    header("Location: schedule.php");
    exit();
}

// Get subjects for dropdown
$subjects = $pdo->prepare("SELECT * FROM subjects WHERE user_id = ?");
$subjects->execute([$user_id]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_id = $_POST['subject_id'];
    $session_date = $_POST['session_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $notes = $_POST['notes'];
    $completed = isset($_POST['completed']) ? 1 : 0;
    
    $update = $pdo->prepare("
        UPDATE study_sessions 
        SET subject_id = ?, session_date = ?, start_time = ?, end_time = ?, notes = ?, completed = ?
        WHERE session_id = ?
    ");
    $update->execute([$subject_id, $session_date, $start_time, $end_time, $notes, $completed, $session_id]);
    
    header("Location: schedule.php");
    exit();
}
?>

<h1>Edit Study Session</h1>

<form method="post">
    <div class="form-group">
        <label for="subject_id">Subject</label>
        <select id="subject_id" name="subject_id" required>
            <?php while ($subject = $subjects->fetch()): ?>
                <option value="<?php echo $subject['subject_id']; ?>" 
                    <?php if ($subject['subject_id'] == $session['subject_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="session_date">Date</label>
        <input type="date" id="session_date" name="session_date" required 
               value="<?php echo htmlspecialchars($session['session_date']); ?>">
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="time" id="start_time" name="start_time" required
                   value="<?php echo htmlspecialchars(substr($session['start_time'], 0, 5)); ?>">
        </div>
        
        <div class="form-group">
            <label for="end_time">End Time</label>
            <input type="time" id="end_time" name="end_time" required
                   value="<?php echo htmlspecialchars(substr($session['end_time'], 0, 5)); ?>">
        </div>
    </div>
    
    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3"><?php 
            echo htmlspecialchars($session['notes']); 
        ?></textarea>
    </div>
    
    <div class="form-group">
        <label>
            <input type="checkbox" name="completed" 
                <?php if ($session['completed']) echo 'checked'; ?>>
            Mark as completed
        </label>
    </div>
    
    <button type="submit" class="btn">Update Session</button>
    <a href="schedule.php" class="btn secondary">Cancel</a>
</form>

<?php require 'footer.php'; ?>