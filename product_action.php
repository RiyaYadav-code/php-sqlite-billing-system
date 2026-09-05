<?php
require_once __DIR__.'/db.php';$a=$_POST['action']??'';
try{
 if($a==='add'){
  $n=trim($_POST['name']??'');$p=(float)($_POST['price']??0);$stock=max(0,(int)($_POST['stock_quantity']??0));$low=max(0,(int)($_POST['low_stock_threshold']??5));
  if($n===''||$p<0)throw new Exception('Enter a valid product name and price.');
  $pdo->prepare("INSERT INTO products(name,price,stock_quantity,low_stock_threshold) VALUES(?,?,?,?)")->execute([$n,$p,$stock,$low]);
 }
 elseif($a==='update'){
  $id=(int)$_POST['id'];$n=trim($_POST['name']??'');$p=(float)($_POST['price']??0);$stock=max(0,(int)($_POST['stock_quantity']??0));$low=max(0,(int)($_POST['low_stock_threshold']??5));
  $pdo->prepare("UPDATE products SET name=?,price=?,stock_quantity=?,low_stock_threshold=? WHERE id=?")->execute([$n,$p,$stock,$low,$id]);
 }
 elseif($a==='delete'){$pdo->prepare("DELETE FROM products WHERE id=?")->execute([(int)$_POST['id']]);}
 header('Location:index.php#products');exit;
}catch(Throwable $e){die('Product operation failed: '.e($e->getMessage()));}

