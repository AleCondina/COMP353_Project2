<?php
require_once 'db.php';

$formations = $conn->query("
    SELECT 
        tf.formationID, 
        tf.date, 
        tf.startTime, 
        ta.name AS teamAName, 
        ta.gender AS genderA,
        tb.name AS teamBName, 
        tb.gender AS genderB
    FROM TeamFormation tf
    JOIN Team ta ON tf.team1ID = ta.teamID
    JOIN Team tb ON tf.team2ID = tb.teamID
    ORDER BY tf.date DESC, tf.startTime DESC
");

$teams = $conn->query("
    SELECT t.teamID, t.name, l.name AS locationName
    FROM Team t
    JOIN TeamLocated tl ON t.teamID = tl.teamID
    JOIN Location l ON tl.locationID = l.locationID
");

$clubMembers = $conn->query("
    SELECT cm.clubMemberID, cm.firstName, cm.lastName, cm.gender, l.name AS locationName
    FROM ClubMember cm
    JOIN EnrolledAt ea ON cm.clubMemberID = ea.clubMemberID
    JOIN Location l ON ea.locationID = l.locationID
");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $formationID = $_POST['formationID'];
    $teamID = $_POST['teamID'];
    $clubMemberID = $_POST['clubMemberID'];
    $role = $_POST['role'];

    // Check for conflicts
    $conflictCheck = $conn->prepare("
        SELECT 1 FROM FormationPlayers fp 
        JOIN TeamFormation tf ON fp.formationID = tf.formationID
        WHERE fp.clubMemberID = ? AND tf.date = (
            SELECT date FROM TeamFormation WHERE formationID = ?
        ) AND ABS(TIMESTAMPDIFF(HOUR, tf.startTime, (SELECT startTime FROM TeamFormation WHERE formationID = ?))) < 3
    ");
    $conflictCheck->bind_param("iii", $clubMemberID, $formationID, $formationID);
    $conflictCheck->execute();
    $conflictResult = $conflictCheck->get_result();

    if ($conflictResult->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO FormationPlayers (formationID, teamID, clubMemberID, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $formationID, $teamID, $clubMemberID, $role);
        $stmt->execute();
        $stmt->close();
        echo "<p style='color:green;'>Club member assigned successfully.</p>";
    } else {
        echo "<p style='color:red;'>Conflict detected: member is already in another formation within 3 hours on the same day.</p>";
    }

    $conflictCheck->close();
}
?>

<h2>Assign Club Member to Team Formation</h2>

<form method="POST">
    <label for="formationID">Formation:</label>
    <select name="formationID" required>
        <option value="">Select Formation</option>
        <?php while ($row = $formations->fetch_assoc()): ?>
            <option value="<?= $row['formationID'] ?>">
                <?= htmlspecialchars($row['teamAName']) ?> (<?= $row['genderA'] ?>) vs <?= htmlspecialchars($row['teamBName']) ?> (<?= $row['genderB'] ?>) — <?= $row['date'] ?> <?= $row['startTime'] ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="teamID">Team:</label>
    <select name="teamID" required>
        <option value="">Select Team</option>
        <?php while ($row = $teams->fetch_assoc()): ?>
            <option value="<?= $row['teamID'] ?>">
                <?= htmlspecialchars($row['name']) ?> (<?= htmlspecialchars($row['locationName']) ?>)
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="clubMemberID">Club Member:</label>
    <select name="clubMemberID" required>
        <option value="">Select Member</option>
        <?php while ($row = $clubMembers->fetch_assoc()): ?>
            <option value="<?= $row['clubMemberID'] ?>">
                <?= htmlspecialchars($row['firstName'] . " " . $row['lastName']) ?> (<?= $row['gender'] ?>, <?= htmlspecialchars($row['locationName']) ?>)
            </option>
        <?php endwhile; ?>
    </select><br><br>
    <label for="role">Role:</label>
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="Goalkeeper">Goalkeeper</option>
        <option value="Defender">Defender</option>
        <option value="Midfielder">Midfielder</option>
        <option value="Forward">Forward</option>
        <option value="Head Coach">Head Coach</option>
    </select><br><br>

    <button type="submit">Assign</button>
</form>
