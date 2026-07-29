# Inventory reservations

MVP: no soft reservations. Stock locked (`lockForUpdate`) only at payment confirmation.

Future: `inventory_reservations` table with TTL cleanup job if overselling becomes an issue under high concurrency.
