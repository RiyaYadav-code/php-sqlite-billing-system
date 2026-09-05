<?php
require_once __DIR__.'/db.php';$id=(int)($_POST['id']??0);
try{
 if($id){$pdo->beginTransaction();$s=$pdo->prepare("SELECT product_id,quantity FROM bill_items WHERE bill_id=?");$s->execute([$id]);$items=$s->fetchAll();$up=$pdo->prepare("UPDATE products SET stock_quantity=stock_quantity+? WHERE id=?");foreach($items as $i)$up->execute([(int)$i['quantity'],(int)$i['product_id']]);$pdo->prepare("DELETE FROM bills WHERE id=?")->execute([$id]);$pdo->commit();}
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();die('Could not delete bill: '.e($e->getMessage()));}
header('Location:index.php#bills');exit;
