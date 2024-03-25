Implementation Logic:

    Adding a New Order:
        The add method handles the creation of a new order based on the provided request data.
        It validates the incoming request parameters to ensure all required fields are present and have the correct format.
        The controller then creates or retrieves addresses (billing and delivery), creates or retrieves a customer, and finally creates a new order along with its associated items.
        Upon successful creation, it returns the ID of the newly created order.

    Listing Orders:
        The list method retrieves a list of orders based on specified criteria such as order ID, status, start date, and end date.
        It constructs a query based on the provided parameters and fetches orders from the database accordingly.
        The response includes details of each order such as ID, customer name, status, creation date, and total amount.

    Modifying Order Status:
        The modify method allows for changing the status of an existing order.
        It validates the incoming request parameters to ensure the order ID is provided and the new status is valid.
        If the order is found, its status is updated to the new status provided in the request.

Startup Parameters:

    Adding a New Order:
        Endpoint: POST /api/orders/add
        Request Body:
            Name: Customer name
            Email: Customer email
            Receipt: Type of receipt (personal or home delivery)
            Billing and Delivery Address Details
            Products: Array of products containing name, quantity, and actual price

    Listing Orders:
        Endpoint: POST /api/orders/list
        Request Body: Optional parameters
            Order ID: ID of the order to retrieve
            Status: Status of the orders to retrieve (optional, can be "new" or "completed")
            Start Date: Start date of the orders to retrieve
            End Date: End date of the orders to retrieve

    Modifying Order Status:
        Endpoint: POST /api/orders/modify
        Request Body:
            Order ID: ID of the order to modify
            New Status: New status for the order (can be "new" or "completed")
