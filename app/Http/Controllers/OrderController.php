<?php

namespace App\Http\Controllers;

use App\Models\Addresses;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;


#[
    OA\Info(version: "1.0.0", description: "VM ORK 2023", title: "vm okr 2023"),
    OA\Server(url: 'http://localhost', description: "local server"),
    OA\SecurityScheme( securityScheme: 'bearerAuth', type: "http", name: "Authorization", in: "header", scheme: "bearer"),
]
/**
 * Class OrderController
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * Add a new order.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    #[OA\Post(
        path: "/api/orders/add",
        summary: "Add a new order",
        requestBody: new OA\RequestBody(required: true,
            content: new OA\MediaType(mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(required: [
                    'name',
                    'email',
                    'receipt',
                    'billing_name',
                    'billing_postal_code',
                    'billing_city',
                    'billing_public_space',
                    'billing_house_number',
                    'delivery_name',
                    'delivery_postal_code',
                    'delivery_city',
                    'delivery_public_space',
                    'delivery_house_number',
                    'products'
                ],
                properties: [
                    new OA\Property(property: 'name', description: "name", type: "string"),
                    new OA\Property(property: 'email', description: "email", type: "string"),
                    new OA\Property(property: 'receipt', description: "receipt", type: "string"),
                    new OA\Property(property: 'billing_name', description: "billing name", type: "string"),
                    new OA\Property(property: 'billing_postal_code', description: "billing postal code", type: "string"),
                    new OA\Property(property: 'billing_city', description: "billing city", type: "string"),
                    new OA\Property(property: 'billing_public_space', description: "billing public space", type: "string"),
                    new OA\Property(property: 'billing_house_number', description: "billing house number", type: "string"),
                    new OA\Property(property: 'delivery_name', description: "delivery name", type: "string"),
                    new OA\Property(property: 'delivery_postal_code', description: "delivery postal code", type: "string"),
                    new OA\Property(property: 'delivery_city', description: "delivery city", type: "string"),
                    new OA\Property(property: 'delivery_public_space', description: "delivery public space", type: "string"),
                    new OA\Property(property: 'delivery_house_number', description: "delivery house number", type: "string"),
                    new OA\Property(
                        property: 'products',
                        description: 'products',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'name', description: 'product name', type: 'string'),
                                new OA\Property(property: 'quantity', description: 'product quantity', type: 'integer'),
                                new OA\Property(property: 'actual_price', description: 'actual price of the product', type: 'number'),
                            ]
                        )
                    )
                ]
                ))),
        tags: ["Orders"],
        responses: [
            new OA\Response(response: 200, description: "Successful operation",
                content: new OA\MediaType(mediaType: "application/json",
                example: '
                {
                    "orderId": 123
                }'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request",
                content: new OA\MediaType(mediaType: "application/json",
                    example: '
                {
                    "error": "Bad Request",
                    "message": "Invalid input data"
                }'
                )
            ),
            new OA\Response(response: 500, description: "Server Error",
                content: new OA\MediaType(mediaType: "application/json",
                    example: '
                {
                    "error": "Server Error",
                    "message": "Internal server error occurred"
                }'
                )
            )
        ]
    )]
    /**
     * Example request:
     *
     * ```
     * curl -X POST "http://localhost/api/orders/add" -H "Content-Type: application/x-www-form-urlencoded" \
     * -d "name=Dr. Mr. Lakatos Marlonbrando Winettou" \
     * -d "email=winettou@lakatosestarsa.hu" \
     * -d "receipt=home_delivery" \
     * -d "billing_name=Lakatos Marlonbrando Winettou" \
     * -d "billing_postal_code=9999" \
     * -d "billing_city=Kukutyin" \
     * -d "billing_public_space=Kultúra útja" \
     * -d "billing_house_number=12" \
     * -d "delivery_name=Lakatos Marlonbrando Winettou" \
     * -d "delivery_postal_code=6666" \
     * -d "delivery_city=Piripócs" \
     * -d "delivery_public_space=Alkoholista tér" \
     * -d "delivery_house_number=32/a" \
     * -d "products[0][name]=Latex búslakodó" \
     * -d "products[0][quantity]=2" \
     * -d "products[0][actual_price]=25599" \
     * -d "products[1][name]=Sertés taraj hegyező" \
     * -d "products[1][quantity]=1" \
     * -d "products[1][actual_price]=6599.99"
     * ```
     */
     public function add(Request $request) {
         $data = $request->validate([
             'name' => 'required',
             'email' => 'required|email',
             'receipt' => 'required|in:personal,home_delivery',
             'billing_name' => 'required',
             'billing_postal_code' => 'required',
             'billing_city' => 'required',
             'billing_public_space' => 'required',
             'billing_house_number' => 'required',
             'delivery_name' => 'required',
             'delivery_postal_code' => 'required',
             'delivery_city' => 'required',
             'delivery_public_space' => 'required',
             'delivery_house_number' => 'required',
             'products' => 'required|array',
             'products.*.name' => 'required',
             'products.*.quantity' => 'required|integer|min:1',
             'products.*.actual_price' => 'required|numeric|min:0',
         ]);
         $billing_address = Addresses::firstOrCreate([
             'name' => $data['billing_name'],
             'postal_code' => $data['billing_postal_code'],
             'city' => $data['billing_city'],
             'public_space' => $data['billing_public_space'],
             'house_number' => $data['billing_house_number'],
         ]);

         $delivery_address = Addresses::firstOrCreate([
             'name' => $data['delivery_name'],
             'postal_code' => $data['delivery_postal_code'],
             'city' => $data['delivery_city'],
             'public_space' => $data['delivery_public_space'],
             'house_number' => $data['delivery_house_number'],
         ]);

         $customer = Customer::firstOrCreate([
             'name' => $data['name'],
             'email' => $data['email'],
             'address_id' => $delivery_address->id,
             'billing_address_id' => $billing_address->id,
         ]);

         $order = new Order();
         $order->customer_id = $customer->id;
         $order->order_receipt = $data['receipt'];
         $order->status = 'new';
         $order->save();

         foreach($data['products'] as $product) {
             $product_item = Product::firstOrCreate([
                'name' => $product['name'],
                'price' => $product['actual_price'],
             ]);
             $order_item = new OrderItems();
             $order_item->order_id = $order->id;
             $order_item->product_id = $product_item->id;
             $order_item->quantity = $product['quantity'];
             $order_item->actual_price = $product['actual_price'];
             $order_item->save();
         }

         return response()->json(['orderId' => $order->id]);
     }

    #[OA\Post(
        path: "/api/orders/list",
        summary: "List orders",
        requestBody: new OA\RequestBody(required: false,
            content: new OA\MediaType(mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(required: [],
                    properties: [
                        new OA\Property(property: 'orderId', description: "ID of the order to retrieve", type: "integer"),
                        new OA\Property(property: 'status', description: "Status of the orders to retrieve", type: "string", enum: ["new", "completed"]),
                        new OA\Property(property: 'startDate', description: "Start date of the orders to retrieve", format: "date", type: "string"),
                        new OA\Property(property: 'endDate', description: "End date of the orders to retrieve", format: "date", type: "string"),
                    ]
                ))),
        tags: ["Orders"],
        responses: [
            new OA\Response(response: 200, description: "Successful operation",
                content: new OA\MediaType(mediaType: "application/json",
                    example: '
                    {
                        "orders": [
                            {
                                "id": 123,
                                "status" : "new",
                                "created_at": "2024-03-19T12:07:53.000000Z",
                                "name": "Dr. Mr. Lakatos Marlonbrando Winettou",
                                "total_amount": 57797.99
                            }
                        ]
                    }'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request",
                content: new OA\MediaType(mediaType: "application/json",
                    example: '
                {
                    "error": "Bad Request",
                    "message": "Invalid input data"
                }'
                )
            ),
            new OA\Response(response: 500, description: "Server Error",
                content: new OA\MediaType(mediaType: "application/json",
                    example: '
                {
                    "error": "Server Error",
                    "message": "Internal server error occurred"
                }'
                )
            )
        ]
    )]
    /**
     * Example request:
     *
     * ```
     * curl -X POST "http://localhost/api/orders/list" -H "Content-Type: application/x-www-form-urlencoded" \
     * -d "orderId=123"
     * ```
     */
    public function list(Request $request) {
        $orderId = $request->input('orderId', null);
        $orderStatus = $request->input('status', null);
        $startDate = $request->input('startDate', null);
        $endDate = $request->input('endDate', now());
        $query = Order::query();
        $query->when($orderId, function ($query, $orderId) {
            return $query->where('id', $orderId);
        })
            ->when($orderStatus, function ($query, $orderStatus) {
                return $query->where('status', $orderStatus);
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            });
        $query->select('id', 'customer_id', 'status', 'created_at');
        $query->with(['customer:id,name']);
        $query->with(['orderItems' => function ($query) {
            $query->select('order_id', DB::raw('SUM(actual_price * quantity) as total_amount'))
                ->groupBy('order_id');
        }]);
        $orders = $query->get()->map(function ($order) {
            $order->name = $order->customer->name ?? null;
            $order->total_amount = $order->orderItems->first()->total_amount ?? null;
            unset($order->orderItems);
            unset($order->customer);
            unset($order->customer_id);
            return $order;
        });
        return response()->json(['orders' => $orders]);
    }

    #[OA\Post(
        path: "/api/orders/modify",
        summary: "Modify an existing order status",
        requestBody: new OA\RequestBody(required: true,
            content: new OA\MediaType(mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(required: ['orderId', 'newStatus'],
                    properties: [
                        new OA\Property(property: 'orderId', description: "The ID of the modified order", type: "integer"),
                        new OA\Property(property: 'newStatus', description: "The new status of the order", type: "string", enum: ["new", "completed"]),
                    ]
                ))),
        tags: ["Orders"],
        responses: [
            new OA\Response(response: 200, description: "Successful operation",
                content: new OA\MediaType(mediaType: "application/json",
                    example: '
                    [
                        123,
                        "completed"
                    ]'
                )
            ),
            new OA\Response(response: 404, description: "Not Found",
                content: new OA\MediaType(mediaType: "application/json",
                    example: '
                {
                    "error": "Not Found",
                    "message": "Order not found"
                }'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request",
                content: new OA\MediaType(mediaType: "application/json",
                    example: '
                {
                    "error": "Bad Request",
                    "message": "Invalid input data"
                }'
                )
            ),
            new OA\Response(response: 500, description: "Server Error",
                content: new OA\MediaType(mediaType: "application/json",
                    example: '
                {
                    "error": "Server Error",
                    "message": "Internal server error occurred"
                }'
                )
            )
        ]
    )]
    /**
     * Example request:
     *
     * ```
     * curl -X POST "http://localhost/api/orders/modify" -H "Content-Type: application/x-www-form-urlencoded" \
     * -d "orderId=123" \
     * -d "newStatus=completed"
     * ```
     */
    public function modify(Request $request) {
        $data = $request->validate([
            'orderId' => 'required',
            'newStatus' => 'required|in:new,completed',
        ]);

        $order = Order::findOrFail($data['orderId']);
        $order->status = $data['newStatus'];
        $order->save();

        return response()->json([$order->id, $order->status]);
     }
}
