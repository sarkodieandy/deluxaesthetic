# Order statuses

Statuses: `draft`, `awaiting_payment`, `paid`, `processing`, `ready_for_pickup`, `shipped`, `out_for_delivery`, `delivered`, `cancelled`, `refund_requested`, `refunded`.

Valid transitions enforced by `OrderStatusTransitionService`.

Payment statuses remain separate (`PaymentStatus` enum).
