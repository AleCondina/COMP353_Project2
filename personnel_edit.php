<?php
include 'db.php';

// Fetch current data
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM Personnel WHERE personnelID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$person = $result->fetch_assoc();
$stmt->close();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $stmt = $conn->prepare("UPDATE Personnel SET firstName=?, lastName=?, gender=?, dateOfBirth=?, medicareNumber=?, phoneNumber=?, address=?, city=?, province=?, postalCode=?, email=?, role=?, mandate=? WHERE personnelID=?");
    $stmt->bind_param("sssssssssssssi",
        $_POST['firstName'], $_POST['lastName'], $_POST['gender'], $_POST['dateOfBirth'], $_POST['medicareNumber'], $_POST['phoneNumber'],
        $_POST['address'], $_POST['city'], $_POST['province'], $_POST['postalCode'], $_POST['email'],
        $_POST['role'], $_POST['mandate'], $id
    );
    $stmt->execute();
    $stmt->close();
    header("Location: personnel.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Personnel</title>
</head>
<body>
    <h1>Edit Personnel</h1>
    <form method="POST">
        <input type="hidden" name="edit" value="1">
        <p>SSN: <?= htmlspecialchars($person['SSN']) ?></p>
        <input name="firstName" value="<?= htmlspecialchars($person['firstName']) ?>" required><br>
        <input name="lastName" value="<?= htmlspecialchars($person['lastName']) ?>" required><br>
        <label for="gender">Gender:</label>
        <select name="gender" required>
            <option value="Not Assigned" <?= (isset($gender) && $gender === 'Not Assigned') ? 'selected' : '' ?>>Not Assigned</option>
            <option value="Male" <?= (isset($gender) && $gender === 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= (isset($gender) && $gender === 'Female') ? 'selected' : '' ?>>Female</option>
        </select>
        <input name="dateOfBirth" type="date" value="<?= $person['dateOfBirth'] ?>" required><br>
        <input name="medicareNumber" value="<?= htmlspecialchars($person['medicareNumber']) ?>"><br>
        <input name="phoneNumber" value="<?= htmlspecialchars($person['phoneNumber']) ?>"><br>
        <input name="address" value="<?= htmlspecialchars($person['address']) ?>"><br>
        <input name="city" value="<?= htmlspecialchars($person['city']) ?>"><br>
        <input name="province" value="<?= htmlspecialchars($person['province']) ?>"><br>
        <input name="postalCode" value="<?= htmlspecialchars($person['postalCode']) ?>"><br>
        <input name="email" type="email" value="<?= htmlspecialchars($person['email']) ?>"><br>
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
        <button type="submit">Update Personnel</button>
    </form>
</body>
</html>
