<!-- 19. Get a report on all volunteer personnels who are family members of at least one minor
club member. Results should include the volunteer’s first name, last name, number of
associated minor members, telephone number, email address, current location name
and current role. Results should be displayed sorted in ascending order by location
name, then by role, then by first name then by last name. -->


<?php
require_once "db.php";

$query = "
    SELECT 
        P.firstName, 
        P.lastName, 
        COUNT(DISTINCT CM.clubMemberID) AS numMinors,
        P.phoneNumber, 
        P.email, 
        L.name AS locationName,
        P.role
    FROM FamilyMember FM
    JOIN RelatedTo RT ON FM.SSN = RT.familyMemberSSN
    JOIN ClubMember CM ON RT.clubMemberID = CM.clubMemberID
    JOIN Personnel P ON FM.SSN = P.SSN
    JOIN WorksAt WA ON P.personnelID = WA.personnelID
    JOIN Location L ON WA.locationID = L.locationID
    WHERE P.mandate = 'Volunteer'
    AND TIMESTAMPDIFF(YEAR, CM.dateOfBirth, CURDATE()) < 18
    GROUP BY P.personnelID, P.firstName, P.lastName, P.phoneNumber, P.email, L.name, P.role
    ORDER BY locationName ASC, P.role ASC, P.firstName ASC, P.lastName ASC;
    ";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Volunteers Related to Minor Club Members</title>
</head>
<body>
    <h2>Volunteer Personnel Related to Minor Club Members</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="6">
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th># of Minors</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Location</th>
                <th>Role</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['firstName']) ?></td>
                    <td><?= htmlspecialchars($row['lastName']) ?></td>
                    <td><?= $row['numMinors'] ?></td>
                    <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['locationName']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No volunteers related to minor club members were found.</p>
    <?php endif; ?>

    <p><a href="index.php">Back to Dashboard</a></p>
</body>
</html>
