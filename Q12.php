<!-- For a given period of time, give a report on the teams’ formations for all the locations.
For each location, the report should include the location name, the total number of
training sessions, the total number of players in the training sessions, the total number
of game sessions, the total number of players in the game sessions. Results should only
include locations that have at least four game sessions. Results should be displayed
sorted in descending order by the total number of game sessions. For example, the
period of time could be from Jan 1st, 2025, to May 31st, 2025 -->

<?php
include 'db.php';

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$startDate = isset($_GET['start_date']) ? sanitize_input($_GET['start_date']) : '';
$endDate = isset($_GET['end_date']) ? sanitize_input($_GET['end_date']) : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Formation Report Per Location</title>
</head>
<body>
<h2>Team Formation Report (All Locations)</h2>

<form method="get">
    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" required value="<?php echo $startDate; ?>">
    
    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" required value="<?php echo $endDate; ?>">

    <input type="submit" value="Generate Report">
</form>

<?php
if (!empty($startDate) && !empty($endDate)) {
    $sql = "
        SELECT 
            L.name,
            COUNT(CASE WHEN TF.sessionType = 'Training' THEN 1 END) AS totalTrainings,
            COUNT(DISTINCT CASE WHEN TF.sessionType = 'Training' THEN FP.ClubMemberID END) AS trainingPlayers,
            COUNT(CASE WHEN TF.sessionType = 'Game' THEN 1 END) AS totalGames,
            COUNT(DISTINCT CASE WHEN TF.sessionType = 'Game' THEN FP.ClubMemberID END) AS gamePlayers
        FROM TeamFormation TF
        JOIN Location L ON TF.locationID = L.locationID
        LEFT JOIN FormationPlayers FP ON TF.formationID = FP.formationID
        WHERE TF.date BETWEEN ? AND ?
        GROUP BY L.locationID
        HAVING totalGames >= 4
        ORDER BY totalGames DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3>Results from $startDate to $endDate</h3>";
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='8'>
                <tr>
                    <th>Location Name</th>
                    <th>Total Training Sessions</th>
                    <th>Total Players in Training</th>
                    <th>Total Game Sessions</th>
                    <th>Total Players in Games</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['totalTrainings']}</td>
                    <td>{$row['trainingPlayers']}</td>
                    <td>{$row['totalGames']}</td>
                    <td>{$row['gamePlayers']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No locations found with at least 4 game sessions in this period.</p>";
    }

    $stmt->close();
}
$conn->close();
?>

</body>
</html>


