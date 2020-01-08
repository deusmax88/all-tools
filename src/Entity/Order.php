<?php


namespace App\Entity;

use App\Entity\Product as ProductEntity;

class Order
{
    const FRESH = 1;
    const PAYED = 3;

    protected int $id;

    protected int $status;

    /**
     * @var ProductEntity[]
     */
    protected $productEntities;

    /**
     * Order constructor.
     * @param int $id
     * @param int $status
     * @param Product[] $productEntities
     */
    public function __construct(int $id = null, int $status = self::FRESH, ...$productEntities)
    {
        $this->id = $id;
        $this->status = $status;
        $this->productEntities = $productEntities;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return ProductEntity[]
     */
    public function getProductEntities()
    {
        return $this->productEntities;
    }

    /**
     * @param ProductEntity[] $productEntities
     */
    public function setProductEntities($productEntities): void
    {
        $this->productEntities = $productEntities;
    }

    public function addProductEntity(ProductEntity $productEntity): void
    {
        $this->productEntities[] = $productEntity;
    }
}