<?php
require_once __DIR__.'/config.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS products(
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 name TEXT NOT NULL UNIQUE,
 price REAL NOT NULL CHECK(price>=0),
 stock_quantity INTEGER NOT NULL DEFAULT 0 CHECK(stock_quantity>=0),
 low_stock_threshold INTEGER NOT NULL DEFAULT 5 CHECK(low_stock_threshold>=0),
 created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS customers(
 id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,phone TEXT NOT NULL UNIQUE,created_at TEXT DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS bills(
 id INTEGER PRIMARY KEY AUTOINCREMENT,bill_number TEXT NOT NULL UNIQUE,customer_id INTEGER NOT NULL,
 subtotal REAL NOT NULL,discount_percent REAL DEFAULT 0,discount_amount REAL DEFAULT 0,
 gst_percent REAL DEFAULT 18,gst_amount REAL DEFAULT 0,total REAL NOT NULL,created_at TEXT DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE RESTRICT);
CREATE TABLE IF NOT EXISTS bill_items(
 id INTEGER PRIMARY KEY AUTOINCREMENT,bill_id INTEGER NOT NULL,product_id INTEGER NOT NULL,
 product_name TEXT NOT NULL,price REAL NOT NULL,quantity INTEGER NOT NULL,amount REAL NOT NULL,
 FOREIGN KEY(bill_id) REFERENCES bills(id) ON DELETE CASCADE,
 FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE RESTRICT);");

// Safe migration for databases created by the earlier version.
$cols=$pdo->query("PRAGMA table_info(products)")->fetchAll();
$names=array_column($cols,'name');
if(!in_array('stock_quantity',$names,true)) $pdo->exec("ALTER TABLE products ADD COLUMN stock_quantity INTEGER NOT NULL DEFAULT 0");
if(!in_array('low_stock_threshold',$names,true)) $pdo->exec("ALTER TABLE products ADD COLUMN low_stock_threshold INTEGER NOT NULL DEFAULT 5");

if((int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn()===0){
 $s=$pdo->prepare("INSERT INTO products(name,price,stock_quantity,low_stock_threshold) VALUES(?,?,?,?)");
 foreach([
  ['Laptop',50000,10,3],['Monitor',12000,15,5],['Mouse',500,30,8],['Keyboard',1000,25,5],
  ['Headphones',1500,20,5],['USB Cable',300,40,10],['Webcam',2500,12,4]
 ] as $p)$s->execute($p);
}

