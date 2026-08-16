<?php
require_once __DIR__.'/db.php';
$products=$pdo->query("SELECT * FROM products ORDER BY name")->fetchAll();
$q=trim($_GET['bill_search']??'');
if($q!==''){
 $s=$pdo->prepare("SELECT b.*,c.name customer_name,c.phone FROM bills b JOIN customers c ON c.id=b.customer_id WHERE b.bill_number LIKE :q OR c.name LIKE :q OR c.phone LIKE :q ORDER BY b.id DESC");
 $s->execute([':q'=>"%$q%"]);$bills=$s->fetchAll();
}else $bills=$pdo->query("SELECT b.*,c.name customer_name,c.phone FROM bills b JOIN customers c ON c.id=b.customer_id ORDER BY b.id DESC")->fetchAll();
$customers=$pdo->query("SELECT c.name,c.phone,COUNT(b.id) bill_count,COALESCE(SUM(b.total),0) total_purchase FROM customers c LEFT JOIN bills b ON b.customer_id=c.id GROUP BY c.id ORDER BY c.name")->fetchAll();
$totalBills=(int)$pdo->query("SELECT COUNT(*) FROM bills")->fetchColumn();
$totalSales=(float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM bills")->fetchColumn();
$todaySales=(float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM bills WHERE date(created_at)=date('now','localtime')")->fetchColumn();
$monthSales=(float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM bills WHERE strftime('%Y-%m',created_at)=strftime('%Y-%m','now','localtime')")->fetchColumn();
$dd=$_GET['daily_date']??date('Y-m-d');$mm=$_GET['report_month']??date('Y-m');
$s=$pdo->prepare("SELECT COUNT(*) c,COALESCE(SUM(total),0) total FROM bills WHERE date(created_at)=?");$s->execute([$dd]);$daily=$s->fetch();
$s=$pdo->prepare("SELECT COUNT(*) c,COALESCE(SUM(total),0) total FROM bills WHERE strftime('%Y-%m',created_at)=?");$s->execute([$mm]);$monthly=$s->fetch();
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>PHP SQLite Billing System</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header><h1>Product Billing System</h1><p>PHP + SQLite • Products • Bills • Reports • Customers</p></header><main class="wrap">
<nav><a href="#billing">🧾 New Bill</a><a href="#products">📦 Products</a><a href="#bills">🧾 Bills</a><a href="#reports">📊 Reports</a><a href="#customers">👤 Customers</a></nav>

<section id="billing" class="card"><h2>Create New Bill</h2><form method="post" action="save_bill.php" id="billForm">
<div class="grid"><div><label>Customer Name</label><input name="customer_name" required></div><div><label>Phone Number</label><input name="phone" maxlength="10" pattern="[0-9]{10}" required></div>
<div><label>Product</label><select id="productSelect"><option value="">-- Select Product --</option><?php foreach($products as $p):?><option value="<?=$p['id']?>" data-price="<?=$p['price']?>"><?=e($p['name'])?> - <?=money((float)$p['price'])?></option><?php endforeach;?></select></div>
<div><label>Quantity</label><input id="qty" type="number" min="1" value="1"></div></div>
<div class="actions"><button type="button" class="btn primary" onclick="addItem()">+ Add Product</button><button type="button" class="btn light" onclick="clearCart()">Clear</button></div>
<div id="cart"><div class="empty">No products added.</div></div><input type="hidden" name="items" id="items">
<div class="grid"><div><label>Discount (%)</label><input name="discount" id="discount" type="number" min="0" max="100" value="0" step=".01" oninput="renderCart()"></div><div><label>GST (%)</label><input name="gst" id="gst" type="number" min="0" max="100" value="18" step=".01" oninput="renderCart()"></div></div>
<div id="totals" class="totalbox"></div><div class="actions"><button class="btn primary" onclick="return prepareSubmit()">Save Bill</button></div></form></section>

<section id="products" class="card"><h2>📦 Product Management</h2><form method="post" action="product_action.php" class="grid three"><input type="hidden" name="action" value="add"><div><label>Product Name</label><input name="name" required></div><div><label>Price (₹)</label><input name="price" type="number" min="0" step=".01" required></div><div class="end"><button class="btn primary">Add Product</button></div></form>
<table><tr><th>Product</th><th class="num">Price</th><th class="num">Action</th></tr><?php foreach($products as $p):?><tr><td><?=e($p['name'])?></td><td class="num"><?=money((float)$p['price'])?></td><td class="num"><a class="btn light" href="edit_product.php?id=<?=$p['id']?>">Edit</a> <form class="inline" method="post" action="product_action.php" onsubmit="return confirm('Delete this product?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$p['id']?>"><button class="btn danger">Delete</button></form></td></tr><?php endforeach;?></table></section>

<section id="bills" class="card"><h2>🧾 Saved Bills</h2><form><input name="bill_search" value="<?=e($q)?>" placeholder="Search bill number, customer or phone..."> <button class="btn primary">Search</button></form>
<table><tr><th>Bill No.</th><th>Customer</th><th>Date</th><th class="num">Total</th><th class="num">Action</th></tr><?php if(!$bills):?><tr><td colspan="5" class="empty">No bills found.</td></tr><?php endif;?><?php foreach($bills as $b):?><tr><td><?=e($b['bill_number'])?></td><td><?=e($b['customer_name'])?><br><small><?=e($b['phone'])?></small></td><td><?=$b['created_at']?></td><td class="num"><?=money((float)$b['total'])?></td><td class="num"><a class="btn light" href="view_bill.php?id=<?=$b['id']?>">View</a> <form class="inline" method="post" action="delete_bill.php" onsubmit="return confirm('Delete this bill?')"><input type="hidden" name="id" value="<?=$b['id']?>"><button class="btn danger">Delete</button></form></td></tr><?php endforeach;?></table></section>

<section id="reports" class="card"><h2>📊 Sales Reports</h2><div class="statgrid"><div class="stat">Total Bills<b><?=$totalBills?></b></div><div class="stat">Total Sales<b><?=money($totalSales)?></b></div><div class="stat">Today's Sales<b><?=money($todaySales)?></b></div><div class="stat">This Month<b><?=money($monthSales)?></b></div></div>
<div class="grid report"><form><label>Daily Report</label><input type="date" name="daily_date" value="<?=e($dd)?>"><button class="btn primary">View</button><p>Bills: <b><?=$daily['c']?></b> | Sales: <b><?=money((float)$daily['total'])?></b></p></form><form><label>Monthly Report</label><input type="month" name="report_month" value="<?=e($mm)?>"><button class="btn primary">View</button><p>Bills: <b><?=$monthly['c']?></b> | Sales: <b><?=money((float)$monthly['total'])?></b></p></form></div></section>

<section id="customers" class="card"><h2>👤 Customer History</h2><table><tr><th>Customer</th><th>Phone</th><th class="num">Bills</th><th class="num">Total Purchase</th></tr><?php if(!$customers):?><tr><td colspan="4" class="empty">No customer history yet.</td></tr><?php endif;?><?php foreach($customers as $c):?><tr><td><?=e($c['name'])?></td><td><?=e($c['phone'])?></td><td class="num"><?=$c['bill_count']?></td><td class="num"><?=money((float)$c['total_purchase'])?></td></tr><?php endforeach;?></table></section>
</main><script src="assets/app.js"></script></body></html>