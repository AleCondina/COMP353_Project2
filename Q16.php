<!-- 16. Get a report on all active club members who have only been assigned at least once to
every role throughout all the formation team game sessions. The club member must be
assigned to at least one formation game session as a goalkeeper, one as a defender, one
as a midfielder, and one as a forward. The list should include the club member’s
membership number, first name, last name, age, phone number, email and current
location name. The results should be displayed sorted in ascending order by location
name then by club membership number. -->


<?php
include 'db.php';

$query = "
    SELECT 
        CM.clubMemberID,
        CM.firstName,
        CM.lastName,
        TIMESTAMPDIFF(YEAR, CM.dateOfBirth, CURDATE()) AS age,
        CM.phoneNumber,
        L.name AS locationName
    FROM ClubMember CM
    JOIN EnrolledAt EA ON CM.clubMemberID = EA.clubMemberID
    JOIN Location L ON EA.locationID = L.locationID
    WHERE CM.status = 'Active'
      AND CM.clubMemberID IN (
          SELECT clubMemberID
          FROM FormationPlayers
          GROUP BY clubMemberID
          HAVING 
              SUM(role = 'Goalkeeper') > 0 AND
              SUM(role = 'Defender') > 0 AND
              SUM(role = 'Midfielder') > 0 AND
              SUM(role = 'Forward') > 0
      )
    ORDER BY L.name ASC, CM.clubMemberID ASC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All-Roles Club Members Report</title>
</head>
<body>
    <h2>Active Club Members Assigned to All Roles</h2>
    <table border="1">
        <tr>
            <th>Club Member ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Age</th>
            <th>Phone Number</th>
            <th>Location</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['clubMemberID']) ?></td>
                <td><?= htmlspecialchars($row['firstName']) ?></td>
                <td><?= htmlspecialchars($row['lastName']) ?></td>
                <td><?= $row['age'] ?></td>
                <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                <td><?= htmlspecialchars($row['locationName']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
