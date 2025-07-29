<?php
require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: clubMember.php");
    exit();
}

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("UPDATE ClubMember SET 
        firstName = ?, lastName = ?, dateOfBirth = ?, height = ?, weight = ?, SSN = ?, medicareNumber = ?, phoneNumber = ?, 
        address = ?, city = ?, province = ?, postalCode = ?, status = ?, gender = ?
        WHERE clubMemberID = ?");

    $stmt->bind_param(
         "sssddsssssssssi",
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
        $_POST['gender'],
        $id
    );

    $stmt->execute();
    $stmt->close();
    header("Location: clubMember.php");
    exit();
}

// Fetch club member
$stmt = $conn->prepare("SELECT * FROM ClubMember WHERE clubMemberID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
$stmt->close();

if (!$member) {
    echo "Club member not found.";
    exit();
}
?>
<h2>Edit Club Member</h2>

<form method="POST">
    <input type="text" name="firstName" placeholder="First Name" value="<?= htmlspecialchars($member['firstName']) ?>" required>
    <input type="text" name="lastName" placeholder="Last Name" value="<?= htmlspecialchars($member['lastName']) ?>" required>

    <label for="gender">Gender:</label>
    <select name="gender" required>
        <option value="Not Assigned" <?= ($member['gender'] === 'Not Assigned') ? 'selected' : '' ?>>Not Assigned</option>
        <option value="Male" <?= ($member['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= ($member['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
    </select>

    <input type="date" name="dateOfBirth" value="<?= $member['dateOfBirth'] ?>" required>
    <input type="number" step="0.01" name="height" placeholder="Height (cm)" value="<?= $member['height'] ?>">
    <input type="number" step="0.01" name="weight" placeholder="Weight (kg)" value="<?= $member['weight'] ?>">
    <input type="text" name="SSN" placeholder="SSN (XXX-XX-XXXX)" maxlength="11" value="<?= htmlspecialchars($member['SSN']) ?>" required>
    <input type="text" name="medicareNumber" placeholder="Medicare Number" maxlength="12" value="<?= htmlspecialchars($member['medicareNumber']) ?>" required>
    <input type="text" name="phoneNumber" placeholder="Phone Number" value="<?= htmlspecialchars($member['phoneNumber']) ?>">
    <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($member['address']) ?>">
    <input type="text" name="city" placeholder="City" value="<?= htmlspecialchars($member['city']) ?>">
    <input type="text" name="province" placeholder="Province" value="<?= htmlspecialchars($member['province']) ?>">
    <input type="text" name="postalCode" placeholder="Postal Code" value="<?= htmlspecialchars($member['postalCode']) ?>">

    <label for="status">Status:</label>
    <select name="status" required>
        <option value="Active" <?= ($member['status'] === 'Active') ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= ($member['status'] === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
    </select>

    <button type="submit">Update Club Member</button>
</form>
