<?php
session_start();
include("db.php");
include("includes/header.php");
?>

<h2>Manage Orders</h2>

<table class="table table-bordered">
<thead>
<tr>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Total Price</th>
    <th>Status</th>
    <th>Payment</th>
    <th>Created At</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php
$res = $conn->query("SELECT o.*, u.name AS customer FROM orders o LEFT JOIN users u ON o.user_id=u.user_id ORDER BY o.created_at DESC");
while($row = $res->fetch_assoc()){
    echo "<tr>
    <td>#{$row['order_id']}</td>
    <td>".htmlspecialchars($row['customer'])."</td>
    <td>₹".number_format($row['total_price'],2)."</td>
    <td>{$row['status']}</td>
    <td>{$row['payment_method']}</td>
    <td>{$row['created_at']}</td>
    <td>
        <a href='view_order.php?id={$row['order_id']}' class='btn btn-sm btn-primary'>View</a>
    </td>
    </tr>";
}
?>
</tbody>
</table>

<?php include("includes/footer.php"); ?>
