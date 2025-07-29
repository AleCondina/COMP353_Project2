<!-- 18. Get a report on all active club members who have never lost a game in which they
played. A club member is considered to win a game if she/he has been assigned to a
game session and is assigned to the team that has a score higher than the score of the
other team. The club member must be assigned to at least one formation game session.
The list should include the club member’s membership number, first name, last name,
age, phone number, email and current location name. The results should be displayed
sorted in ascending order by location name then by club membership number. -->

<?php
require_once "db.php";

// Prepare the query to get all active club members who have never lost a game they played in
$sql = "
    SELECT DISTINCT
    CM.clubMemberID,
    CM.firstName,
    CM.lastName,
    TIMESTAMPDIFF(YEAR, CM.dateOfBirth, CURDATE()) AS age,
    CM.phoneNumber,
    L.name AS locationName
    FROM ClubMember CM
    JOIN FormationPlayers FP ON CM.clubMemberID = FP.clubMemberID
    JOIN TeamFormation TF ON FP.formationID = TF.formationID
    JOIN TeamLocated TL ON FP.teamID = TL.teamID
    JOIN Location L ON TL.locationID = L.locationID
    WHERE CM.status = 'Active'
    AND TF.sessionType = 'Game'
    AND NOT EXISTS (
        SELECT 1
        FROM FormationPlayers FP2
        JOIN TeamFormation TF2 ON FP2.formationID = TF2.formationID
        WHERE FP2.clubMemberID = CM.clubMemberID
        AND TF2.sessionType = 'Game'
        AND (
            (FP2.teamID = TF2.team1ID AND TF2.team1Score < TF2.team2Score)
            OR
            (FP2.teamID = TF2.team2ID AND TF2.team2Score < TF2.team1Score)
        )
    )
    ORDER BY locationName ASC, CM.clubMemberID ASC;

";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Club Members Who Have Never Lost</title>
</head>
<body>
    <h2>Active Club Members Who Have Never Lost a Game</h2>

    <table border="1" cellpadding="8">
        <tr>
            <th>Membership ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Age</th>
            <th>Phone</th>
            <th>Location</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['clubMemberID']) ?></td>
            <td><?= htmlspecialchars($row['firstName']) ?></td>
            <td><?= htmlspecialchars($row['lastName']) ?></td>
            <td><?= htmlspecialchars($row['age']) ?></td>
            <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
            <td><?= htmlspecialchars($row['locationName']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="index.php">Back to Home</a></p>
</body>
</html>
