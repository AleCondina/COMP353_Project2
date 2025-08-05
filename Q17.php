<!-- 17. For the given location, get the list of all family members who have currently active
club members associated with them and are also head coaches for the same location.
Information includes first name, last name, and phone number of the family member.
A family member is considered to be a head coach if she/he is assigned as a head coach
to at least one team formation session in the same location. -->


<?php
include 'db.php';

$locations = $conn->query("SELECT locationID, name FROM Location");

$locationID = isset($_GET['locationID']) ? intval($_GET['locationID']) : null;
$results    = [];

if ($locationID) {
    $stmt = $conn->prepare("
        /* Family member who is ALSO a club‑member … */
        SELECT DISTINCT
               FM.firstName,
               FM.lastName,
               FM.phoneNumber
        FROM   FamilyMember      FM
        JOIN   ClubMember        CM  ON CM.SSN = FM.SSN          /* same person */
        JOIN   RoleIn  FP  ON FP.clubMemberID = CM.clubMemberID
        JOIN   TeamMatchUp     TF  ON TF.formationID  = FP.formationID
        JOIN   EnrolledAt        EA  ON EA.clubMemberID = CM.clubMemberID
        WHERE  CM.status = 'Active'
          AND  EA.endDate is null
          AND  FP.role   = 'head coach'     /* they coach at least one session   */
          AND  EA.locationID = ?            /* person is enrolled at location    */
          AND  TF.locationID = EA.locationID/* formation held at same location   */
    ");
    $stmt->bind_param("i", $locationID);
    $stmt->execute();
    $results = $stmt->get_result();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Head‑Coach Family Members by Location</title>
</head>
<body>
<h2>Head‑coach family members with ACTIVE club‑member profiles</h2>

<form method="GET">
    <label>Select Location:</label>
    <select name="locationID" onchange="this.form.submit()" required>
        <option value="">Choose…</option>
        <?php while ($loc = $locations->fetch_assoc()): ?>
            <option value="<?= $loc['locationID'] ?>"
                <?= $locationID == $loc['locationID'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($loc['name']) ?> (ID <?= $loc['locationID'] ?>)
            </option>
        <?php endwhile; ?>
    </select>
</form>

<?php if ($locationID): ?>
    <h3>Results for location ID <?= $locationID ?></h3>
    <table border="1" cellpadding="6">
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Phone Number</th>
        </tr>
        <?php if ($results && $results->num_rows): ?>
            <?php while ($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['firstName']) ?></td>
                    <td><?= htmlspecialchars($row['lastName']) ?></td>
                    <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No matching family‑members found for this location.</td></tr>
        <?php endif; ?>
    </table>
<?php endif; ?>

</body>
</html>
