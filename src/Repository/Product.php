<?php


namespace App\Repository;

use App\Entity\Product as ProductEntity;
use App\Exception\ExistanceCheckingFaild as ExistanceCheckingFailedException;
use PDO;

class Product
{
    /**
     * @var PDO
     */
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param ProductEntity $productEntity
     * @return string
     */
    public function createOne(ProductEntity $productEntity)
    {
        $sth = $this->pdo->prepare("INSERT INTO product(name, price) VALUES(?, ?)");
        $sth->execute([
                $productEntity->getName(),
                $productEntity->getPrice()
        ]);

        return $this->pdo->lastInsertId();
    }

    public function exists(int $productId)
    {
        try {
            $this->pdo->beginTransaction();
            $sth = $this->pdo->prepare("SELECT id FROM product WHERE id = ?");
            $sth->execute([$productId]);
            $this->pdo->commit();
        }
        catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw new ExistanceCheckingFailedException("Product with id $productId existence check failed", 0, $e);
        }

        return $sth->rowCount() > 0;
    }

    public function getById(int $productId)
    {
        try {
            $this->pdo->beginTransaction();
            $sth = $this->pdo->prepare("SELECT name, price FROM product WHERE id = ?");
            $sth->execute([$productId]);
            $row = $sth->fetch();

            $productEntity = new ProductEntity($row['name'], $row['price'], $productId);

            $this->pdo->commit();
        }
        catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw new ExistanceCheckingFailedException("Product with id $productId fetch failed", 0, $e);
        }

        return $productEntity;
    }
}