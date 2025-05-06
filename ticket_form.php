<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "ticketdb");
if (!$conn) {
    die("MySQL 연결 실패: " . mysqli_connect_error());
}

$prices = [
    "입장권" => ["어린이" => 7000, "어른" => 10000],
    "BIG3" => ["어린이" => 12000, "어른" => 16000],
    "자유이용권" => ["어린이" => 21000, "어른" => 26000],
    "연간이용권" => ["어린이" => 70000, "어른" => 90000],
];

$total_details = [];
$final_total = 0;
$order_submitted = false;
$name = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["customer_name"];
    $final_total = 0;

    foreach ($_POST["quantity"] as $type => $groups) {
        foreach ($groups as $group => $qty) {
            $qty = intval($qty);
            if ($qty > 0) {
                $price = $prices[$type][$group];
                $total = $qty * $price;
                $final_total += $total;

                $sql = "INSERT INTO ticket_orders (customer_name, ticket_type, age_group, quantity, price)
                        VALUES ('$name', '$type', '$group', $qty, $price)";
                mysqli_query($conn, $sql);

                $total_details[] = "$group $type {$qty}매";
            }
        }
    }

    $_SESSION['summary'] = [
        'name' => $name,
        'details' => $total_details,
        'total' => $final_total
    ];
    header("Location: ticket_form.php");
    exit;
}

if (isset($_SESSION['summary'])) {
    $order_submitted = true;
    $name = $_SESSION['summary']['name'];
    $total_details = $_SESSION['summary']['details'];
    $final_total = $_SESSION['summary']['total'];
    unset($_SESSION['summary']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>티켓 주문</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            width: 720px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        input[type="text"] {
            padding: 6px;
            width: 300px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        select {
            padding: 5px;
        }

        input[type="submit"] {
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: center;
        }

        .result-box {
            margin-top: 40px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
            font-size: 16px;
            line-height: 1.8;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✨ 티켓 주문서</h2>
        <form method="post">
            <div class="form-group">
                고객 성명: <input type="text" name="customer_name" required>
            </div>

            <table>
                <tr>
                    <th>No</th><th>구분</th><th>어린이<br>(가격)</th><th>어른<br>(가격)</th><th>비고</th>
                </tr>
                <?php
                $i = 1;
                foreach ($prices as $type => $groupPrices) {
                    echo "<tr>
                        <td>{$i}</td>
                        <td>{$type}</td>
                        <td><select name='quantity[$type][어린이]'>";
                    for ($j = 0; $j <= 5; $j++) {
                        $display = $j == 0 ? "0" : "$j (₩" . number_format($groupPrices["어린이"] * $j) . ")";
                        echo "<option value='$j'>$display</option>";
                    }
                    echo "</select></td>
                        <td><select name='quantity[$type][어른]'>";
                    for ($j = 0; $j <= 5; $j++) {
                        $display = $j == 0 ? "0" : "$j (₩" . number_format($groupPrices["어른"] * $j) . ")";
                        echo "<option value='$j'>$display</option>";
                    }
                    echo "</select></td>
                        <td>입장";
                    if ($type == "BIG3") echo "+놀이3종";
                    else if ($type != "입장권") echo "+놀이자유";
                    echo "</td></tr>";
                    $i++;
                }
                ?>
            </table>

            <div class="form-group">
                <input type="submit" value="합계">
            </div>
        </form>

        <?php if ($order_submitted && !empty($total_details)): ?>
        <div class="result-box">
            <?= date("Y년 m월 d일 A h:i") ?><br>
            <span class="bold"><?= htmlspecialchars($name) ?></span> 고객님 감사합니다.<br>
            <?php foreach ($total_details as $line): ?>
                <?= $line ?><br>
            <?php endforeach; ?>
            합계 <span class="bold"><?= number_format($final_total) ?>원</span>입니다.
        </div>
        <script>
            window.onload = function() {
                window.open('ticket_list.php', 'popup', 'width=900,height=600');
            }
        </script>
        <?php endif; ?>
    </div>
</body>
</html>
