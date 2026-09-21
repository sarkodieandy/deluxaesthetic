# Checkout workflow

Steps: contact → fulfilment (delivery/pickup) → review → payment → confirmation.

- Delivery fee from `config('ecommerce.delivery_fee')` (server). Pickup fee = 0 unless configured.
- Contact prefilled for Clients; guests enter name/email/phone when guest checkout enabled.
- Review page shows server-calculated totals only.
- Pay button creates a pending order + payment attempt, then redirects to expressPay (or the local-only mock page).
