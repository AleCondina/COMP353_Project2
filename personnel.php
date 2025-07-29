<?php
include 'db.php';

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $valid_roles = ['Administrator', 'Captain', 'Coach', 'Assistant Coach', 'General Manager', 'Other'];
    $valid_mandates = ['Volunteer', 'Salaried'];

    $role = $_POST['role'];
    $mandate = $_POST['mandate'];

    if (!in_array($role, $valid_roles) || !in_array($mandate, $valid_mandates)) {
        die("Invalid role or mandate value.");
    }

    $stmt = $conn->prepare("INSERT INTO Personnel (SSN, firstName, lastName, gender, dateOfBirth, medicareNumber, phoneNumber, address, city, province, postalCode, email, role, mandate)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssss",
        $_POST['SSN'], $_POST['firstName'], $_POST['lastName'],$_POST['gender'], $_POST['dateOfBirth'], $_POST['medicareNumber'],
        $_POST['phoneNumber'], $_POST['address'], $_POST['city'], $_POST['province'], $_POST['postalCode'],
        $_POST['email'], $role, $mandate
    );
    $stmt->execute();
    $stmt->close();
    header("Location: personnel.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM Personnel WHERE personnelID = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    $stmt->close();
    header("Location: personnel.php");
    exit;
}

// Fetch all
$result = $conn->query("SELECT * FROM Personnel");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Personnel</title>
</head>
<body>

<h2>Add Personnel</h2>
    <form method="POST">
        <input type="hidden" name="add" value="1">
        <input name="SSN" placeholder="SSN" required><br>
        <input name="firstName" placeholder="First Name" required><br>
        <input name="lastName" placeholder="Last Name" required><br>
         <label for="gender">Gender:</label>
        <select name="gender" required>
            <option value="Not Assigned" <?= (isset($gender) && $gender === 'Not Assigned') ? 'selected' : '' ?>>Not Assigned</option>
            <option value="Male" <?= (isset($gender) && $gender === 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= (isset($gender) && $gender === 'Female') ? 'selected' : '' ?>>Female</option>
        </select>
        <input name="dateOfBirth" type="date" required><br>
        <input name="medicareNumber" placeholder="Medicare Number"><br>
        <input name="phoneNumber" placeholder="Phone Number"><br>
        <input name="address" placeholder="Address"><br>
        <input name="city" placeholder="City"><br>
        <input name="province" placeholder="Province"><br>
        <input name="postalCode" placeholder="Postal Code"><br>
        <input name="email" type="email" placeholder="Email"><br>
        <select name="role" required>
            <option value="">Select Role</option>
            <option>Administrator</option>
            <option>Captain</option>
            <option>Coach</option>
            <option>Assistant Coach</option>
            <option>General Manager</option>
            <option>Other</option>
        </select>
        <select name="mandate" required>
            <option value="">Select Mandate</option>
            <option>Volunteer</option>
            <option>Salaried</option>
        </select>
        <button type="submit">Add Personnel</button>
    </form>

    <h1>All Personnel</h1>
    <table border="1">
        <tr>
            <th>Personnel ID</th><th>SSN</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>Date of Birth</th>
            <th>Medicare Number</th><th>Phone Number</th><th>Address</th><th>City</th><th>Province</th>
            <th>Postal Code</th><th>Email</th><th>Role</th><th>Mandate</th><th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['personnelID']) ?></td>
            <td><?= htmlspecialchars($row['SSN']) ?></td>
            <td><?= htmlspecialchars($row['firstName']) ?></td>
            <td><?= htmlspecialchars($row['lastName']) ?></td>
            <td><?= htmlspecialchars($row['gender']) ?></td>
            <td><?= htmlspecialchars($row['dateOfBirth']) ?></td>
            <td><?= htmlspecialchars($row['medicareNumber']) ?></td>
            <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['city']) ?></td>
            <td><?= htmlspecialchars($row['province']) ?></td>
            <td><?= htmlspecialchars($row['postalCode']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td><?= htmlspecialchars($row['mandate']) ?></td>
            <td>
                <a href="personnel_edit.php?id=<?= $row['personnelID'] ?>">Edit</a>
                <a href="personnel.php?delete=<?= $row['personnelID'] ?>" onclick="return confirm('Delete this personnel?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    
</body>
</html>
