<?php
require_once 'db.php';


// Handle form submission
$emailTemplate = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clubMemberID'])) {
    $clubMemberID = $_POST['clubMemberID'];

    // Full query to get all needed details
    $sql = "
    SELECT 
        cm.firstName AS memberFirstName,
        cm.lastName AS memberLastName,
        cm.email AS memberEmail,
        ri.role AS memberRole, 
        t.name,
        s.date,
        s.startTime,
        s.sessionType,
        l.address,
        COALESCE(hc1.firstName, hc2.firstName) AS coachFirstName,
        COALESCE(hc1.lastName, hc2.lastName) AS coachLastName,
        COALESCE(hc1.email, hc2.email) AS coachEmail

    FROM ClubMember cm
    JOIN InTeam it ON cm.clubMemberID = it.clubMemberID
    JOIN Team t ON it.teamID = t.teamID
    JOIN TeamLocated tl ON t.teamID = tl.teamID
    JOIN Location l ON tl.locationID = l.locationID
    JOIN TeamMatchUp s ON t.teamID = s.team1ID OR t.teamID = s.team2ID

    -- First head coach source: RoleIn
    LEFT JOIN RoleIn ri ON ri.formationID = s.formationID AND ri.role = 'head coach'
    LEFT JOIN ClubMember hc1 ON ri.clubMemberID = hc1.clubMemberID

    -- Second head coach source: HeadCoachOf
    LEFT JOIN HeadCoachOf hco ON hco.teamID = t.teamID
    LEFT JOIN ClubMember hc2 ON hco.personnelID = hc2.clubMemberID

    WHERE cm.clubMemberID = ?
        AND s.date BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL 7 DAY
    ORDER BY s.date, s.startTime

    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $clubMemberID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $emailTemplate = "<h3>Email Templates</h3>";
        while ($row = $result->fetch_assoc()) {
            // Format date & time
            $sessionDate = date("D j-M-Y", strtotime($row['date']));
            $sessionTime = date("g:i A", strtotime($row['startTime']));

            // Subject
            $subject = "{$row['name']} {$sessionDate} {$sessionTime} {$row['sessionType']} session";

            // Email body
            $emailBody = "
                Dear {$row['memberFirstName']} {$row['memberLastName']},

                This is to inform you of your upcoming {$row['sessionType']} session.

                - **Role**: {$row['memberRole']}
                - **Team**: {$row['name']}
                - **Date**: {$sessionDate}
                - **Time**: {$sessionTime}
                - **Address**: {$row['address']}

                **Head Coach Info:**
                - Name: {$row['coachFirstName']} {$row['coachLastName']}
                - Email: {$row['coachEmail']}

                Please arrive on time and contact the head coach if you have any questions.

                Best regards,
                The Club Admin Team
            ";

            // Display the formatted output
            $emailTemplate .= "
            <strong>To:</strong> {$row['memberEmail']}<br>
            <strong>Subject:</strong> $subject<br><br>
            <pre style='white-space: pre-wrap; background:#f0f0f0; padding:10px; border-radius:5px;'>$emailBody</pre>
            ";
        }
    } else {
        $emailTemplate = "<p style='color:red;'>No session or data found for selected member.</p>";

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Club Member Schedule Email</title>
</head>
<body>
    <h2>Select a Club Member in a Team For Upcoming Session Email Generation</h2>

    <form method="POST">
        <label for="clubMemberID">Club Member:</label>
        <select name="clubMemberID" required>
            <option value="">-- Select Member --</option>
            <?php
            // Populate dropdown
            $memberQuery = "
                SELECT DISTINCT CM.clubMemberID, CM.firstName, CM.lastName
                FROM ClubMember CM
                JOIN InTeam IT ON CM.clubMemberID = IT.clubMemberID
                ORDER BY CM.lastName ASC;
            ";
            $members = $conn->query($memberQuery);
            while ($m = $members->fetch_assoc()) {
                $id = $m['clubMemberID'];
                $name = $m['firstName'] . " " . $m['lastName'];
                echo "<option value='$id'" . (isset($clubMemberID) && $clubMemberID == $id ? " selected" : "") . ">$name</option>";
            }
            ?>
        </select>
        <button type="submit">Generate Email</button>
    </form>

    <br>
    <?= $emailTemplate ?>

</body>
</html>

<?php
$conn->close();
?>