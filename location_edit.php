<?php
include 'db.php';

// Fetch existing location
if (!isset($_GET['id'])) {
    die("Missing location ID");
}
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM Location WHERE locationID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$location = $result->fetch_assoc();
$stmt->close();

if (!$location) {
    die("Location not found");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Location</title>
</head>
<body>
    <h1>Edit Location</h1>
    <form method="POST" action="location.php">
        <input type="hidden" name="edit" value="1">
        <input type="hidden" name="locationID" value="<?= $location['locationID'] ?>">
        <label>Name: <input name="name" value="<?= htmlspecialchars($location['name']) ?>" required></label><br>
        <label>Type:
            <select name="type" required>
                <option value="Head" <?= $location['type'] == 'Head' ? 'selected' : '' ?>>Head</option>
                <option value="Branch" <?= $location['type'] == 'Branch' ? 'selected' : '' ?>>Branch</option>
            </select>
        </label><br>
        <label>Address: <input name="address" value="<?= htmlspecialchars($location['address']) ?>"></label><br>
        <label>City: <input name="city" value="<?= htmlspecialchars($location['city']) ?>"></label><br>
        <label>Province: <input name="province" value="<?= htmlspecialchars($location['province']) ?>"></label><br>
        <label>Postal Code: <input name="postalCode" value="<?= htmlspecialchars($location['postalCode']) ?>"></label><br>
        <label>Web Address: <input name="webAddress" value="<?= htmlspecialchars($location['webAddress']) ?>"></label><br>
        <label>Max Capacity: <input type="number" name="maxCapacity" value="<?= htmlspecialchars($location['maxCapacity']) ?>"></label><br>
        <button type="submit">Update Location</button>
    </form>
    <br>
    <a href="location.php">Back to Locations</a>
</body>
</html>
