<?php
require_once __DIR__.'/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location:index.php');exit;}
$name=trim($_POST['customer_name']??'');$phone=trim($_POST['phone']??'');$dp=max(0,min(100,(float)($_POST['discount']??0)));$gp=max(0,min(100,(float)($_POST['gst']??18)));$items=json_decode($_POST['items']??'[]',true);
if($name===''||!preg_match('/^[0-9]{10}$/',$phone)||!is_array($items)||!count($items))die('Invalid bill data.');
try{
$pdo->beginTransaction();
$s=$pdo->prepare("SELECT id FROM customers WHERE phone=?");$s->execute([$phone]);$cid=$s->fetchColumn();
if($cid)$pdo->prepare("UPDATE customers SET name=? WHERE id=?")->execute([$name,$cid]);else{$s=$pdo->prepare("INSERT INTO customers(name,phone) VALUES(?,?)");$s->execute([$name,$phone]);$cid=$pdo->lastInsertId();}
$sub=0;$clean=[];
foreach($items as $i){$pid=(int)($i['id']??0);$q=(int)($i['qty']??0);if($q<1)continue;$s=$pdo->prepare("SELECT id,name,price FROM products WHERE id=?");$s->execute([$pid]);$p=$s->fetch();if(!$p)continue;$amt=(float)$p['price']*$q;$sub+=$amt;$clean[]=[(int)$p['id'],$p['name'],(float)$p['price'],$q,$amt];}
if(!$clean)throw new Exception('No valid products.');
$disc=$sub*$dp/100;$taxable=$sub-$disc;$tax=$taxable*$gp/100;$total=$taxable+$tax;$bn='BILL-'.date('Ymd-His').'-'.random_int(100,999);
$s=$pdo->prepare("INSERT INTO bills(bill_number,customer_id,subtotal,discount_percent,discount_amount,gst_percent,gst_amount,total) VALUES(?,?,?,?,?,?,?,?)");$s->execute([$bn,$cid,$sub,$dp,$disc,$gp,$tax,$total]);$bid=$pdo->lastInsertId();
$s=$pdo->prepare("INSERT INTO bill_items(bill_id,product_id,product_name,price,quantity,amount) VALUES(?,?,?,?,?,?)");foreach($clean as $i)$s->execute([$bid,...$i]);
$pdo->commit();header("Location:view_bill.php?id=$bid");exit;
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();die('Could not save bill: '.e($e->getMessage()));}
