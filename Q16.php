<!-- 16. Get a report on all active club members who have only been assigned at least once to
every role throughout all the formation team game sessions. The club member must be
assigned to at least one formation game session as a goalkeeper, one as a defender, one
as a midfielder, and one as a forward. The list should include the club member’s
membership number, first name, last name, age, phone number, email and current
location name. The results should be displayed sorted in ascending order by location
name then by club membership number. -->

<?php
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Members Assigned to All Key Roles in Game Sessions</title>
    <style>
        table {
            border-collapse: collapse;
            width: 85%;
        }
        th, td {
            border: 1px solid #888;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #ccc;
        }
    </style>
</head>
<body>
<h2>Active Club Members Assigned at Least Once to All Key Roles in Game Sessions</h2>

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
JOIN (
    SELECT EA1.*
    FROM EnrolledAt EA1
    INNER JOIN (
        SELECT clubMemberID, MAX(startDate) AS latestStart
        FROM EnrolledAt
        GROUP BY clubMemberID
    ) EA2
    ON EA1.clubMemberID = EA2.clubMemberID AND EA1.startDate = EA2.latestStart
) EA ON CM.clubMemberID = EA.clubMemberID
JOIN Location L ON EA.locationID = L.locationID
WHERE CM.status = 'Active'
  AND CM.clubMemberID IN (
    SELECT clubMemberID
    FROM RoleIn R
    JOIN TeamMatchUp TM ON R.formationID = TM.formationID
    WHERE TM.sessionType = 'Game'
    GROUP BY clubMemberID
    HAVING 
        COUNT(DISTINCT CASE WHEN role = 'setter' THEN 1 END) > 0 AND
        COUNT(DISTINCT CASE WHEN role = 'libero' THEN 1 END) > 0 AND
        COUNT(DISTINCT CASE WHEN role = 'outside hitter' THEN 1 END) > 0 AND
        COUNT(DISTINCT CASE WHEN role = 'opposite hitter' THEN 1 END) > 0
  )
ORDER BY locationName ASC, CM.clubMemberID ASC;

";


$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table>
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
    echo "<p>No club members found who were assigned to all four required roles in game sessions only.</p>";
}

$conn->close();
?>
</body>
</html>
