# Payment verification workflow

1. Server creates payment `reference` (unique).
2. Gateway initialise returns authorization URL.
3. Callback/webhook loads payment by reference.
4. Verify with gateway (`verify`).
5. Compare amount/currency/payable order.
6. Idempotent: successful payment processed once.
7. Confirm order + inventory + clear cart + notifications.
8. Redirect to success page by order number (DB-verified), never by query trust alone.
