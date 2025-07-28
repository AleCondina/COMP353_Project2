<?php
include 'db.php';

// Handle Add Payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $clubMemberID = $_POST['clubMemberID'];
    $amount = $_POST['amount'];

    // Insert into Payment table
    $stmt = $conn->prepare("INSERT INTO Payment (amount, paymentDate) VALUES (?, NOW())");
    $stmt->bind_param("d", $amount);
    $stmt->execute();
    $paymentID = $conn->insert_id; // get last inserted payment ID
    $stmt->close();

    // Link to MakesPayment table
    $stmt = $conn->prepare("INSERT INTO MakesPayment (clubMemberID, paymentID) VALUES (?, ?)");
    $stmt->bind_param("ii", $clubMemberID, $paymentID);
    $stmt->execute();
    $stmt->close();

    header("Location: MakesPayment.php");
    exit;
}

// Fetch all MakesPayment records
$result = $conn->query("SELECT mp.clubMemberID, mp.paymentID, cm.firstName, cm.lastName, p.amount, p.paymentDate 
                        FROM MakesPayment mp
                        JOIN ClubMember cm ON mp.clubMemberID = cm.clubMemberID
                        JOIN Payment p ON mp.paymentID = p.paymentID");

// Fetch ClubMembers for dropdown
$members = $conn->query("SELECT clubMemberID, firstName, lastName FROM ClubMember");
?>

<!DOCTYPE html>
<html>
<head>
    <title>MakesPayment</title>
</head>
<body>

    <h2>Record New Payment</h2>
    <form method="POST">
        <input type="hidden" name="add" value="1">

        <label>Club Member:</label>
        <select name="clubMemberID" required>
            <option value="">Select...</option>
            <?php while ($row = $members->fetch_assoc()): ?>
                <option value="<?= $row['clubMemberID'] ?>">
                    <?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?> (ID: <?= $row['clubMemberID'] ?>)
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Payment Amount:</label>
        <input type="number" name="amount" step="0.01" min="0.01" required><br><br>

        <button type="submit">Make Payment</button>
    </form>

    <h1>All Payments</h1>
    <table border="1">
        <tr>
            <th>Club Member</th><th>Payment ID</th><th>Amount</th><th>Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?> (ID: <?= $row['clubMemberID'] ?>)</td>
            <td><?= $row['paymentID'] ?></td>
            <td>$<?= $row['amount'] ?></td>
            <td><?= $row['paymentDate'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
