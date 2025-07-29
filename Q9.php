<!-- 9. For a given family member, get details of the secondary family member and all the club
members associated with the primary family member. Information includes first name,
last name and phone number of the secondary family member, and for every associated
club member, the club membership number, first-name, last-name, date of birth, Social
Security Number, Medicare card number, telephone number, address, city, province,
postal-code, and relationship with the secondary family member. -->


<?php
include 'db.php';

function getFamilyMembers($conn) {
    $sql = "SELECT SSN, firstName, lastName FROM FamilyMember";
    return $conn->query($sql);
}

function getRelatedData($conn, $primarySSN) {
    $sql = "
        SELECT 
            f.firstName AS secondaryFirstName,
            f.lastName AS secondaryLastName,
            f.phoneNumber AS secondaryPhone,
            cm.clubMemberID,
            cm.firstName AS memberFirstName,
            cm.lastName AS memberLastName,
            cm.dateOfBirth,
            cm.SSN,
            cm.medicareNumber,
            cm.phoneNumber,
            cm.address,
            cm.city,
            cm.province,
            cm.postalCode,
            rt.relationship
        FROM RelatedTo rt
        JOIN FamilyMember f ON rt.familyMemberSSN = f.SSN
        JOIN ClubMember cm ON rt.clubMemberID = cm.clubMemberID
        WHERE rt.familyMemberSSN = ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $primarySSN);
    $stmt->execute();
    return $stmt->get_result();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Family Member Details</title>
</head>
<body>
    <h2>Select a Family Member</h2>
    <form method="get">
        <select name="SSN" onchange="this.form.submit()">
            <option value="">-- Select Family Member --</option>
            <?php
            $members = getFamilyMembers($conn);
            while ($row = $members->fetch_assoc()) {
                $selected = ($_GET['SSN'] ?? '') === $row['SSN'] ? 'selected' : '';
                echo "<option value='{$row['SSN']}' $selected>{$row['firstName']} {$row['lastName']} ({$row['SSN']})</option>";
            }
            ?>
        </select>
    </form>

    <?php
    if (isset($_GET['SSN']) && !empty($_GET['SSN'])) {
        $results = getRelatedData($conn, $_GET['SSN']);
        
        if ($results->num_rows > 0) {
            $firstRow = $results->fetch_assoc();
            echo "<h3>Secondary Family Member:</h3>";
            echo "<p><strong>Name:</strong> {$firstRow['secondaryFirstName']} {$firstRow['secondaryLastName']}</p>";
            echo "<p><strong>Phone:</strong> {$firstRow['secondaryPhone']}</p>";

            echo "<h3>Associated Club Members:</h3>";
            echo "<table border='1'>
                    <tr>
                        <th>Club Member ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Date of Birth</th>
                        <th>SSN</th>
                        <th>Medicare Number</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Province</th>
                        <th>Postal Code</th>
                        <th>Relationship</th>
                    </tr>";
            echo "<tr>
                    <td>{$firstRow['clubMemberID']}</td>
                    <td>{$firstRow['memberFirstName']}</td>
                    <td>{$firstRow['memberLastName']}</td>
                    <td>{$firstRow['dateOfBirth']}</td>
                    <td>{$firstRow['SSN']}</td>
                    <td>{$firstRow['medicareNumber']}</td>
                    <td>{$firstRow['phoneNumber']}</td>
                    <td>{$firstRow['address']}</td>
                    <td>{$firstRow['city']}</td>
                    <td>{$firstRow['province']}</td>
                    <td>{$firstRow['postalCode']}</td>
                    <td>{$firstRow['relationship']}</td>
                  </tr>";
            while ($row = $results->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['clubMemberID']}</td>
                        <td>{$row['memberFirstName']}</td>
                        <td>{$row['memberLastName']}</td>
                        <td>{$row['dateOfBirth']}</td>
                        <td>{$row['SSN']}</td>
                        <td>{$row['medicareNumber']}</td>
                        <td>{$row['phoneNumber']}</td>
                        <td>{$row['address']}</td>
                        <td>{$row['city']}</td>
                        <td>{$row['province']}</td>
                        <td>{$row['postalCode']}</td>
                        <td>{$row['relationship']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No related data found for this family member.</p>";
        }
    }
    ?>
</body>
</html>



