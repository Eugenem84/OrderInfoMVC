<?php

namespace DataAccess;

class OrderRepository
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getProductsByOrderId(int $orderId): array
    {
        $stmt = $this->pdo->prepare("SELECT product_id, quantity FROM orders_products WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getProductById(int $productId): array
    {
        $productStmt = $this->pdo->prepare("SELECT name, main_shelf_id FROM products WHERE product_id = ?");
        $productStmt->execute([$productId]);
        $productData = $productStmt->fetch(\PDO::FETCH_ASSOC);

        $shelfStmt = $this->pdo->prepare("SELECT shelf_name FROM shelves WHERE shelf_id = ?");
        $shelfStmt->execute([$productData['main_shelf_id']]);
        $shelfName = $shelfStmt->fetchColumn();

        return [
            'name' => $productData['name'],
            'shelf_name' => $shelfName,
        ];
    }

    public function getAdditionalShelvesForProduct(int $productId): array
    {
        $stmt = $this->pdo->prepare("SELECT shelf_name FROM shelves WHERE shelf_id IN (SELECT shelf_id FROM additional_shelves_for_products WHERE product_id = ?)");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
