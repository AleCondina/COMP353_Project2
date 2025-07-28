<?php
require_once 'db.php';

// Handle delete
if (isset($_GET['delete'])) {
    $ssn = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM FamilyMember WHERE SSN = ?");
    $stmt->bind_param("s", $ssn);
    $stmt->execute();
    $stmt->close();
    header("Location: familyMember.php");
    exit();
}

// Handle add
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("INSERT INTO FamilyMember 
        (SSN, firstName, lastName, dateOfBirth, medicareNumber, phoneNumber, address, city, province, postalCode, email)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssssssssss",
        $_POST['SSN'],
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['dateOfBirth'],
        $_POST['medicareNumber'],
        $_POST['phoneNumber'],
        $_POST['address'],
        $_POST['city'],
        $_POST['province'],
        $_POST['postalCode'],
        $_POST['email']
    );

    $stmt->execute();
    $stmt->close();
    header("Location: familyMember.php");
    exit();
}

// Fetch all family members
$result = $conn->query("SELECT * FROM FamilyMember");
?>

<h2>Family Members</h2>

<form method="POST">
    <input type="text" name="SSN" placeholder="SSN" required>
    <input type="text" name="firstName" placeholder="First Name">
    <input type="text" name="lastName" placeholder="Last Name">
    <input type="date" name="dateOfBirth" required>
    <input type="text" name="medicareNumber" placeholder="Medicare Number" required>
    <input type="text" name="phoneNumber" placeholder="Phone Number">
    <input type="text" name="address" placeholder="Address">
    <input type="text" name="city" placeholder="City">
    <input type="text" name="province" placeholder="Province">
    <input type="text" name="postalCode" placeholder="Postal Code">
    <input type="email" name="email" placeholder="Email">
    <button type="submit">Add Family Member</button>
</form>

<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>SSN</th><th>First Name</th><th>Last Name</th><th>Date of Birth</th>
        <th>Medicare Number</th><th>Phone</th><th>Address</th><th>City</th><th>Province</th>
        <th>Postal Code</th><th>Email</th><th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['SSN']) ?></td>
        <td><?= htmlspecialchars($row['firstName']) ?></td>
        <td><?= htmlspecialchars($row['lastName']) ?></td>
        <td><?= htmlspecialchars($row['dateOfBirth']) ?></td>
        <td><?= htmlspecialchars($row['medicareNumber']) ?></td>
        <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
        <td><?= htmlspecialchars($row['address']) ?></td>
        <td><?= htmlspecialchars($row['city']) ?></td>
        <td><?= htmlspecialchars($row['province']) ?></td>
        <td><?= htmlspecialchars($row['postalCode']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td>
            <a href="familyMember_edit.php?ssn=<?= urlencode($row['SSN']) ?>">Edit</a>
            <a href="familyMember.php?delete=<?= urlencode($row['SSN']) ?>" onclick="return confirm('Delete this family member?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
