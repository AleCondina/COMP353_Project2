<?php
include 'db.php';

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO Team (name, gender) VALUES (?, ?)");
    $stmt->bind_param("ss", $_POST['name'], $_POST['gender']);
    $stmt->execute();
    $newTeamID = $stmt->insert_id;
    $stmt->close();

    // Insert into TeamLocated
    $stmt = $conn->prepare("INSERT INTO TeamLocated (teamID, locationID) VALUES (?, ?)");
    $stmt->bind_param("ii", $newTeamID, $_POST['locationID']);
    $stmt->execute();
    $stmt->close();

    header("Location: teams.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $teamID = $_GET['delete'];

    // First delete from TeamLocated and InTeam to satisfy FK constraints
    $conn->query("DELETE FROM TeamLocated WHERE teamID = $teamID");
    $conn->query("DELETE FROM InTeam WHERE teamID = $teamID");
    $conn->query("DELETE FROM Team WHERE teamID = $teamID");

    header("Location: teams.php");
    exit;
}

// Fetch Teams with location
$teams = $conn->query("SELECT t.teamID, t.name, t.gender, l.name AS locationName
                       FROM Team t
                       LEFT JOIN TeamLocated tl ON t.teamID = tl.teamID
                       LEFT JOIN Location l ON tl.locationID = l.locationID");

// Fetch Locations for dropdown
$locations = $conn->query("SELECT locationID, name FROM Location");

// Fetch selected team members
$members = null;
$selectedTeamID = $_GET['teamID'] ?? null;
if ($selectedTeamID) {
    $stmt = $conn->prepare("SELECT cm.firstName, cm.lastName 
                            FROM InTeam it
                            JOIN ClubMember cm ON it.clubMemberID = cm.clubMemberID
                            WHERE it.teamID = ?");
    $stmt->bind_param("i", $selectedTeamID);
    $stmt->execute();
    $members = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teams</title>
</head>
<body>
    <h2>Create Team</h2>
    <form method="POST">
        <input type="hidden" name="add" value="1">
        <label>Team Name:</label>
        <input type="text" name="name" required><br><br>

        <label>Gender:</label>
        <select name="gender" required>
            <option value="">Select...</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select><br><br>

        <label>Location:</label>
        <select name="locationID" required>
            <option value="">Select...</option>
            <?php while ($loc = $locations->fetch_assoc()): ?>
                <option value="<?= $loc['locationID'] ?>">
                    <?= htmlspecialchars($loc['name']) ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Add Team</button>
    </form>

    <h2>Select Team to View Members</h2>
    <form method="GET">
        <label>Select Team:</label>
        <select name="teamID" onchange="this.form.submit()">
            <option value="">Select...</option>
            <?php
            $teams->data_seek(0);
            while ($row = $teams->fetch_assoc()): ?>
                <option value="<?= $row['teamID'] ?>" <?= ($selectedTeamID == $row['teamID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($members !== null): ?>
        <h3>Members in Team</h3>
        <ul>
            <?php while ($member = $members->fetch_assoc()): ?>
                <li><?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?></li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>

    <h2>All Teams</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Gender</th>
            <th>Location</th>
            <th>Actions</th>
        </tr>
        <?php
        $teams->data_seek(0);
        while ($row = $teams->fetch_assoc()): ?>
        <tr>
            <td><?= $row['teamID'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['gender']) ?></td>
            <td><?= htmlspecialchars($row['locationName'] ?? 'N/A') ?></td>
            <td>
                <a href="teams_edit.php?id=<?= $row['teamID'] ?>">Edit</a>
                <a href="teams.php?delete=<?= $row['teamID'] ?>" onclick="return confirm('Delete this team?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
