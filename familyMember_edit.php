<?php
require_once 'db.php';

if (!isset($_GET['ssn'])) {
    header("Location: familyMember.php");
    exit();
}

$ssn = $_GET['ssn'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("UPDATE FamilyMember SET 
        firstName = ?, lastName = ?, dateOfBirth = ?, medicareNumber = ?, phoneNumber = ?, 
        address = ?, city = ?, province = ?, postalCode = ?, email = ?
        WHERE SSN = ?");
    
    $stmt->bind_param(
        "sssssssssss",
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['dateOfBirth'],
        $_POST['medicareNumber'],
        $_POST['phoneNumber'],
        $_POST['address'],
        $_POST['city'],
        $_POST['province'],
        $_POST['postalCode'],
        $_POST['email'],
        $ssn
    );

    $stmt->execute();
    $stmt->close();
    header("Location: familyMember.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM FamilyMember WHERE SSN = ?");
$stmt->bind_param("s", $ssn);
$stmt->execute();
$result = $stmt->get_result();
$fm = $result->fetch_assoc();
$stmt->close();

if (!$fm) {
    echo "Family member not found.";
    exit();
}
?>

<h2>Edit Family Member</h2>
<form method="POST">
    <label>SSN: <?= htmlspecialchars($fm['SSN']) ?></label><br><br>
    <input type="text" name="firstName" value="<?= htmlspecialchars($fm['firstName']) ?>" placeholder="First Name">
    <input type="text" name="lastName" value="<?= htmlspecialchars($fm['lastName']) ?>" placeholder="Last Name">
    <input type="date" name="dateOfBirth" value="<?= htmlspecialchars($fm['dateOfBirth']) ?>">
    <input type="text" name="medicareNumber" value="<?= htmlspecialchars($fm['medicareNumber']) ?>" required>
    <input type="text" name="phoneNumber" value="<?= htmlspecialchars($fm['phoneNumber']) ?>">
    <input type="text" name="address" value="<?= htmlspecialchars($fm['address']) ?>">
    <input type="text" name="city" value="<?= htmlspecialchars($fm['city']) ?>">
    <input type="text" name="province" value="<?= htmlspecialchars($fm['province']) ?>">
    <input type="text" name="postalCode" value="<?= htmlspecialchars($fm['postalCode']) ?>">
    <input type="email" name="email" value="<?= htmlspecialchars($fm['email']) ?>">
    <button type="submit">Update Family Member</button>
</form>
