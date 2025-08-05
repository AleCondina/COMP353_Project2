<!-- 15. Get a report on all active club members who have only been assigned as goalkeepers
in all the team formation sessions they have been assigned to. They must be assigned
to at least one formation session as a goalkeeper. They should have never been assigned
to any formation session with a role different than goalkeeper. The list should include
the club member’s membership number, first name, last name, age, phone number,
email and current location name. The results should be displayed sorted in ascending
order by location name then by club membership number. -->


<?php
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Middle Blocker-Only Assigned Club Members</title>
</head>
<body>
<h2>Active Club Members Assigned Only as Middle Blockers</h2>

<?php
$sql = "
    SELECT 
        CM.clubMemberID,
        CM.firstName,
        CM.lastName,
        TIMESTAMPDIFF(YEAR, CM.dateOfBirth, CURDATE()) AS age,
        CM.phoneNumber,
        CM.email,
        L.name AS locationName
    FROM ClubMember CM
    JOIN EnrolledAt EA ON CM.clubMemberID = EA.clubMemberID
    JOIN Location L ON EA.locationID = L.locationID
    WHERE CM.status = 'Active'
      AND CM.clubMemberID IN (
          SELECT clubMemberID
          FROM RoleIn
          GROUP BY clubMemberID
          HAVING 
            COUNT(*) > 0 AND
            COUNT(CASE WHEN role = 'middle blocker' THEN 1 END) = COUNT(*)
      )
    ORDER BY L.name ASC, CM.clubMemberID ASC;
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>
            <tr>
                <th>Membership #</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Age</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Current Location</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['clubMemberID']}</td>
                <td>{$row['firstName']}</td>
                <td>{$row['lastName']}</td>
                <td>{$row['age']}</td>
                <td>{$row['phoneNumber']}</td>
                <td>{$row['email']}</td>
                <td>{$row['locationName']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No club members found who have only been assigned as middle blockers.</p>";
}

$conn->close();
?>
</body>
</html>
