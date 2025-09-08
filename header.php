<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Planner - <?php echo $page_title ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>
    <header>
        <h1>Study Planner</h1>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="dashboard.php">Dashboard</a>
                <a href="subjects.php">Subjects</a>
                <a href="schedule.php">Schedule</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">