<?php
require 'db.php';
require 'header.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE user_id = ?");
$stmt->execute([$user_id]);
$subjects = $stmt->fetchAll();
?>

<h1>Your Subjects</h1>

<a href="add_subject.php" class="btn">Add New Subject</a>

<?php if (count($subjects) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Subject Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($subject['description']); ?></td>
                    <td class="actions">
                        <a href="edit_subject.php?id=<?php echo $subject['subject_id']; ?>" class="btn">Edit</a>
                        <form action="delete_subject.php" method="post" style="display:inline;">
                            <input type="hidden" name="subject_id" value="<?php echo $subject['subject_id']; ?>">
                            <button type="submit" class="btn danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No subjects found. <a href="add_subject.php">Add your first subject</a></p>
<?php endif; ?>

<?php require 'footer.php'; ?>