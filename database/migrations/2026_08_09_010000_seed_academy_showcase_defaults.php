<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $items = [
            ['training_country', 'Ghana', 'Home academy', 'Clinic-led training and supervised practical experience from our home in Accra.', 10],
            ['training_country', 'Cameroon', 'Regional training', 'Visiting aesthetics masterclasses for practitioners and beauty professionals.', 20],
            ['training_country', 'Côte d’Ivoire', 'Regional training', 'Hands-on professional education within our West African academy programme.', 30],
            ['training_country', 'Senegal', 'Regional training', 'Practical training shaped around confident and responsible technique.', 40],
            ['training_country', 'Benin Republic', 'Regional training', 'Professional aesthetics education within our West African community.', 50],

            ['training_step', 'Clinical theory', 'Step 01', 'Anatomy, consultation, safety and product knowledge.', 10],
            ['training_step', 'Live demonstration', 'Step 02', 'Watch the procedure, clinical decisions and treatment protocols in context.', 20],
            ['training_step', 'Supervised practice', 'Step 03', 'Build confidence through guided, hands-on treatment practice.', 30],
            ['training_step', 'Certification & support', 'Step 04', 'Complete assessment, receive certification and continue with professional support.', 40],

            ['career_benefit', 'Customer service', 'Professional practice', 'Build a considered client experience around every consultation and treatment.', 10],
            ['career_benefit', 'Referral network', 'Industry connections', 'Join a professional network shaped around referrals and industry recognition.', 20],
            ['career_benefit', 'Lifetime mentorship', 'Continued support', 'Continue receiving guidance and access to trusted product-seller contacts.', 30],
            ['career_benefit', 'Recognised certification', 'Professional achievement', 'Complete the required assessment pathway and receive your academy certificate.', 40],
            ['career_benefit', 'Graduation experience', 'Celebrate your progress', 'Mark your achievement with a graduation ceremony and professional photography.', 50],
        ];

        foreach ($items as [$type, $title, $subtitle, $body, $sortOrder]) {
            $exists = DB::table('academy_showcase_items')
                ->where('type', $type)
                ->where('title', $title)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('academy_showcase_items')->insert([
                'type' => $type,
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
                'sort_order' => $sortOrder,
                'is_featured' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // These rows become administrator-owned content after deployment. A
        // rollback must not silently delete edits, uploaded proof or visibility
        // choices made in production.
    }
};
