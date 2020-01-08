<?php
namespace App\Controller;

use App\Service\Payment as PaymentService;
use App\Service\Order as OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Order
{
    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * Order constructor.
     * @param OrderService $orderService
     * @param PaymentService $paymentService
     */
    public function __construct(OrderService $orderService, PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createOrder(Request $request) : Response
    {
        try {
            $productIds = $request->get('productIds');
            $productIds = explode(",", $productIds);

            $orderEntity = $this->orderService->createFresh($productIds);
            return new Response(
                json_encode(['orderId' => $orderEntity->getId()]),
                200,
                ['Content-Type' => 'application/json']
            );
        }
        catch(\Exception $e) {
            return new Response(
                json_encode(['error' => $e->getMessage()]),
                400,
                ['Content-Type' => 'application/json']
            );
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function payOrder(Request $request) : Response
    {
        try {
            $orderId = $request->get('orderId');
            $amount = $request->get('amount');

            $this->paymentService->payOrderById($orderId, $amount);
            return new Response(
                '',
                200,
                ['Content-Type' => 'application/json']
            );
        }
        catch(\Exception $e) {
            return new Response(
                json_encode(['error' => $e->getMessage()]),
                400,
                ['Content-Type' => 'application/json']
            );
        }
    }
}