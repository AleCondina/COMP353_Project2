<?php
include 'db.php';

// Assign a player
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
    $formationID = $_POST['formationID'];
    $clubMemberID = $_POST['clubMemberID'];
    $role = $_POST['role'];

    // Get the date of the current formation
    $stmt = $conn->prepare("SELECT date FROM TeamFormation WHERE formationID = ?");
    $stmt->bind_param("i", $formationID);
    $stmt->execute();
    $stmt->bind_result($formationDate);
    $stmt->fetch();
    $stmt->close();

    // Check for conflicting assignment on the same day
    $stmt = $conn->prepare("
        SELECT fp.formationID
        FROM FormationPlayers fp
        JOIN TeamFormation tf ON fp.formationID = tf.formationID
        WHERE fp.clubMemberID = ? AND tf.date = ? AND fp.formationID != ?
    ");
    $stmt->bind_param("isi", $clubMemberID, $formationDate, $formationID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "This club member is already assigned to another formation on the same day.";
    } else {
        // Insert assignment
        $stmt = $conn->prepare("INSERT INTO FormationPlayers (formationID, clubMemberID, role) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $formationID, $clubMemberID, $role);
        $stmt->execute();
    }
    $stmt->close();
    header("Location: formation_players.php");
    exit;
}

// Delete a player from formation
if (isset($_GET['deleteFormationID']) && isset($_GET['deleteClubMemberID'])) {
    $stmt = $conn->prepare("DELETE FROM FormationPlayers WHERE formationID = ? AND clubMemberID = ?");
    $stmt->bind_param("ii", $_GET['deleteFormationID'], $_GET['deleteClubMemberID']);
    $stmt->execute();
    $stmt->close();
    header("Location: formation_players.php");
    exit;
}

// Fetch all formations and club members
$formations = $conn->query("
    SELECT 
        tf.formationID, 
        tf.date, 
        tf.startTime, 
        ta.name AS teamAName, 
        tb.name AS teamBName
    FROM TeamFormation tf
    JOIN Team ta ON tf.team1ID = ta.teamID
    JOIN Team tb ON tf.team2ID = tb.teamID
    ORDER BY tf.date DESC, tf.startTime DESC
");
$clubMembers = $conn->query("SELECT clubMemberID, firstName, lastName FROM ClubMember");

$teams = $conn->query("SELECT teamID, name FROM Team");


// Fetch current assignments
$assignments = $conn->query("
    SELECT fp.*, cm.firstName, cm.lastName, tf.date 
    FROM FormationPlayers fp
    JOIN ClubMember cm ON fp.clubMemberID = cm.clubMemberID
    JOIN TeamFormation tf ON fp.formationID = tf.formationID
    ORDER BY tf.date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Club Members to Formations</title>
</head>
<body>
<h2>Assign Club Member to Formation</h2>
<?php if (!empty($error)): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="POST">
    <input type="hidden" name="assign" value="1">

    <label>Formation:</label>
    <select name="formationID" id="formationID" required onchange="fetchTeams(this.value)">
        <option value="">Select...</option>
       <?php while ($row = $formations->fetch_assoc()): ?>
    <option value="<?= $row['formationID'] ?>">
        <?= htmlspecialchars($row['teamAName']) ?> vs <?= htmlspecialchars($row['teamBName']) ?> — <?= $row['date'] ?> <?= $row['startTime'] ?>
    </option>
    <?php endwhile; ?>  

    </select><br><br>

    <label>Team:</label>
    <select name="teamID" required>
        <option value="">Select...</option>
        <?php
        $teams->data_seek(0);
        while ($row = $teams->fetch_assoc()): ?>
            <option value="<?= $row['teamID'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
    </select><br><br>


    <label>Club Member:</label>
    <select name="clubMemberID" required>
        <option value="">Select...</option>
        <?php
        $clubMembers->data_seek(0); // Reset result pointer
        while ($row = $clubMembers->fetch_assoc()): ?>
            <option value="<?= $row['clubMemberID'] ?>"><?= htmlspecialchars($row['firstName'] . " " . $row['lastName']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Role:</label>
    <select name="role" required>
        <option value="">Select...</option>
        <option value="Goalkeeper">Goalkeeper</option>
        <option value="Defender">Defender</option>
        <option value="Midfielder">Midfielder</option>
        <option value="Forward">Forward</option>
        <option value="Head Coach">Head Coach</option>
    </select><br><br>

    <button type="submit">Assign</button>
</form>


<h2>Current Assignments</h2>
<table border="1">
    <tr>
        <th>Formation ID</th>
        <th>Date</th>
        <th>Club Member</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $assignments->fetch_assoc()): ?>
    <tr>
        <td><?= $row['formationID'] ?></td>
        <td><?= $row['date'] ?></td>
        <td><?= htmlspecialchars($row['firstName'] . " " . $row['lastName']) ?></td>
        <td><?= $row['role'] ?></td>
        <td>
            <a href="?deleteFormationID=<?= $row['formationID'] ?>&deleteClubMemberID=<?= $row['clubMemberID'] ?>" onclick="return confirm('Delete this assignment?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>


