<?php
require_once __DIR__.'/config.php';
$pdo->exec("CREATE TABLE IF NOT EXISTS products(
id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL UNIQUE,price REAL NOT NULL CHECK(price>=0),created_at TEXT DEFAULT CURRENT_TIMESTAMP);
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
if((int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn()===0){
 $s=$pdo->prepare("INSERT INTO products(name,price) VALUES(?,?)");
 foreach([['Laptop',50000],['Monitor',12000],['Mouse',500],['Keyboard',1000],['Headphones',1500],['USB Cable',300],['Webcam',2500]] as $p)$s->execute($p);
}
