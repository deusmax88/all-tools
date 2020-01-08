<?php


namespace App\Repository;

use PDO;
use App\Entity\Order as OrderEntity;
use App\Exception\OrderCreationFailed as OrderCreationFailedException;
use App\Service\Product as ProductService;
use App\Exception\OrderNotFound as OrderNotFoundException;

class Order
{
    /**
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * @var ProductService
     */
    protected ProductService $productService;

    /**
     * Order constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo, ProductService $productService)
    {
        $this->pdo = $pdo;
        $this->productService = $productService;
    }

    /**
     * @param OrderEntity $orderEntity
     * @return string
     * @throws OrderCreationFailedException
     */
    public function createOne(OrderEntity $orderEntity)
    {
        try {
            $this->pdo->beginTransaction();

            $sth = $this->pdo->prepare("INSERT INTO order (status) VALUES (?)");
            $sth->execute([
                $orderEntity->getStatus()
            ]);

            $orderId = $this->pdo->lastInsertId();

            $sth = $this->pdo->prepare("INSERT INTO order_product(order_id, product_id) VALUES (?, ?)");
            foreach($orderEntity->getProductEntities() as $productEntity) {
                $sth->execute([$orderId, $productEntity->getId()]);
            }

            $this->pdo->commit();

            $orderEntity->setId($orderId);
        }
        catch(\PDOException $e) {
            $this->pdo->rollBack();
            throw new OrderCreationFailedException("Order creation failed",0, $e);
        }

        return $orderId;
    }

    public function getById(int $orderId)
    {
        try {
            $this->pdo->beginTransaction();
            $sth = $this->pdo->prepare("SELECT status FROM order WHERE id = ?");
            $sth->execute([
                $orderId
            ]);

            $status = $sth->fetchColumn();

            if (false === $status) {
                throw new OrderNotFoundException("Order with id $orderId not found");
            }

            $orderEntity = new OrderEntity($orderId, $status);

            $sth = $this->pdo->prepare("SELECT product_id FROM order_product WHERE order_id = ?");
            $sth->execute([$orderId]);

            while($productId = $sth->fetchColumn()) {
                $orderEntity->addProductEntity($this->productService->retreiveById($productId));
            }

            $this->pdo->commit();
        }
        catch (\PDOException $e) {
            $this->pdo->rollBack();
        }

        return $orderEntity;
    }

    public function updateStatus(OrderEntity $orderEntity)
    {
        try {
            $this->pdo->beginTransaction();
            $sth = $this->pdo->prepare("UPDATE order SET status = ? WHERE id = ?");
            $sth->execute([
                $orderEntity->getStatus(),
                $orderEntity->getId()
            ]);

            $this->pdo->commit();
        }
        catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}