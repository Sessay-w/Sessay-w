<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_name = $_POST['subject_name'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO subjects (user_id, subject_name, description) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $subject_name, $description]);
        header("Location: subjects.php");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to add subject: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Subject - Study Planner</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Add New Subject</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <form method="post">
            <input type="text" name="subject_name" placeholder="Subject Name" required>
            <textarea name="description" placeholder="Description (optional)" rows="4"></textarea>
            <button type="submit">Add Subject</button>
        </form>
    </div>
</body>
</html>