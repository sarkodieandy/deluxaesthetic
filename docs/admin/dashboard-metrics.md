# Dashboard metrics

Source: `App\Services\Admin\Dashboard\DashboardMetricsService`  
Cache key: `admin.dashboard.metrics` (2 minutes)

| Metric key | Definition |
|------------|------------|
| `appointments_today` | Appointments with `starts_at` today, excluding draft/cancelled |
| `appointments_pending` | Status `pending` or `awaiting_payment` |
| `appointments_confirmed_today` | Confirmed appointments today |
| `appointments_completed_today` | Completed appointments today |
| `monthly_clinic_revenue` | Sum of `amount_paid` on qualifying appointments this month; uses `payments` table when present |
| `active_clients` | Count of `client_profiles` |
| `active_students` | Count of `student_profiles` |
| `new_orders` | Orders in awaiting_payment / paid / processing (if `orders` table exists) |
| `low_stock_products` | Products where `stock_quantity <= low_stock_threshold` |
| `failed_payments` | Failed payments in last 30 days |
| `staff_users` | Active users with staff roles from `config('admin.roles')` |

Recent activity: latest rows from `audit_logs` with user name.

All values are computed from the database — never hard-coded in views.
