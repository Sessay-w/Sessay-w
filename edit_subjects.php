<?php
require 'db.php';
require 'header.php';

if (!isset($_GET['id'])) {
    header("Location: subjects.php");
    exit();
}

$subject_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch subject data
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_id = ? AND user_id = ?");
$stmt->execute([$subject_id, $user_id]);
$subject = $stmt->fetch();

if (!$subject) {
    header("Location: subjects.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_name = $_POST['subject_name'];
    $description = $_POST['description'];
    
    $update = $pdo->prepare("UPDATE subjects SET subject_name = ?, description = ? WHERE subject_id = ?");
    $update->execute([$subject_name, $description, $subject_id]);
    
    header("Location: subjects.php");
    exit();
}
?>

<h1>Edit Subject</h1>

<form method="post">
    <div class="form-group">
        <label for="subject_name">Subject Name</label>
        <input type="text" id="subject_name" name="subject_name" 
               value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?php 
            echo htmlspecialchars($subject['description']); 
        ?></textarea>
    </div>
    
    <button type="submit" class="btn">Update Subject</button>
    <a href="subjects.php" class="btn secondary">Cancel</a>
</form>

<?php require 'footer.php'; ?>