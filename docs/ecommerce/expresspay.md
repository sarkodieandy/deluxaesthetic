# expressPay store checkout

The product store uses expressPay's Merchant API. Checkout follows this server-controlled flow:

1. Create a pending order and payment using the server-calculated GHS total.
2. Submit the payment to expressPay and redirect the shopper using the returned token.
3. Receive the browser callback or server notification.
4. Query expressPay from the server and compare the order ID, token, amount, and currency.
5. Fulfil the order and decrement inventory once, only after an approved query response.

## Production environment

Set these values directly in the production `.env`; never commit the merchant ID or API key.

```dotenv
STORE_WHATSAPP_CHECKOUT=false
STORE_PAYMENT_DRIVER=expresspay
STORE_PAYMENT_MOCK=false
EXPRESSPAY_ENVIRONMENT=production
EXPRESSPAY_MERCHANT_ID=replace_on_server
EXPRESSPAY_API_KEY=replace_on_server
EXPRESSPAY_CALLBACK_URL=https://deluxaestheticclinic.com/checkout/callback
EXPRESSPAY_POST_URL=https://deluxaestheticclinic.com/api/webhooks/expresspay
```

Then refresh Laravel's cached configuration:

```bash
php artisan optimize:clear
php artisan config:cache
```

Both callback URLs must be publicly reachable over HTTPS. The notification route does not trust the incoming status; it re-queries expressPay before changing an order.
