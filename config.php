<?php
declare(strict_types=1);
$dir=__DIR__.'/data';
if(!is_dir($dir)) mkdir($dir,0775,true);
try{
 $pdo=new PDO('sqlite:'.$dir.'/billing.sqlite');
 $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
 $pdo->exec('PRAGMA foreign_keys=ON');
}catch(PDOException $e){die('Database connection failed: '.htmlspecialchars($e->getMessage()));}
function e(string $v):string{return htmlspecialchars($v,ENT_QUOTES,'UTF-8');}
function money(float $n):string{return '₹'.number_format($n,2,'.',',');}
