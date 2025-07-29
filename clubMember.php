<?php
require_once 'db.php';

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM ClubMember WHERE clubMemberID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: clubMember.php");
    exit();
}

// Handle creation
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("INSERT INTO ClubMember 
        (firstName, lastName, dateOfBirth, height, weight, SSN, medicareNumber, phoneNumber, address, city, province, postalCode, status, gender)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssddsssssssss",
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['dateOfBirth'],
        $_POST['height'],
        $_POST['weight'],
        $_POST['SSN'],
        $_POST['medicareNumber'],
        $_POST['phoneNumber'],
        $_POST['address'],
        $_POST['city'],
        $_POST['province'],
        $_POST['postalCode'],
        $_POST['status'], 
        $_POST['gender']
    );

    $stmt->execute();
    $stmt->close();
    header("Location: clubMember.php");
    exit();
}

// Fetch all Club Members
$result = $conn->query("SELECT * FROM ClubMember");
?>

<h2>Club Members</h2>

<form method="POST">
    <input type="text" name="firstName" placeholder="First Name" required>
    <input type="text" name="lastName" placeholder="Last Name" required>

    <label for="gender">Gender:</label>
    <select name="gender" required>
        <option value="Not Assigned" <?= (isset($gender) && $gender === 'Not Assigned') ? 'selected' : '' ?>>Not Assigned</option>
        <option value="Male" <?= (isset($gender) && $gender === 'Male') ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= (isset($gender) && $gender === 'Female') ? 'selected' : '' ?>>Female</option>
    </select>

    <input type="date" name="dateOfBirth" required>
    <input type="number" step="0.01" name="height" placeholder="Height (cm)">
    <input type="number" step="0.01" name="weight" placeholder="Weight (kg)">
    <input type="text" name="SSN" placeholder="SSN (XXX-XX-XXXX)" maxlength="11" required>
    <input type="text" name="medicareNumber" placeholder="Medicare Number" maxlength="12" required>
    <input type="text" name="phoneNumber" placeholder="Phone Number">
    <input type="text" name="address" placeholder="Address">
    <input type="text" name="city" placeholder="City">
    <input type="text" name="province" placeholder="Province">
    <input type="text" name="postalCode" placeholder="Postal Code">
    
    <select name="status" required>
        <option value="Active">Active</option>
        <option value="Inactive" selected>Inactive</option>
    </select>

    <button type="submit">Add Club Member</button>
</form>


<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>ID</th><th>First</th><th>Last</th><th>DOB</th><th>Height</th><th>Weight</th>
        <th>SSN</th><th>Medicare</th><th>Phone</th><th>Address</th><th>City</th>
        <th>Province</th><th>Postal Code</th><th>Gender</th><th>Status</th><th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['clubMemberID']) ?></td>
        <td><?= htmlspecialchars($row['firstName']) ?></td>
        <td><?= htmlspecialchars($row['lastName']) ?></td>
        <td><?= htmlspecialchars($row['dateOfBirth']) ?></td>
        <td><?= htmlspecialchars($row['height']) ?></td>
        <td><?= htmlspecialchars($row['weight']) ?></td>
        <td><?= htmlspecialchars($row['SSN']) ?></td>
        <td><?= htmlspecialchars($row['medicareNumber']) ?></td>
        <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
        <td><?= htmlspecialchars($row['address']) ?></td>
        <td><?= htmlspecialchars($row['city']) ?></td>
        <td><?= htmlspecialchars($row['province']) ?></td>
        <td><?= htmlspecialchars($row['postalCode']) ?></td>
        <td><?= htmlspecialchars($row['gender']) ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td>
            <a href="clubMember_edit.php?id=<?= $row['clubMemberID'] ?>">Edit</a>
            <a href="clubMember.php?delete=<?= $row['clubMemberID'] ?>" onclick="return confirm('Delete this club member?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
