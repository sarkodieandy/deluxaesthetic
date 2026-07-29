<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\ClientProfile;
use App\Models\ConsultationRequest;
use App\Models\GalleryItem;
use App\Models\PractitionerProfile;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Models\StudentProfile;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminSidebarDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', env('SEED_ADMIN_EMAIL', 'admin@deluxaesthetic.test'))->first();
        $client = User::query()->where('email', env('SEED_CLIENT_EMAIL', 'client@deluxaesthetic.test'))->first();
        $student = User::query()->where('email', env('SEED_STUDENT_EMAIL', 'student@deluxaesthetic.test'))->first();

        if (! $admin || ! $client?->clientProfile || ! $student?->studentProfile) {
            $this->command?->warn('Run RolePermissionSeeder, DemoUserSeeder, BranchSeeder, and ClinicDemoSeeder first.');

            return;
        }

        $branch = Branch::query()->where('is_primary', true)->first();
        $practitioner = PractitionerProfile::query()->where('is_active', true)->first();
        $treatment = Treatment::query()->where('is_active', true)->first();

        if (! $branch || ! $practitioner || ! $treatment) {
            $this->command?->warn('Missing branch, practitioner, or treatment demo data.');

            return;
        }

        $this->seedAuditLogs($admin);
        $this->seedConsultations($client, $treatment, $admin);
        $this->seedAppointments($client->clientProfile, $treatment, $practitioner, $branch, $client);
        $products = $this->seedStoreCatalog($admin);
        $this->seedOrderFlow($client, $products, $branch, $admin);
        $this->seedGallery();
        $this->seedAcademyExtras($student->studentProfile, $admin);
        $this->seedModuleSettings();

        Cache::forget('admin.dashboard.metrics');

        $this->command?->newLine();
        $this->command?->info('Admin sidebar demo data seeded.');
        $this->command?->line('  Log in as admin: '.env('SEED_ADMIN_EMAIL', 'admin@deluxaesthetic.test'));
        $this->command?->line('  Browse each sidebar section — live modules now have sample rows.');
        $this->command?->newLine();
        $this->command?->line('  Live data: Dashboard, Activity, Appointments, Consultations, Clients,');
        $this->command?->line('  Practitioners, Treatments, Students, Trainers, Courses, Enrolments,');
        $this->command?->line('  Certificates, Products, Inventory, Orders, Payments, Reports, Gallery,');
        $this->command?->line('  Users, Roles, Settings.');
        $this->command?->line('  Placeholder screens (UI shell): Branches, Schedules, Attendance, Assessments,');
        $this->command?->line('  Deliveries, Reviews, Refunds, Marketing, Content editors, Notifications, AI.');
        $this->command?->newLine();
    }

    private function seedAuditLogs(User $admin): void
    {
        if (AuditLog::query()->where('action', 'demo.sidebar_seed')->exists()) {
            return;
        }

        $samples = [
            ['action' => 'product.created', 'description' => 'Demo product added to store catalogue'],
            ['action' => 'order.updated', 'description' => 'Demo order marked as processing'],
            ['action' => 'appointment.confirmed', 'description' => 'Demo appointment confirmed for client'],
            ['action' => 'certificate.issued', 'description' => 'Demo certificate issued to student'],
            ['action' => 'consultation.responded', 'description' => 'Consultation enquiry assigned to staff'],
            ['action' => 'inventory.adjusted', 'description' => 'Stock adjusted for retail serum'],
            ['action' => 'enrolment.completed', 'description' => 'Student enrolment marked completed'],
            ['action' => 'demo.sidebar_seed', 'description' => 'Admin sidebar demo dataset loaded'],
        ];

        foreach ($samples as $sample) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => $sample['action'],
                'new_values' => ['message' => $sample['description']],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'AdminSidebarDemoSeeder',
                'created_at' => now()->subMinutes(random_int(5, 600)),
            ]);
        }
    }

    private function seedConsultations(User $client, Treatment $treatment, User $admin): void
    {
        ConsultationRequest::updateOrCreate(
            ['email' => 'enquiry@example.com', 'description' => 'Academy enrolment enquiry — Advanced Facial Aesthetics intake.'],
            [
                'user_id' => null,
                'treatment_id' => null,
                'name' => 'Abena Osei',
                'phone' => '+233201234567',
                'preferred_date' => now()->addWeek()->toDateString(),
                'preferred_channel' => 'whatsapp',
                'consent_accepted' => true,
                'assigned_to' => $admin->id,
                'internal_notes' => 'Follow up on WhatsApp for physical enrolment.',
                'status' => 'in_review',
            ]
        );

        ConsultationRequest::updateOrCreate(
            ['email' => $client->email, 'description' => 'I would like advice on a clarity facial before my event.'],
            [
                'user_id' => $client->id,
                'treatment_id' => $treatment->id,
                'name' => $client->name,
                'phone' => config('clinic.phone'),
                'preferred_date' => now()->addDays(3)->toDateString(),
                'preferred_channel' => 'phone',
                'consent_accepted' => true,
                'status' => 'submitted',
            ]
        );
    }

    private function seedAppointments(
        ClientProfile $clientProfile,
        Treatment $treatment,
        PractitionerProfile $practitioner,
        Branch $branch,
        User $client,
    ): void {
        Appointment::updateOrCreate(
            ['reference' => 'APT-DEMO-001'],
            [
                'client_profile_id' => $clientProfile->id,
                'treatment_id' => $treatment->id,
                'practitioner_profile_id' => $practitioner->id,
                'branch_id' => $branch->id,
                'booked_by_user_id' => $client->id,
                'starts_at' => now()->timezone(config('clinic.timezone'))->setTime(11, 0),
                'ends_at' => now()->timezone(config('clinic.timezone'))->setTime(12, 0),
                'status' => AppointmentStatus::Confirmed->value,
                'price' => $treatment->price,
                'deposit_amount' => $treatment->deposit_amount ?? 0,
                'amount_paid' => $treatment->deposit_amount ?? 0,
                'client_notes' => 'First visit — sensitive skin.',
            ]
        );

        Appointment::updateOrCreate(
            ['reference' => 'APT-DEMO-002'],
            [
                'client_profile_id' => $clientProfile->id,
                'treatment_id' => $treatment->id,
                'practitioner_profile_id' => $practitioner->id,
                'branch_id' => $branch->id,
                'booked_by_user_id' => $client->id,
                'starts_at' => now()->addDay()->setTime(14, 30),
                'ends_at' => now()->addDay()->setTime(15, 45),
                'status' => AppointmentStatus::Pending->value,
                'price' => $treatment->price,
                'deposit_amount' => 0,
                'amount_paid' => 0,
            ]
        );

        Appointment::updateOrCreate(
            ['reference' => 'APT-DEMO-003'],
            [
                'client_profile_id' => $clientProfile->id,
                'treatment_id' => $treatment->id,
                'practitioner_profile_id' => $practitioner->id,
                'branch_id' => $branch->id,
                'booked_by_user_id' => $client->id,
                'starts_at' => now()->subDays(7)->setTime(10, 0),
                'ends_at' => now()->subDays(7)->setTime(11, 0),
                'status' => AppointmentStatus::Completed->value,
                'price' => $treatment->price,
                'deposit_amount' => 0,
                'amount_paid' => $treatment->price,
            ]
        );
    }

    /**
     * @return array<int, Product>
     */
    private function seedStoreCatalog(User $admin): array
    {
        $category = ProductCategory::updateOrCreate(
            ['slug' => 'skincare-retail'],
            ['name' => 'Skincare Retail', 'description' => 'Professional retail skincare', 'is_active' => true]
        );

        $items = [
            [
                'slug' => 'hydrating-barrier-serum',
                'name' => 'Hydrating Barrier Serum',
                'sku' => 'DLX-SERUM-001',
                'price' => 280,
                'stock' => 24,
                'featured' => true,
                'image' => 'assets/web/images/store/product-serum.jpg',
            ],
            [
                'slug' => 'brightening-vitamin-c',
                'name' => 'Brightening Vitamin C',
                'sku' => 'DLX-SERUM-002',
                'price' => 320,
                'stock' => 8,
                'featured' => true,
                'image' => 'assets/web/images/store/product-vitc.jpg',
            ],
            [
                'slug' => 'gentle-cleanser',
                'name' => 'Gentle Clinical Cleanser',
                'sku' => 'DLX-CLEAN-001',
                'price' => 195,
                'stock' => 3,
                'featured' => false,
                'image' => 'assets/web/images/store/product-cleanser.jpg',
            ],
        ];

        $products = [];

        foreach ($items as $item) {
            $product = Product::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'product_category_id' => $category->id,
                    'name' => $item['name'],
                    'sku' => $item['sku'],
                    'description' => 'Professional aesthetic retail product for home care after clinic treatments.',
                    'usage_instructions' => 'Apply as directed by your practitioner.',
                    'ingredients' => 'Aqua, glycerin, niacinamide, hyaluronic acid.',
                    'price' => $item['price'],
                    'stock_quantity' => $item['stock'],
                    'low_stock_threshold' => 5,
                    'delivery_eligible' => true,
                    'pickup_eligible' => true,
                    'is_featured' => $item['featured'],
                    'is_active' => true,
                ]
            );

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                [
                    'path' => $item['image'],
                    'alt_text' => $item['name'],
                    'sort_order' => 0,
                    'is_primary' => true,
                ]
            );

            if (! DB::table('inventory_movements')->where('product_id', $product->id)->where('reason', 'Demo stock load')->exists()) {
                DB::table('inventory_movements')->insert([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'quantity_change' => $item['stock'],
                    'reason' => 'Demo stock load',
                    'reference_type' => Product::class,
                    'reference_id' => $product->id,
                    'created_by' => $admin->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $products[] = $product;
        }

        return $products;
    }

    /**
     * @param  array<int, Product>  $products
     */
    private function seedOrderFlow(User $client, array $products, Branch $branch, User $admin): void
    {
        if (DB::table('orders')->where('number', 'ORD-DEMO-001')->exists()) {
            return;
        }

        $orderId = DB::table('orders')->insertGetId([
            'number' => 'ORD-DEMO-001',
            'user_id' => $client->id,
            'status' => 'processing',
            'fulfillment_type' => 'delivery',
            'branch_id' => $branch->id,
            'subtotal' => 600,
            'discount_total' => 0,
            'delivery_fee' => 25,
            'tax_total' => 0,
            'grand_total' => 625,
            'currency' => 'GHS',
            'notes' => 'Demo order for admin preview.',
            'created_at' => now()->subDay(),
            'updated_at' => now(),
        ]);

        $lineProduct = $products[0] ?? $products[array_key_first($products)];

        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'product_id' => $lineProduct->id,
            'product_variant_id' => null,
            'name' => $lineProduct->name,
            'sku' => $lineProduct->sku,
            'quantity' => 2,
            'unit_price' => 280,
            'line_total' => 560,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('deliveries')->insert([
            'order_id' => $orderId,
            'carrier' => 'Local courier',
            'tracking_number' => 'DLX-TRK-10001',
            'status' => 'shipped',
            'shipped_at' => now()->subHours(6),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $paymentId = DB::table('payments')->insertGetId([
            'reference' => 'PAY-DEMO-001',
            'user_id' => $client->id,
            'payable_type' => 'App\Models\Order',
            'payable_id' => $orderId,
            'amount' => 625,
            'currency' => 'GHS',
            'gateway' => 'paystack',
            'status' => 'completed',
            'provider_reference' => 'demo_psk_001',
            'paid_at' => now()->subDay(),
            'created_at' => now()->subDay(),
            'updated_at' => now(),
        ]);

        DB::table('refunds')->insert([
            'payment_id' => $paymentId,
            'amount' => 50,
            'status' => 'requested',
            'reason' => 'Demo partial refund request',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('reviews')->insert([
            'user_id' => $client->id,
            'reviewable_type' => Product::class,
            'reviewable_id' => $lineProduct->id,
            'rating' => 5,
            'body' => 'Lovely texture and my skin felt calm after the clinic facial.',
            'is_approved' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedGallery(): void
    {
        GalleryItem::updateOrCreate(
            ['slug' => 'clinic-treatment-room'],
            [
                'title' => 'Treatment room',
                'type' => 'gallery',
                'description' => 'Calm clinical environment at De Lux.',
                'image_path' => 'assets/web/images/gallery/clinic-room.jpg',
                'alt_text' => 'Treatment room',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        GalleryItem::updateOrCreate(
            ['slug' => 'academy-training-session'],
            [
                'title' => 'Academy training',
                'type' => 'gallery',
                'description' => 'Hands-on professional training.',
                'image_path' => 'assets/web/images/gallery/academy-class.jpg',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        GalleryItem::updateOrCreate(
            ['slug' => 'before-after-clarity'],
            [
                'title' => 'Clarity protocol result',
                'type' => 'before_after',
                'description' => 'Example before and after (demo).',
                'before_image_path' => 'assets/web/images/gallery/before-clarity.jpg',
                'after_image_path' => 'assets/web/images/gallery/after-clarity.jpg',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 3,
            ]
        );
    }

    private function seedAcademyExtras(StudentProfile $studentProfile, User $admin): void
    {
        $courseId = DB::table('courses')->where('slug', 'skin-analysis-basics')->value('id');

        if (! $courseId) {
            $categoryId = DB::table('course_categories')->where('slug', 'professional-aesthetics')->value('id')
                ?? DB::table('course_categories')->insertGetId([
                    'name' => 'Professional Aesthetics',
                    'slug' => 'professional-aesthetics',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            $courseId = DB::table('courses')->insertGetId([
                'course_category_id' => $categoryId,
                'name' => 'Skin Analysis Basics',
                'slug' => 'skin-analysis-basics',
                'description' => 'Introductory skin analysis for new students.',
                'delivery_mode' => 'physical',
                'duration_hours' => 8,
                'venue' => 'De Lux Training Academy',
                'max_students' => 10,
                'waiting_list_capacity' => 2,
                'fee' => 1200,
                'is_featured' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $scheduleId = DB::table('course_schedules')->where('course_id', $courseId)->value('id');

        if (! $scheduleId) {
            $scheduleId = DB::table('course_schedules')->insertGetId([
                'course_id' => $courseId,
                'starts_on' => now()->addWeek()->toDateString(),
                'ends_on' => now()->addWeeks(2)->toDateString(),
                'capacity' => 10,
                'enrolled_count' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $enrolmentId = DB::table('enrolments')->where('reference', 'ENR-DEMO-INPROGRESS')->value('id');

        if (! $enrolmentId) {
            $enrolmentId = DB::table('enrolments')->insertGetId([
                'reference' => 'ENR-DEMO-INPROGRESS',
                'student_profile_id' => $studentProfile->id,
                'course_id' => $courseId,
                'course_schedule_id' => $scheduleId,
                'status' => 'in_progress',
                'fee' => 1200,
                'amount_paid' => 600,
                'outstanding_balance' => 600,
                'currency' => 'GHS',
                'policies_accepted' => true,
                'confirmed_at' => now()->subWeek(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $assessmentId = DB::table('assessments')->where('course_id', $courseId)->where('title', 'Practical skin analysis')->value('id');

        if (! $assessmentId) {
            $assessmentId = DB::table('assessments')->insertGetId([
                'course_id' => $courseId,
                'title' => 'Practical skin analysis',
                'max_score' => 100,
                'passing_score' => 70,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! DB::table('attendance_records')->where('enrolment_id', $enrolmentId)->exists()) {
            DB::table('attendance_records')->insert([
                'enrolment_id' => $enrolmentId,
                'course_schedule_id' => $scheduleId,
                'session_date' => now()->subDays(2)->toDateString(),
                'status' => 'present',
                'notes' => 'Demo attendance',
                'recorded_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! DB::table('assessment_results')->where('enrolment_id', $enrolmentId)->where('assessment_id', $assessmentId)->exists()) {
            DB::table('assessment_results')->insert([
                'assessment_id' => $assessmentId,
                'enrolment_id' => $enrolmentId,
                'score' => 82,
                'comments' => 'Strong consultation notes.',
                'status' => 'passed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedModuleSettings(): void
    {
        $extras = [
            ['group' => 'homepage', 'key' => 'hero_headline_en', 'value' => 'Clinical aesthetics · Spa wellness · Professional academy', 'is_public' => true],
            ['group' => 'homepage', 'key' => 'hero_subhead_en', 'value' => 'Premium care in East Legon, Accra.', 'is_public' => true],
            ['group' => 'content', 'key' => 'pages_about_excerpt', 'value' => 'De Lux Aesthetic Clinic and Training Academy.', 'is_public' => true],
            ['group' => 'content', 'key' => 'blog_latest_post_title', 'value' => 'Aftercare tips for humid climates', 'is_public' => true],
            ['group' => 'content', 'key' => 'faq_booking_answer', 'value' => 'Book online or WhatsApp the clinic for a consultation.', 'is_public' => true],
            ['group' => 'marketing', 'key' => 'testimonial_featured_quote', 'value' => 'Professional, calm, and my skin has never looked better.', 'is_public' => true],
            ['group' => 'marketing', 'key' => 'loyalty_points_per_visit', 'value' => '50', 'is_public' => false],
            ['group' => 'marketing', 'key' => 'referral_reward_amount', 'value' => '100', 'is_public' => false],
            ['group' => 'notifications', 'key' => 'email_appointment_confirmed_subject', 'value' => 'Your De Lux appointment is confirmed', 'is_public' => false],
            ['group' => 'ai', 'key' => 'knowledge_clinic_hours', 'value' => config('clinic.hours'), 'is_public' => false],
            ['group' => 'ai', 'key' => 'knowledge_academy_enrolment', 'value' => 'Academy enrolment is in person or via WhatsApp enquiry.', 'is_public' => false],
            ['group' => 'system', 'key' => 'demo_sidebar_seeded_at', 'value' => now()->toIso8601String(), 'is_public' => false],
        ];

        foreach ($extras as $row) {
            Setting::updateOrCreate(
                ['key' => $row['key']],
                [
                    'group' => $row['group'],
                    'value' => $row['value'],
                    'type' => 'string',
                    'is_public' => $row['is_public'],
                ]
            );
        }
    }
}
