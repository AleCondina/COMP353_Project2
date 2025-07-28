<?php
include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: teams.php");
    exit;
}

// Fetch current team info
$stmt = $conn->prepare("SELECT t.name, tl.locationID 
                        FROM Team t 
                        LEFT JOIN TeamLocated tl ON t.teamID = tl.teamID 
                        WHERE t.teamID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $locationID);
$stmt->fetch();
$stmt->close();

// Fetch all locations for dropdown
$locations = $conn->query("SELECT locationID, name FROM Location");

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = $_POST['name'];
    $newLocationID = $_POST['locationID'];

    // Update team name
    $stmt = $conn->prepare("UPDATE Team SET name = ? WHERE teamID = ?");
    $stmt->bind_param("si", $newName, $id);
    $stmt->execute();
    $stmt->close();

    // Check if team already has a location mapping
    $check = $conn->prepare("SELECT COUNT(*) FROM TeamLocated WHERE teamID = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($exists);
    $check->fetch();
    $check->close();

    if ($exists) {
        $stmt = $conn->prepare("UPDATE TeamLocated SET locationID = ? WHERE teamID = ?");
        $stmt->bind_param("ii", $newLocationID, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO TeamLocated (teamID, locationID) VALUES (?, ?)");
        $stmt->bind_param("ii", $id, $newLocationID);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: teams.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Team</title>
</head>
<body>
    <h2>Edit Team</h2>
    <form method="POST">
        <label>Team Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required><br><br>

        <label>Location:</label>
        <select name="locationID" required>
            <option value="">Select...</option>
            <?php while ($loc = $locations->fetch_assoc()): ?>
                <option value="<?= $loc['locationID'] ?>" <?= ($loc['locationID'] == $locationID) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($loc['name']) ?> (ID: <?= $loc['locationID'] ?>)
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Update Team</button>
    </form>
    <br>
    <a href="teams.php">Back to Teams</a>
</body>
</html>
