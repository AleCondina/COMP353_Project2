<?php
include 'db.php';

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO TeamFormation (team1ID, team2ID, locationID, date, startTime, sessionType, team1Score, team2Score) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisssii",
        $_POST['team1ID'],
        $_POST['team2ID'],
        $_POST['locationID'],
        $_POST['date'],
        $_POST['startTime'],
        $_POST['sessionType'],
        $_POST['team1Score'],
        $_POST['team2Score']
    );
    $stmt->execute();
    $stmt->close();
    header("Location: teamformation.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM TeamFormation WHERE formationID = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    $stmt->close();
    header("Location: teamformation.php");
    exit;
}

// Fetch teams
$teams = $conn->query("SELECT teamID, name FROM Team");

// Fetch locations
$locations = $conn->query("SELECT locationID, name, address FROM Location");

// Fetch formations
$formations = $conn->query("SELECT tf.*, t1.name AS team1Name, t2.name AS team2Name, l.name AS locationName, l.address AS locationAddress
                            FROM TeamFormation tf
                            JOIN Team t1 ON tf.team1ID = t1.teamID
                            JOIN Team t2 ON tf.team2ID = t2.teamID
                            JOIN Location l ON tf.locationID = l.locationID");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Team Formations</title>
</head>
<body>
<h2>Create Team Formation</h2>
<form method="POST">
    <input type="hidden" name="add" value="1">

    <label>Team 1:</label>
    <select name="team1ID" required>
        <option value="">Select...</option>
        <?php $teams->data_seek(0); while ($row = $teams->fetch_assoc()): ?>
            <option value="<?= $row['teamID'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Team 2:</label>
    <select name="team2ID" required>
        <option value="">Select...</option>
        <?php $teams->data_seek(0); while ($row = $teams->fetch_assoc()): ?>
            <option value="<?= $row['teamID'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Date:</label>
    <input type="date" name="date" required><br><br>

    <label>Start Time:</label>
    <input type="time" name="startTime" required><br><br>

    <label>Team 1 Score:</label>
    <input type="number" name="team1Score" min="0" value="0"><br><br>

    <label>Team 2 Score:</label>
    <input type="number" name="team2Score" min="0" value="0"><br><br>

    <label>Session Type:</label>
    <select name="sessionType" required>
        <option value="">Select...</option>
        <option value="Game">Game</option>
        <option value="Training">Training</option>
    </select><br><br>

    <label>Location:</label>
    <select name="locationID" onchange="updateAddress(this)" required>
        <option value="">Select...</option>
        <?php $locations->data_seek(0); while ($row = $locations->fetch_assoc()): ?>
            <option value="<?= $row['locationID'] ?>" data-address="<?= htmlspecialchars($row['address']) ?>">
                <?= htmlspecialchars($row['name']) ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <button type="submit">Create Formation</button>
</form>

<h2>All Team Formations</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Team 1</th>
        <th>Team 2</th>
        <th>Date</th>
        <th>Time</th>
        <th>Score</th>
        <th>Session Type</th>
        <th>Location</th>
        <th>Address</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $formations->fetch_assoc()): ?>
    <tr>
        <td><?= $row['formationID'] ?></td>
        <td><?= htmlspecialchars($row['team1Name']) ?></td>
        <td><?= htmlspecialchars($row['team2Name']) ?></td>
        <td><?= $row['date'] ?></td>
        <td><?= $row['startTime'] ?></td>
        <td><?= $row['team1Score'] ?> - <?= $row['team2Score'] ?></td>
        <td><?= $row['sessionType'] ?></td>
        <td><?= htmlspecialchars($row['locationName']) ?></td>
        <td><?= htmlspecialchars($row['locationAddress']) ?></td>
        <td>
            <a href="teamformation_edit.php?id=<?= $row['formationID'] ?>">Edit</a> |
            <a href="?delete=<?= $row['formationID'] ?>" onclick="return confirm('Delete this formation?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>


<script>
function updateAddress(select) {
    const selected = select.options[select.selectedIndex];
    const address = selected.getAttribute('data-address');
    document.getElementById('addressInput').value = address || '';
}
</script>
</body>
</html>
