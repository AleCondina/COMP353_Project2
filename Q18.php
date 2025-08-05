<!-- 18. Get a report on all active club members who have never lost a game in which they
played. A club member is considered to win a game if she/he has been assigned to a
game session and is assigned to the team that has a score higher than the score of the
other team. The club member must be assigned to at least one formation game session.
The list should include the club member’s membership number, first name, last name,
age, phone number, email and current location name. The results should be displayed
sorted in ascending order by location name then by club membership number. -->

<?php
require_once "db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Club Members Who Have Never Lost a Game</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
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
    <h2>Active Club Members Who Have Never Lost a Game</h2>

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
          SELECT DISTINCT RI.clubMemberID
          FROM RoleIn RI
          JOIN TeamMatchUp TMU ON RI.formationID = TMU.formationID
          JOIN InTeam IT ON RI.clubMemberID = IT.clubMemberID
          WHERE TMU.sessionType = 'Game'
      )
      AND NOT EXISTS (
          SELECT 1
          FROM RoleIn RI2
          JOIN TeamMatchUp TMU2 ON RI2.formationID = TMU2.formationID
          JOIN InTeam IT2 ON RI2.clubMemberID = IT2.clubMemberID
          WHERE TMU2.sessionType = 'Game'
            AND RI2.clubMemberID = CM.clubMemberID
            AND (
                (IT2.teamID = TMU2.team1ID AND TMU2.team1Score < TMU2.team2Score)
                OR
                (IT2.teamID = TMU2.team2ID AND TMU2.team2Score < TMU2.team1Score)
            )
      )
    ORDER BY locationName ASC, CM.clubMemberID ASC;
";


    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Membership ID</th>
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
        echo "<p>No club members found who have never lost a game.</p>";
    }

    $conn->close();
    ?>
</body>
</html>
