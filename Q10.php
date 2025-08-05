<!-- 10. For a given location and for a given period of time, get details of all the team’s
formations recorded in the system. Details include, head coach first name and last
name, start time of the training or game session, address of the session, nature of the
session (training or game), the teams name, the score (if the session is in the future,
then score will be null), and the first name, last name and role (goalkeeper, defender,
etc.) of every player in the team. Results should be displayed sorted in ascending order
by the start time of the session. For example, the period of time could be from Jan 1st
,
2025, to May 31st, 2025. -->
<?php
include 'db.php';

function fetchLocations($conn) {
    $locations = [];
    $result = $conn->query("SELECT locationID, name, address FROM Location");
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
    return $locations;
}

$matchups = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['locationID'], $_POST['startDate'], $_POST['endDate'])) {
    $locationID = $_POST['locationID'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    $query = "
        SELECT
            TMU.formationID,
            TMU.date,
            TMU.startTime,
            TMU.sessionType,
            TMU.team1Score,
            TMU.team2Score,
            L.name AS locationName,
            L.address AS locationAddress,

            T1.teamID AS team1ID,
            T1.name AS team1Name,
            HC1.firstName AS hc1FirstName,
            HC1.lastName AS hc1LastName,

            T2.teamID AS team2ID,
            T2.name AS team2Name,
            HC2.firstName AS hc2FirstName,
            HC2.lastName AS hc2LastName,

            RI.role,
            CM.clubMemberID,
            CM.firstName,
            CM.lastName

        FROM TeamMatchUp TMU
        JOIN Location L ON TMU.locationID = L.locationID
        JOIN Team T1 ON TMU.team1ID = T1.teamID
        LEFT JOIN HeadCoachOf HCO1 ON HCO1.teamID = T1.teamID
        LEFT JOIN Personnel HC1 ON HCO1.personnelID = HC1.personnelID

        JOIN Team T2 ON TMU.team2ID = T2.teamID
        LEFT JOIN HeadCoachOf HCO2 ON HCO2.teamID = T2.teamID
        LEFT JOIN Personnel HC2 ON HCO2.personnelID = HC2.personnelID

        LEFT JOIN RoleIn RI ON RI.formationID = TMU.formationID
        LEFT JOIN ClubMember CM ON RI.clubMemberID = CM.clubMemberID

        WHERE TMU.locationID = ? AND TMU.date BETWEEN ? AND ?
        ORDER BY TMU.date, TMU.startTime, TMU.formationID
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $locationID, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $fid = $row['formationID'];

        if (!isset($matchups[$fid])) {
            $matchups[$fid] = [
                'date' => $row['date'],
                'startTime' => $row['startTime'],
                'sessionType' => $row['sessionType'],
                'team1' => [
                    'id' => $row['team1ID'],
                    'name' => $row['team1Name'],
                    'headCoach' => $row['hc1FirstName'] && $row['hc1LastName']
                        ? $row['hc1FirstName'] . ' ' . $row['hc1LastName']
                        : 'N/A'
                ],
                'team2' => [
                    'id' => $row['team2ID'],
                    'name' => $row['team2Name'],
                    'headCoach' => $row['hc2FirstName'] && $row['hc2LastName']
                        ? $row['hc2FirstName'] . ' ' . $row['hc2LastName']
                        : 'N/A'
                ],
                'team1Score' => $row['team1Score'],
                'team2Score' => $row['team2Score'],
                'locationName' => $row['locationName'],
                'locationAddress' => $row['locationAddress'],
                'players' => []
            ];
        }

        if ($row['clubMemberID']) {
            $matchups[$fid]['players'][] = [
                'firstName' => $row['firstName'],
                'lastName' => $row['lastName'],
                'role' => $row['role']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Matchup Details by Location and Time</title>
</head>
<body>
    <h2>Select a Location and Time Range</h2>
    <form method="post">
        <label for="locationID">Location:</label>
        <select name="locationID" required>
            <?php foreach (fetchLocations($conn) as $loc): ?>
                <option value="<?= $loc['locationID'] ?>"><?= htmlspecialchars($loc['name']) ?> - <?= htmlspecialchars($loc['address']) ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <label for="startDate">Start Date:</label>
        <input type="date" name="startDate" required><br><br>
        <label for="endDate">End Date:</label>
        <input type="date" name="endDate" required><br><br>
        <input type="submit" value="Fetch Matchups">
    </form>

    <?php if (!empty($matchups)): ?>
        <h3>Results</h3>
        <?php foreach ($matchups as $fid => $data): ?>
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                <strong>Matchup ID:</strong> <?= $fid ?><br>
                <strong>Date:</strong> <?= $data['date'] ?> <strong>Time:</strong> <?= $data['startTime'] ?><br>
                <strong>Type:</strong> <?= $data['sessionType'] ?><br>
                <strong>Location:</strong> <?= $data['locationName'] ?> (<?= $data['locationAddress'] ?>)<br>
                <strong>Teams:</strong>
                <ul>
                    <li><strong><?= $data['team1']['name'] ?> (ID: <?= $data['team1']['id'] ?>)</strong><br>
                        Head Coach: <?= $data['team1']['headCoach'] ?>
                    </li>
                    <li><strong><?= $data['team2']['name'] ?> (ID: <?= $data['team2']['id'] ?>)</strong><br>
                        Head Coach: <?= $data['team2']['headCoach'] ?>
                    </li>
                </ul>
                <em>Players:</em>
                <ul>
                    <?php foreach ($data['players'] as $player): ?>
                        <li><?= htmlspecialchars($player['firstName']) ?> <?= htmlspecialchars($player['lastName']) ?> - <?= $player['role'] ?></li>
                    <?php endforeach; ?>
                    <?php if (empty($data['players'])): ?>
                        <li>No players assigned</li>
                    <?php endif; ?>
                </ul>
                <?php if ($data['sessionType'] === 'Game'): ?>
                    <strong>Score:</strong> <?= $data['team1Score'] ?> - <?= $data['team2Score'] ?><br>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <p><strong>No matchups found for the selected criteria.</strong></p>
    <?php endif; ?>
</body>
</html>
