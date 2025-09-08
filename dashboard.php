<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get subjects
$subjects = $pdo->prepare("SELECT * FROM subjects WHERE user_id = ?");
$subjects->execute([$user_id]);

// Get upcoming sessions
$sessions = $pdo->prepare("
    SELECT s.*, sub.subject_name 
    FROM study_sessions s
    JOIN subjects sub ON s.subject_id = sub.subject_id
    WHERE s.user_id = ? AND s.session_date >= CURDATE()
    ORDER BY s.session_date, s.start_time
    LIMIT 5
");
$sessions->execute([$user_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Study Planner</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="subjects.php">Subjects</a>
            <a href="schedule.php">Schedule</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <section class="quick-actions">
            <h2>Quick Actions</h2>
            <a href="add_subject.php" class="btn">Add Subject</a>
            <a href="add_session.php" class="btn">Schedule Study Session</a>
        </section>

        <section class="upcoming-sessions">
            <h2>Upcoming Study Sessions</h2>
            <?php if ($sessions->rowCount() > 0): ?>
                <ul>
                    <?php while ($session = $sessions->fetch()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($session['subject_name']); ?></strong>
                            <span><?php echo date('D, M j', strtotime($session['session_date'])); ?></span>
                            <span><?php echo date('g:i A', strtotime($session['start_time'])) . ' - ' . date('g:i A', strtotime($session['end_time'])); ?></span>
                            <a href="edit_session.php?id=<?php echo $session['session_id']; ?>">Edit</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No upcoming study sessions. <a href="add_session.php">Schedule one now!</a></p>
            <?php endif; ?>
        </section>

        <section class="subjects-overview">
            <h2>Your Subjects</h2>
            <?php if ($subjects->rowCount() > 0): ?>
                <ul>
                    <?php while ($subject = $subjects->fetch()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($subject['subject_name']); ?></strong>
                            <p><?php echo htmlspecialchars($subject['description']); ?></p>
                            <a href="edit_subject.php?id=<?php echo $subject['subject_id']; ?>">Edit</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No subjects added yet. <a href="add_subject.php">Add your first subject!</a></p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>