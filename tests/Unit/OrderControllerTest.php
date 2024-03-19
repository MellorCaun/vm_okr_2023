<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use function json_decode;

class OrderControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function testAddOrder()
    {
        $requestData = [
            'name' => 'Dr. Mr. Lakatos Marlonbrando Winettou',
            'email' => 'winettou@lakatosestarsa.hu',
            'receipt' => 'home_delivery',
            'billing_name' => 'Lakatos Marlonbrando Winettou',
            'billing_postal_code' => '9999',
            'billing_city' => 'Kukutyin',
            'billing_public_space' => 'Kultúra útja',
            'billing_house_number' => '12',
            'delivery_name' => 'Lakatos Marlonbrando Winettou',
            'delivery_postal_code' => '6666',
            'delivery_city' => 'Piripócs',
            'delivery_public_space' => 'Alkoholista tér',
            'delivery_house_number' => '32/a',
            'products' => [
                [
                    'name' => 'Latex búslakodó',
                    'quantity' => 2,
                    'actual_price' => 25599,
                ],
                [
                    'name' => 'Sertés taraj hegyező',
                    'quantity' => 1,
                    'actual_price' => 6599.99,
                ],
            ],
        ];

        $request = new Request($requestData);
        $orderController = new OrderController();

        $response = $orderController->add($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getContent();
        $responseData = json_decode($responseData, true);
        $orderId = $responseData['orderId'];
        $order = Order::find($orderId);
        $this->assertNotNull($order);
        $this->assertEquals('new', $order->status);
    }

    /**
     * @return void
     */
    public function testListOrders()
    {
        $request = new Request([
            'orderId' => null,
            'status' => null,
            'endDate' => now(),
        ]);
        $controller = new OrderController();
        $response = $controller->list($request);
        $this->assertEquals(200, $response->status());
        $responseData = $response->getContent();
        $responseData = json_decode($responseData, true);
        $this->assertArrayHasKey('orders', $responseData);
        $this->assertIsArray($responseData['orders']);
        foreach ($responseData['orders'] as $order) {
            $this->assertArrayHasKey('id', $order);
            $this->assertArrayHasKey('name', $order);
            $this->assertArrayHasKey('status', $order);
            $this->assertArrayHasKey('created_at', $order);
            $this->assertArrayHasKey('total_amount', $order);
        }
    }


    /**
     * @return void
     */
    public function testModifyOrderStatus()
    {
        $requestData = [
            'orderId' => 1,
            'newStatus' => 'completed',
        ];
        $request = new Request($requestData);
        $orderController = new OrderController();

        $response = $orderController->modify($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getContent();
        $responseData = json_decode($responseData, true);
        $this->assertEquals(1, $responseData[0]);
        $this->assertEquals('completed', $responseData[1]);
        $order = Order::find(1);
        $this->assertEquals('completed', $order->status);
    }
}
