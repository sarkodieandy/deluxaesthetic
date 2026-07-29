<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $metrics = [
            'appointments' => Schema::hasTable('appointments') ? DB::table('appointments')->count() : 0,
            'enrolments' => Schema::hasTable('enrolments') ? DB::table('enrolments')->count() : 0,
            'orders' => Schema::hasTable('orders') ? DB::table('orders')->count() : 0,
            'payments' => Schema::hasTable('payments') ? (float) DB::table('payments')->where('status', 'completed')->sum('amount') : 0,
            'products_low_stock' => Schema::hasTable('products') ? DB::table('products')->whereRaw('stock_quantity <= low_stock_threshold')->count() : 0,
        ];

        return view('admin.reports.index', compact('metrics'));
    }
}
