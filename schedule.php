<?php
require 'db.php';
require 'header.php';

$user_id = $_SESSION['user_id'];

// Handle session deletion
if (isset($_POST['delete_session'])) {
    $session_id = $_POST['session_id'];
    $stmt = $pdo->prepare("DELETE FROM study_sessions WHERE session_id = ? AND user_id = ?");
    $stmt->execute([$session_id, $user_id]);
}

// Handle marking as complete/incomplete
if (isset($_POST['toggle_complete'])) {
    $session_id = $_POST['session_id'];
    $stmt = $pdo->prepare("UPDATE study_sessions SET completed = NOT completed WHERE session_id = ? AND user_id = ?");
    $stmt->execute([$session_id, $user_id]);
}

// Get all study sessions with subject names
$sessions = $pdo->prepare("
    SELECT s.*, sub.subject_name 
    FROM study_sessions s
    JOIN subjects sub ON s.subject_id = sub.subject_id
    WHERE s.user_id = ?
    ORDER BY s.session_date, s.start_time
");
$sessions->execute([$user_id]);
?>

<h1>Your Study Schedule</h1>

<a href="add_session.php" class="btn">Schedule New Session</a>

<div class="schedule-filters">
    <form method="get" class="filter-form">
        <label>
            Show:
            <select name="filter" onchange="this.form.submit()">
                <option value="all" <?php echo (!isset($_GET['filter']) || $_GET['filter'] === 'all') ? 'selected' : ''; ?>>All Sessions</option>
                <option value="upcoming" <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                <option value="completed" <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="past" <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'past') ? 'selected' : ''; ?>>Past Sessions</option>
            </select>
        </label>
    </form>
</div>

<?php if ($sessions->rowCount() > 0): ?>
    <div class="schedule-container">
        <?php 
        $current_date = null;
        while ($session = $sessions->fetch()): 
            // Apply filters
            $session_date = new DateTime($session['session_date']);
            $today = new DateTime();
            
            if (isset($_GET['filter'])) {
                if ($_GET['filter'] === 'upcoming' && ($session_date < $today || $session['completed'])) continue;
                if ($_GET['filter'] === 'completed' && !$session['completed']) continue;
                if ($_GET['filter'] === 'past' && ($session_date >= $today || $session['completed'])) continue;
            }
            
            // Display date header if new date
            if ($session['session_date'] != $current_date): 
                $current_date = $session['session_date'];
                $date_obj = new DateTime($current_date);
                $is_past = $date_obj < $today;
                $is_today = $date_obj->format('Y-m-d') == $today->format('Y-m-d');
        ?>
            <h2 class="schedule-date <?php echo $is_today ? 'today' : ''; ?> <?php echo $is_past ? 'past' : ''; ?>">
                <?php echo $date_obj->format('l, F j, Y'); ?>
                <?php if ($is_today): ?>
                    <span class="badge">Today</span>
                <?php endif; ?>
            </h2>
        <?php endif; ?>

        <div class="session-card <?php echo $session['completed'] ? 'completed' : ''; ?>">
            <div class="session-info">
                <h3><?php echo htmlspecialchars($session['subject_name']); ?></h3>
                <div class="session-time">
                    <?php echo date('g:i A', strtotime($session['start_time'])); ?> - 
                    <?php echo date('g:i A', strtotime($session['end_time'])); ?>
                </div>
                <?php if (!empty($session['notes'])): ?>
                    <div class="session-notes"><?php echo htmlspecialchars($session['notes']); ?></div>
                <?php endif; ?>
            </div>
            
            
            <div class="session-actions">
                <form method="post" style="display: inline;">
                    <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                    <button type="submit" name="toggle_complete" class="btn <?php echo $session['completed'] ? 'secondary' : 'success'; ?>">
                        <?php echo $session['completed'] ? 'Mark Incomplete' : 'Mark Complete'; ?>
                    </button>
                </form>
                
                <a href="edit_session.php?id=<?php echo $session['session_id']; ?>" class="btn">Edit</a>
                
                <form method="post" style="display: inline;">
                    <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                    <button type="submit" name="delete_session" class="btn danger" 
                            onclick="return confirm('Are you sure you want to delete this session?')">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>No study sessions found. <a href="add_session.php">Schedule your first session</a></p>
<?php endif; ?>

<?php require 'footer.php'; ?>