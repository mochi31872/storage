<?php
$conn = mysqli_connect("localhost", "root", "", "ticketdb");
$result = mysqli_query($conn, "
    SELECT customer_name, ticket_type, age_group, quantity, price, quantity * price AS total, order_time 
    FROM ticket_orders ORDER BY order_time DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>전체 주문 내역</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #aaa; text-align: center; }
        th { background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <h2>✨ 전체 주문 내역</h2>
    <table>
        <tr>
            <th>고객명</th><th>구분</th><th>연령대</th><th>수량</th><th>단가</th><th>합계</th><th>시간</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['customer_name'] ?></td>
            <td><?= $row['ticket_type'] ?></td>
            <td><?= $row['age_group'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= number_format($row['price']) ?></td>
            <td><?= number_format($row['total']) ?></td>
            <td><?= $row['order_time'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
