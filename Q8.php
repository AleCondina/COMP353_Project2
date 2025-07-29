<!-- 8. Get complete details for every location in the system. Details include address, city,
province, postal-code, phone number, web address, type (Head, Branch), capacity,
general manager name, the number of minor club members associated with that
location, the number of major club members associated with that location, and the
number of teams associated with that location. The results should be displayed sorted
in ascending order by Province, then by city. -->

<?php
include 'db.php'; // assume this sets up $conn

$sql = "
SELECT 
    l.address,
    l.city,
    l.province,
    l.postalCode,
    lc.phoneNumber,
    l.webAddress,
    l.type,
    l.maxCapacity,
    gm.generalManagerName,
    IFNULL(m.minorCount, 0) AS minorMembers,
    IFNULL(m.majorCount, 0) AS majorMembers,
    IFNULL(t.teamCount, 0) AS numTeams
FROM Location l

-- One phone number per location (use MIN or MAX if multiple)
LEFT JOIN (
    SELECT locationID, MIN(number) AS phoneNumber
    FROM LocationCalledAt
    GROUP BY locationID
) lc ON lc.locationID = l.locationID

-- One general manager per location
LEFT JOIN (
    SELECT wa.locationID, CONCAT(p.firstName, ' ', p.lastName) AS generalManagerName
    FROM WorksAt wa
    JOIN Personnel p ON wa.personnelID = p.personnelID
    WHERE p.role = 'General Manager'
    GROUP BY wa.locationID
) gm ON gm.locationID = l.locationID

-- Subquery for minors and majors
LEFT JOIN (
    SELECT 
        ea.locationID,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, cm.dateOfBirth, CURDATE()) < 18 THEN 1 ELSE 0 END) AS minorCount,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, cm.dateOfBirth, CURDATE()) >= 18 THEN 1 ELSE 0 END) AS majorCount
    FROM EnrolledAt ea
    JOIN ClubMember cm ON ea.clubMemberID = cm.clubMemberID
    GROUP BY ea.locationID
) m ON m.locationID = l.locationID

-- Subquery for team count
LEFT JOIN (
    SELECT locationID, COUNT(DISTINCT teamID) AS teamCount
    FROM TeamLocated
    GROUP BY locationID
) t ON t.locationID = l.locationID

ORDER BY l.province ASC, l.city ASC;
";



$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

echo "<h2>Q8 Location Report</h2>";
echo "<table border='1'>";
echo "<tr>
        <th>Address</th><th>City</th><th>Province</th><th>Postal Code</th><th>Phone</th>
        <th>Web Address</th><th>Type</th><th>Capacity</th><th>General Manager</th>
        <th>Minor Members</th><th>Major Members</th><th># Teams</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['city']}</td>";
    echo "<td>{$row['province']}</td>";
    echo "<td>{$row['postalCode']}</td>";
    echo "<td>{$row['phoneNumber']}</td>";
    echo "<td>{$row['webAddress']}</td>";
    echo "<td>{$row['type']}</td>";
    echo "<td>{$row['maxCapacity']}</td>";
    echo "<td>{$row['generalManagerName']}</td>";
    echo "<td>{$row['minorMembers']}</td>";
    echo "<td>{$row['majorMembers']}</td>";
    echo "<td>{$row['numTeams']}</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>

