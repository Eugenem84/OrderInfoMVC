<?php

require_once '../BusinessLogic/ProductService.php';
require_once '../DataAccess/OrderRepository.php';

use BusinessLogic\ProductService;
use DataAccess\OrderRepository;

// Подключение к базе данных
$host = '127.0.0.1';
$dbname = 'borisovatestdb';
$username = 'postgres';
$password = '1234';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Проверка наличия аргументов
if ($argc < 2) {
    die("Необходимо указать номера заказов\n");
}

// Получение номеров заказов из аргументов
$args = $argv[1];
$orderIds = explode(',', $args);

// Создание экземпляра репозитория заказов
$orderRepository = new OrderRepository($pdo);

// Создание экземпляра сервиса продуктов с внедрением зависимости репозитория заказов
$productService = new ProductService($orderRepository);

// Получение информации о продуктах по указанным заказам
$productInfo = $productService->getProductInfoByOrderIds($orderIds);

// Группировка продуктов по стеллажам
$groupedProducts = $productService->groupProductsByShelves($productInfo);

foreach ($groupedProducts as $shelfName => $shelfData) {
    echo "=+=+=+=\n";
    echo "Страница сборки заказов $args \n";
    echo "===Стеллаж $shelfName\n";

    foreach ($shelfData['products'] as $product) {
        echo $product['product_name'] . " (id=" . $product['product_id'] . ")\n";
        echo "заказ " . $product['order_id'] . ", " . $product['quantity'] . " шт\n";

        // Проверяем наличие дополнительных стеллажей для текущего продукта
        if (!empty($product['additional_shelves'])) {
            echo "доп стеллаж: " . implode(',', $product['additional_shelves']) . "\n";
        }

        echo "\n";
    }
}