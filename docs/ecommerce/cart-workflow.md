# Cart workflow

1. Resolve cart by authenticated `user_id` or `session_id`.
2. On login, merge guest session cart into user cart (revalidate stock/price).
3. Add/update item: load product from DB, reject inactive/out-of-stock, set `unit_price` from `effectivePrice()`.
4. Never trust browser price/discount/stock.
5. Coupon codes stored on cart; validated on apply and again at checkout.
6. Cart drawer + full cart page share the same `CartService` summary DTO.
