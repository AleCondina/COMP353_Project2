<?php
require_once "db.php";

if (!isset($_GET['id'])) {
    header("Location: teamformation.php");
    exit;
}

$formationID = $_GET['id'];

// Fetch the formation details
$stmt = $conn->prepare("SELECT * FROM TeamFormation WHERE formationID = ?");
$stmt->bind_param("i", $formationID);
$stmt->execute();
$result = $stmt->get_result();
$formation = $result->fetch_assoc();
$stmt->close();

if (!$formation) {
    echo "Team formation not found.";
    exit;
}

// Fetch locations for dropdown
$locationQuery = $conn->query("SELECT locationID, name FROM Location");
$locations = $locationQuery->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE TeamFormation SET locationID = ?, date = ?, startTime = ?, sessionType = ?, team1Score = ?, team2Score = ? WHERE formationID = ?");
    $stmt->bind_param("isssiii",
        $_POST['locationID'],
        $_POST['date'],
        $_POST['startTime'],
        $_POST['sessionType'],
        $_POST['team1Score'],
        $_POST['team2Score'],
        $formationID
    );
    $stmt->execute();
    $stmt->close();
    header("Location: teamformation.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Team Formation</title>
</head>
<body>
    <h2>Edit Team Formation</h2>
    <form method="post">
        <label>Location:</label>
        <select name="locationID" required>
            <?php foreach ($locations as $loc): ?>
                <option value="<?= $loc['locationID'] ?>" <?= $loc['locationID'] == $formation['locationID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($loc['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Date:</label>
        <input type="date" name="date" value="<?= $formation['date'] ?>" required><br><br>

        <label>Start Time:</label>
        <input type="time" name="startTime" value="<?= $formation['startTime'] ?>" required><br><br>

        <label>Session Type:</label>
        <select name="sessionType" required>
            <option value="Training" <?= $formation['sessionType'] == 'Training' ? 'selected' : '' ?>>Training</option>
            <option value="Game" <?= $formation['sessionType'] == 'Game' ? 'selected' : '' ?>>Game</option>
        </select><br><br>

        <label>Team 1 Score:</label>
        <input type="number" name="team1Score" value="<?= $formation['team1Score'] ?>" min="0" required><br><br>

        <label>Team 2 Score:</label>
        <input type="number" name="team2Score" value="<?= $formation['team2Score'] ?>" min="0" required><br><br>

        <button type="submit" name="update">Update</button>
    </form>

    <p><a href="teamformation.php">Back to Team Formations</a></p>
</body>
</html>
