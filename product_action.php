<?php
require_once __DIR__.'/db.php';$a=$_POST['action']??'';
try{if($a==='add'){$n=trim($_POST['name']??'');$p=(float)($_POST['price']??0);$pdo->prepare("INSERT INTO products(name,price) VALUES(?,?)")->execute([$n,$p]);}
elseif($a==='update'){$pdo->prepare("UPDATE products SET name=?,price=? WHERE id=?")->execute([trim($_POST['name']), (float)$_POST['price'], (int)$_POST['id']]);}
elseif($a==='delete'){$pdo->prepare("DELETE FROM products WHERE id=?")->execute([(int)$_POST['id']]);}
header('Location:index.php#products');exit;}catch(Throwable $e){die('Product operation failed: '.e($e->getMessage()));}
