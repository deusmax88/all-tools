<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Product as ProductService;

class Product
{
    protected $productService;

    /**
     * Product constructor.
     * @param $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    public function generate(Request $request) : Response
    {
        try {
            $numOfProducts = $request->get('numOfProducts');

            $this->productService->generate($numOfProducts);
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