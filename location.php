
<?php 
include 'db.php';

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO Location (name, type, address, city, province, postalCode, webAddress, maxCapacity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $_POST['name'], $_POST['type'], $_POST['address'], $_POST['city'], $_POST['province'], $_POST['postalCode'], $_POST['webAddress'], $_POST['maxCapacity']);
    $stmt->execute();
    $stmt->close();
    header("Location: location.php");
    exit;
}

// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $stmt = $conn->prepare("UPDATE Location SET name=?, type=?, address=?, city=?, province=?, postalCode=?, webAddress=?, maxCapacity=? WHERE locationID=?");
    $stmt->bind_param("sssssssii", $_POST['name'], $_POST['type'], $_POST['address'], $_POST['city'], $_POST['province'], $_POST['postalCode'], $_POST['webAddress'], $_POST['maxCapacity'], $_POST['locationID']);
    $stmt->execute();
    $stmt->close();
    header("Location: location.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM Location WHERE locationID = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    $stmt->close();
    header("Location: location.php");
    exit;
}

// Fetch All
$result = $conn->query("SELECT * FROM Location");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Locations</title>
</head>
<body>
    <h1>All Locations</h1>
    <table border="1">
        <tr>
            <th>Name</th><th>Type</th><th>Address</th><th>City</th><th>Province</th>
            <th>Postal Code</th><th>Web Address</th><th>Max Capacity</th><th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['city']) ?></td>
            <td><?= htmlspecialchars($row['province']) ?></td>
            <td><?= htmlspecialchars($row['postalCode']) ?></td>
            <td><?= htmlspecialchars($row['webAddress']) ?></td>
            <td><?= htmlspecialchars($row['maxCapacity']) ?></td>
            <td>
                <a href="location_edit.php?id=<?= $row['locationID'] ?>">Edit</a>
                <a href="location.php?delete=<?= $row['locationID'] ?>" onclick="return confirm('Delete this location?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Add Location</h2>
    <form method="POST">
        <input type="hidden" name="add" value="1">
        <input name="name" placeholder="Name" required><br>
        <select name="type" required>
            <option value="Head">Head</option>
            <option value="Branch">Branch</option>
        </select><br>
        <input name="address" placeholder="Address"><br>
        <input name="city" placeholder="City"><br>
        <input name="province" placeholder="Province"><br>
        <input name="postalCode" placeholder="Postal Code"><br>
        <input name="webAddress" placeholder="Web Address"><br>
        <input name="maxCapacity" type="number" placeholder="Max Capacity"><br>
        <button type="submit">Add Location</button>
    </form>
</body>
</html>
