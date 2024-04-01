<?php

namespace BusinessLogic;

use DataAccess\OrderRepository;

class ProductService
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getProductInfoByOrderIds(array $orderIds): array
    {
        $productInfo = [];

        foreach ($orderIds as $orderId) {
            $products = $this->orderRepository->getProductsByOrderId($orderId);

            foreach ($products as $product) {
                $productId = $product['product_id'];
                $quantity = $product['quantity'];

                $productData = $this->orderRepository->getProductById($productId);
                $shelfName = $productData['shelf_name'];
                $productName = $productData['name'];

                $additionalShelves = $this->orderRepository->getAdditionalShelvesForProduct($productId);

                $productInfo[] = [
                    'product_name' => $productName,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'order_id' => $orderId,
                    'shelf_name' => $shelfName,
                    'additional_shelves' => $additionalShelves,
                ];
            }
        }

        return $productInfo;
    }

    public function groupProductsByShelves(array $productInfo): array
    {
        //echo "productInfo: " . "\n";
        //print_r($productInfo);
        $groupedProducts = [];

        // Продукты по стеллажам
        foreach ($productInfo as $product) {
            $shelfName = $product['shelf_name'];
            $productData = [
                'product_name' => $product['product_name'],
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'order_id' => $product['order_id'],
                'additional_shelves' => $product['additional_shelves'],
            ];

            if (!isset($groupedProducts[$shelfName])) {
                $groupedProducts[$shelfName] = [
                    'products' => [],
                ];
            }

            $groupedProducts[$shelfName]['products'][] = $productData;
        }
        //echo "groupProducts: ";
        //print_r($groupedProducts);
        return $groupedProducts;
    }

}
