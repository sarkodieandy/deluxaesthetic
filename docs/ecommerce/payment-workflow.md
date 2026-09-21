# Payment verification workflow

1. Server creates payment `reference` (unique).
2. expressPay returns a token used to open its hosted checkout.
3. The callback or server notification loads the payment and validates the stored order token.
4. Query expressPay server-to-server.
5. Compare the order ID, token, amount, currency, and payable order.
6. Idempotent: successful payment processed once.
7. Confirm order + inventory + clear cart + notifications.
8. Redirect to success page by order number (DB-verified), never by query trust alone.
