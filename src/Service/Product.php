<?php


namespace App\Service;

use App\Repository\Product as ProductRepository;
use App\Entity\Product as ProductEntity;

class Product
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $numOfProducts
     */
    public function generate(int $numOfProducts) : void
    {
        for ($i = 0; $i < $numOfProducts; $i++) {
            $name = 'Product '.$i;
            $price = mt_rand(1000, 100000);
            $productEntity = new ProductEntity($name, $price);
            $this->productRepository->createOne($productEntity);
        }
    }

    /**
     * @param mixed ...$productIds
     * @return bool
     * @throws \App\Exception\ExistanceCheckingFaild
     */
    public function exists(...$productIds) : bool
    {
       foreach($productIds as $productId) {
           if (! $this->productRepository->exists($productId)) {
               return false;
           }
       }

       return true;
    }
}