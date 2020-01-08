<?php


namespace App\Service;

use App\Entity\Order as OrderEntity;
use App\Repository\Order as OrderRepository;
use App\Exception\OrderAlreadyPayed as AlreadyPayedOrderException;
use App\Exception\OrderCantBePayed;


class Payment
{
    protected $ch;

    protected OrderRepository $orderRepository;

    /**
     * Payment constructor
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $ch = curl_init('https://ya.ru/');

        $this->ch = $ch;

        $this->orderRepository = $orderRepository;
    }

    public function payOrderById(int $orderId, float $amount)
    {
        $orderEntity = $this->orderRepository->getById($orderId);

        $this->payOrder($orderEntity, $amount);
    }

    /**
     * Оплатить заказ
     *
     * @param OrderEntity $orderEntity
     * @param float $amount
     * @throws AlreadyPayedOrderException
     * @throws OrderCantBePayed
     */
    public function payOrder(OrderEntity $orderEntity, float $amount)
    {
        if ($orderEntity->getStatus() != OrderEntity::FRESH) {
            throw new AlreadyPayedOrderException("Order with id {$orderEntity->getId()} is payed");
        }

        $totalOrderPrice = 0;
        foreach($orderEntity->getProductEntities() as $productEntity) {
            $totalOrderPrice += $productEntity->getPrice();
        }

        if ($totalOrderPrice != $amount) {
            throw new OrderCantBePayed("Total order price $totalOrderPrice can be payed by $amount");
        }

        curl_exec($this->ch);
        $responseCode = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);
        if ($responseCode != 200) {
            throw new OrderCantBePayed("Payment can't be processed");
        }

        $orderEntity->setStatus(OrderEntity::PAYED);

        $this->orderRepository->updateStatus($orderEntity);
    }
}