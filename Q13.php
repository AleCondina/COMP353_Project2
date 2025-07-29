<!-- 13. Get a report on all active club members who have never been assigned to any formation
team session. The list should include the club member’s membership number, first
name, last name, age, phone number, email and current location name. The results
should be displayed sorted in ascending order by location name then by age. -->

<?php
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Club Members Never Assigned to Formation</title>
</head>
<body>
<h2>Active Club Members Never Assigned to a Formation</h2>

<?php
$sql = "
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
    AND CM.clubMemberID NOT IN (
        SELECT DISTINCT clubMemberID FROM FormationPlayers
    )
    ORDER BY L.name ASC, age ASC;
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>
            <tr>
                <th>Membership #</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Age</th>
                <th>Phone</th>
                <th>Current Location</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['clubMemberID']}</td>
                <td>{$row['firstName']}</td>
                <td>{$row['lastName']}</td>
                <td>{$row['age']}</td>
                <td>{$row['phoneNumber']}</td>
                <td>{$row['locationName']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No active club members found who have never been assigned to a formation.</p>";
}

$conn->close();
?>
</body>
</html>
