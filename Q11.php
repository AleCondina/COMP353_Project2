<!-- Q11. Get details of club members who are currently inactive and have been associated with
at least two different locations and are members for at least two years. Details include
Club membership number, first name and last name. Results should be displayed sorted
in ascending order by club membership number. -->
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php'; // Make sure this file contains your database connection logic
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inactive Members with Multiple Locations</title>
    <style>
        table {
            border-collapse: collapse;
            width: 70%;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <h2>Inactive Club Members with 2+ Locations and 2+ Years of Membership</h2>

    <?php
    $query = "
        SELECT
            CM.clubMemberID,
            CM.firstName,
            CM.lastName
        FROM ClubMember CM
        JOIN EnrolledAt EA ON CM.clubMemberID = EA.clubMemberID
        WHERE CM.status = 'Inactive'
          AND TIMESTAMPDIFF(YEAR, CM.dateOfBirth, CURDATE()) >= 2
        GROUP BY CM.clubMemberID, CM.firstName, CM.lastName
        HAVING COUNT(DISTINCT EA.locationID) >= 2
        ORDER BY CM.clubMemberID ASC;
    ";

    $result = $conn->query($query);

    if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Club Member ID</th>
                <th>First Name</th>
                <th>Last Name</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['clubMemberID']) ?></td>
                    <td><?= htmlspecialchars($row['firstName']) ?></td>
                    <td><?= htmlspecialchars($row['lastName']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p><strong>No results found matching the criteria.</strong></p>
    <?php endif; ?>

</body>
</html>
