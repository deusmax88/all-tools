<?php


namespace App\Service;

use App\Repository\Order as OrderRepository;
use App\Service\Product as ProductService;
use App\Entity\Order as OrderEntity;

class Order
{
    protected OrderRepository $orderRepository;

    protected ProductService $productService;

    /**
     * Order constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository, ProductService $productService)
    {
        $this->orderRepository = $orderRepository;
        $this->productService = $productService;
    }


    public function createFresh(...$productIds)
    {
        if (!$this->productService->exists($productIds)) {
            throw new NotExistingProductsException("Creating order with not existing products is not allowed");
        }

        $orderEntity = new OrderEntity(-1, \Entity\Order::FRESH, $productIds);

        $this->orderRepository->createOne($orderEntity);

        return $orderEntity;
    }
}