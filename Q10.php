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

$formations = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['locationID'], $_POST['startDate'], $_POST['endDate'])) {
    $locationID = $_POST['locationID'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    $query = "
        SELECT
            TF.formationID,
            TF.date,
            TF.startTime,
            TF.sessionType,
            TF.team1Score,
            TF.team2Score,
            L.name AS locationName,
            L.address AS locationAddress,
            T1.teamID AS team1ID,
            T1.name AS team1Name,
            T2.teamID AS team2ID,
            T2.name AS team2Name,
            FP.teamID AS playerTeamID,
            FP.role,
            CM.clubMemberID,
            CM.firstName,
            CM.lastName
        FROM TeamFormation TF
        JOIN Location L ON TF.locationID = L.locationID
        JOIN Team T1 ON TF.team1ID = T1.teamID
        JOIN Team T2 ON TF.team2ID = T2.teamID
        LEFT JOIN FormationPlayers FP ON TF.formationID = FP.formationID
        LEFT JOIN ClubMember CM ON FP.clubMemberID = CM.clubMemberID
        WHERE TF.locationID = ? AND TF.date BETWEEN ? AND ?
        ORDER BY TF.date, TF.startTime, TF.formationID, FP.teamID
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $locationID, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $fid = $row['formationID'];

        if (!isset($formations[$fid])) {
            $formations[$fid] = [
                'date' => $row['date'],
                'startTime' => $row['startTime'],
                'sessionType' => $row['sessionType'],
                'team1' => ['id' => $row['team1ID'], 'name' => $row['team1Name']],
                'team2' => ['id' => $row['team2ID'], 'name' => $row['team2Name']],
                'team1Score' => $row['team1Score'],
                'team2Score' => $row['team2Score'],
                'locationName' => $row['locationName'],
                'locationAddress' => $row['locationAddress'],
                'teams' => []
            ];

            // Ensure both teams exist even if they have no players
            foreach ([$row['team1ID'], $row['team2ID']] as $tid) {
                $formations[$fid]['teams'][$tid] = [
                    'players' => [],
                    'headCoach' => null
                ];
            }
        }

        if ($row['playerTeamID']) {
            $tid = $row['playerTeamID'];
            if ($row['role'] === 'Head Coach') {
                $formations[$fid]['teams'][$tid]['headCoach'] = $row['firstName'] . ' ' . $row['lastName'];
            } else {
                $formations[$fid]['teams'][$tid]['players'][] = [
                    'firstName' => $row['firstName'],
                    'lastName' => $row['lastName'],
                    'role' => $row['role']
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Formation Details by Location and Time</title>
</head>
<body>
    <h2>Select a Location and Time Range</h2>
    <form method="post">
        <label for="locationID">Location:</label>
        <select name="locationID" required>
            <?php
            foreach (fetchLocations($conn) as $loc) {
                echo "<option value='{$loc['locationID']}'>" . htmlspecialchars($loc['name']) . " - " . htmlspecialchars($loc['address']) . "</option>";
            }
            ?>
        </select><br><br>
        <label for="startDate">Start Date:</label>
        <input type="date" name="startDate" required><br><br>
        <label for="endDate">End Date:</label>
        <input type="date" name="endDate" required><br><br>
        <input type="submit" value="Fetch Formations">
    </form>

    <?php if (!empty($formations)): ?>
        <h3>Results</h3>
        <?php foreach ($formations as $fid => $data): ?>
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                <strong>Formation ID:</strong> <?= $fid ?><br>
                <strong>Date:</strong> <?= $data['date'] ?> <strong>Time:</strong> <?= $data['startTime'] ?><br>
                <strong>Type:</strong> <?= $data['sessionType'] ?><br>
                <strong>Location:</strong> <?= $data['locationName'] ?> (<?= $data['locationAddress'] ?>)<br>
                <strong>Teams:</strong><br>
                <ul>
                    <?php foreach (['team1', 'team2'] as $key): ?>
                        <?php $teamID = $data[$key]['id']; ?>
                        <li>
                            <strong><?= $data[$key]['name'] ?> (ID: <?= $teamID ?>)</strong><br>
                            Head Coach: <?= $data['teams'][$teamID]['headCoach'] ?? 'N/A' ?><br>
                            <em>Players:</em>
                            <ul>
                                <?php foreach ($data['teams'][$teamID]['players'] as $player): ?>
                                    <li><?= htmlspecialchars($player['firstName']) ?> <?= htmlspecialchars($player['lastName']) ?> - <?= $player['role'] ?></li>
                                <?php endforeach; ?>
                                <?php if (empty($data['teams'][$teamID]['players'])): ?>
                                    <li>No players assigned</li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($data['sessionType'] === 'Game'): ?>
                    <strong>Score:</strong> <?= $data['team1Score'] ?> - <?= $data['team2Score'] ?><br>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <p><strong>No formations found for the selected criteria.</strong></p>
    <?php endif; ?>
</body>
</html>
