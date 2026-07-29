# E-commerce architecture

## Scope
Complete product purchasing for De Luxe Aesthetic Clinic: catalogue → cart → checkout → Paystack/mock payment → inventory → client tracking → admin fulfilment.

## Existing foundation
- Tables already exist (`carts`, `orders`, `payments`, `coupons`, `deliveries`, `inventory_movements`, `product_variants`, …).
- Admin product CRUD + public store listing work.
- `PaystackPaymentService` / `MockPaymentService` are bound via `PaymentGatewayInterface`.
- Admin order edit updates status via query builder (being upgraded to Eloquent).

## Chosen payment strategy
**Pending order before payment (Strategy A, simplified):**

1. Validate cart + stock + pricing on the server.
2. Create `orders` with status `awaiting_payment` and a `payments` row (`initiated`).
3. Initialise gateway with unique payment `reference`.
4. On verified success: mark payment successful, set order `paid` / `processing`, decrement inventory, clear cart lines, notify.
5. On failure/cancel: leave order `awaiting_payment` / mark payment failed; do not reduce stock; allow retry.

Inventory is **not** reserved until payment succeeds. Stock is re-checked with row locks at confirmation time to prevent oversell.

## Guest checkout
Enabled by `config('ecommerce.allow_guest_checkout')`. Orders/payments support nullable `user_id` plus `guest_email` / `guest_phone`. Guests receive a signed tracking link; accounts are not auto-created.

## Module map
| Area | Entry |
|------|--------|
| Catalogue | `Web\StoreController` |
| Cart | `CartService` + `Web\CartController` |
| Checkout | `CheckoutService` + `Web\CheckoutController` |
| Pricing | `OrderPricingService` |
| Payment | `PaymentGatewayInterface` + callback/webhook |
| Inventory | `InventoryService` |
| Client orders | `Client\OrderController` |
| Admin orders | `Admin\OrderController` |

## Money
Currency `GHS` (display GH₵). Decimal columns; totals computed server-side only.
