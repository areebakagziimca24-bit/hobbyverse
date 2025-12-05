<?php
session_start();
include "db_connect.php";

$order_id = intval($_GET['order_id'] ?? 0);
if ($order_id <= 0) die("Invalid Order ID");

// Fetch Order
$order_q = mysqli_query($conn,"
    SELECT orders.*, users.username, users.email
    FROM orders
    LEFT JOIN users ON orders.user_id = users.user_id
    WHERE orders.order_id = $order_id
");
$order = mysqli_fetch_assoc($order_q);
if (!$order) die("Order Not Found");

// Fetch order items
$items = mysqli_query($conn,"
    SELECT order_items.*, products.product_name
    FROM order_items
    LEFT JOIN products ON order_items.product_id = products.product_id
    WHERE order_items.order_id = $order_id
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Invoice #<?= $order_id ?></title>

<!-- Google fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39+Extended+Text&display=swap" rel="stylesheet">

<style>
body {
    background: linear-gradient(145deg,#FFE9F2,#FFF4F9);
    font-family: 'Poppins', sans-serif;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}

/*** Printer wrapper ***/
.printer-wrap {
    margin-top: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/*** Printer ***/
.printer {
    width: 450px;
    height: 70px;
    background: #cccccc;
    border-radius: 18px 18px 0 0;
    box-shadow: 0 8px 20px rgba(0,0,0,.18);
    overflow: hidden;
    position: relative;
    z-index: 2;
}

/*** Receipt sheet ***/
.receipt {
    width: 380px;
    background: #fff;
    border-radius: 0 0 22px 22px;
    padding: 32px 36px 46px;
    box-shadow: 0 18px 40px rgba(0,0,0,.14);
    transform: translateY(-110%);
    opacity: 0;
    animation: printOut 2.7s ease-out forwards;
    position: relative;
}

/*** Receipt printing animation ***/
@keyframes printOut {
    0%   { transform: translateY(-110%); opacity: 1; }
    100% { transform: translateY(0%); opacity: 1; }
}

/*** Header ***/
.receipt-header {
    font-size: 24px;
    font-weight: 600;
    text-align: center;
    background: linear-gradient(90deg,#ff3385,#ff76b0);
    -webkit-background-clip: text;
    color: transparent;
    margin-bottom: 14px;
}

/*** Dashed divider ***/
.dotted {
    border-bottom: 2px dashed #ffd2e3;
    margin: 14px 0;
}

/*** Table styling ***/
.items table { width: 100%; font-size: 14px; }
.items td { padding: 4px 0; }

/*** Total price ***/
.total {
    font-size: 17px;
    text-align: right;
    font-weight: 600;
    color: #ff2e72;
    margin-top: 4px;
}

/*** Thank you note ***/
.thanks {
    text-align: center;
    margin-top: 18px;
    font-size: 13px;
    color: #606060;
}

/*** Barcode ***/
.bc-font {
    font-family: "Libre Barcode 39 Extended Text";
    font-size: 55px;
    text-align: center;
    display: block;
    margin-top: 10px;
    color: #444;
    opacity: 0;
    animation: fadeIn 0.9s ease forwards;
    animation-delay: 2.4s;
}
@keyframes fadeIn { from {opacity:0;} to {opacity:1;} }

.order-id {
    text-align: center;
    font-size: 12px;
    color: #999;
    margin-top: -6px;
}

/*** Print button ***/
.print-btn {
    width: 100%;
    padding: 13px;
    margin-top: 30px;
    border: none;
    border-radius: 26px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    background: linear-gradient(90deg,#ff4c8b,#ff8ab7);
    color: #fff;
    transition: .3s;
}
.print-btn:hover { opacity: .85; }

/*** Jagged Bottom Edge ***/
.receipt:after {
    content: "";
    position: absolute;
    bottom: -16px;
    left: 0;
    width: 100%;
    height: 20px;
    background: repeating-linear-gradient(
        90deg,
        #ffe3ef 0px, #ffe3ef 12px,
        white 12px, white 24px
    );
    border-radius: 0 0 22px 22px;
    
}
.home-btn {
    display: block;
    text-align: center;
    margin-top: 14px;
    padding: 12px;
    border-radius: 26px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    background: linear-gradient(90deg,#c6c6c6,#e6e6e6);
    color: #444;
    transition: .25s;
}
.home-btn:hover {
    background: linear-gradient(90deg,#b9b9b9,#dcdcdc);
    transform: translateY(-1px);
}

</style>
</head>

<body>

<div class="printer-wrap">

    <!-- Grey Printer Block -->
    <div class="printer"></div>

    <!-- Receipt -->
    <div class="receipt">

        <div class="receipt-header">✨ Hobbyverse Receipt ✨</div>
        <small>Date: <?= date("d M Y", strtotime($order['created_at'])) ?></small><br><br>

        <strong>Customer:</strong><br>
        <?= htmlspecialchars($order['username']) ?><br>
        <?= htmlspecialchars($order['email']) ?><br><br>

        <strong>Order Details</strong>
        <div class="dotted"></div>

        <div class="items">
            <table>
                <tr>
                    <th style="text-align:left;">Item</th>
                    <th style="text-align:right;">Price</th>
                </tr>
                <?php
                $grand = 0;
                mysqli_data_seek($items,0);
                while ($i = mysqli_fetch_assoc($items)):
                    $total = $i['quantity'] * $i['price'];
                    $grand += $total;
                ?>
                <tr>
                    <td><?= htmlspecialchars($i['product_name']) ?> × <?= $i['quantity'] ?></td>
                    <td style="text-align:right;">₹<?= number_format($total,2) ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="dotted"></div>
        <div class="total">Total: ₹<?= number_format($grand,2) ?></div>

        <p class="thanks">Thank you for shopping with us 💗</p>

        <!-- Barcode -->
        <span class="bc-font">*<?= $order_id ?>*</span>

        <button class="print-btn" onclick="window.print()">🧾 Print Receipt</button>

<!-- Go Back Home Button -->
<a href="index.php" class="home-btn">🏠 Go Back to Home</a>

    </div>
</div>

</body>
</html>
