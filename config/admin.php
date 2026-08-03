<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Staff roles allowed to access /admin (Client and Student are excluded)
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'Super Administrator',
        'Clinic Administrator',
        'Receptionist',
        'Practitioner',
        'Therapist',
        'Trainer',
        'Finance Officer',
        'Store Manager',
        'Content Manager',
        'Support Agent',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sidebar navigation (filtered by permission at runtime)
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        [
            'key' => 'overview',
            'label' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'permission' => 'dashboard.view'],
                ['label' => 'Public website', 'route' => 'web.home', 'permission' => 'dashboard.view', 'external' => true],
                ['label' => 'Activity', 'route' => 'admin.activity.index', 'permission' => 'audit.view'],
            ],
        ],
        [
            'key' => 'clinic',
            'label' => 'Clinic',
            'items' => [
                ['label' => 'Appointments', 'route' => 'admin.appointments.index', 'permission' => 'appointments.view'],
                ['label' => 'Consultation requests', 'route' => 'admin.consultations.index', 'permission' => 'consultations.view'],
                ['label' => 'Clients', 'route' => 'admin.clients.index', 'permission' => 'clients.view'],
                ['label' => 'Practitioners', 'route' => 'admin.practitioners.index', 'permission' => 'practitioners.manage'],
                ['label' => 'Treatments', 'route' => 'admin.treatments.index', 'permission' => 'treatments.view'],
                ['label' => 'Branches', 'route' => 'admin.branches.index', 'permission' => 'settings.manage'],
                ['label' => 'Schedules', 'route' => 'admin.schedules.index', 'permission' => 'practitioner_schedules.manage'],
            ],
        ],
        [
            'key' => 'academy',
            'label' => 'Academy',
            'items' => [
                ['label' => 'Students', 'route' => 'admin.students.index', 'permission' => 'students.view'],
                ['label' => 'Trainers', 'route' => 'admin.trainers.index', 'permission' => 'courses.view'],
                ['label' => 'Courses', 'route' => 'admin.courses.index', 'permission' => 'courses.view'],
                ['label' => 'Course enquiries', 'route' => 'admin.course-enquiries.index', 'permission' => 'course_enquiries.view'],
                ['label' => 'Physical enrolment', 'route' => 'admin.physical-enrolment.create', 'permission' => 'enrolments.create'],
                ['label' => 'Enrolments', 'route' => 'admin.enrolments.index', 'permission' => 'enrolments.manage'],
                ['label' => 'Course materials', 'route' => 'admin.course-materials.index', 'permission' => 'materials.manage'],
                ['label' => 'Assignments', 'route' => 'admin.assignments.index', 'permission' => 'assessments.manage'],
                ['label' => 'Attendance', 'route' => 'admin.attendance.index', 'permission' => 'attendance.manage'],
                ['label' => 'Assessments', 'route' => 'admin.assessments.index', 'permission' => 'assessments.manage'],
                ['label' => 'Certificates', 'route' => 'admin.certificates.index', 'permission' => 'certificates.view'],
            ],
        ],
        [
            'key' => 'store',
            'label' => 'Store',
            'items' => [
                ['label' => 'Products', 'route' => 'admin.products.index', 'permission' => 'products.view'],
                ['label' => 'Inventory', 'route' => 'admin.inventory.index', 'permission' => 'inventory.view'],
                ['label' => 'Orders', 'route' => 'admin.orders.index', 'permission' => 'orders.view'],
                ['label' => 'Deliveries', 'route' => 'admin.deliveries.index', 'permission' => 'orders.view'],
                ['label' => 'Reviews', 'route' => 'admin.reviews.index', 'permission' => 'reviews.moderate'],
            ],
        ],
        [
            'key' => 'finance',
            'label' => 'Finance',
            'items' => [
                ['label' => 'Payments', 'route' => 'admin.payments.index', 'permission' => 'payments.view'],
                ['label' => 'Refunds', 'route' => 'admin.refunds.index', 'permission' => 'refunds.view'],
                ['label' => 'Reports', 'route' => 'admin.reports.index', 'permission' => 'reports.view'],
            ],
        ],
        [
            'key' => 'marketing',
            'label' => 'Marketing',
            'items' => [
                ['label' => 'Promotions', 'route' => 'admin.promotions.index', 'permission' => 'content.manage'],
                ['label' => 'Testimonials', 'route' => 'admin.testimonials.index', 'permission' => 'content.manage'],
                ['label' => 'Loyalty', 'route' => 'admin.loyalty.index', 'permission' => 'loyalty.manage'],
                ['label' => 'Referrals', 'route' => 'admin.referrals.index', 'permission' => 'loyalty.manage'],
            ],
        ],
        [
            'key' => 'content',
            'label' => 'Content',
            'items' => [
                ['label' => 'Homepage', 'route' => 'admin.content.homepage', 'permission' => 'content.manage'],
                ['label' => 'Pages', 'route' => 'admin.pages.index', 'permission' => 'content.manage'],
                ['label' => 'Blog', 'route' => 'admin.blog.index', 'permission' => 'blog.manage'],
                ['label' => 'FAQs', 'route' => 'admin.faqs.index', 'permission' => 'content.manage'],
                ['label' => 'Gallery', 'route' => 'admin.gallery.index', 'permission' => 'gallery.manage'],
                ['label' => 'Media library', 'route' => 'admin.media.index', 'permission' => 'media.manage'],
                ['label' => 'Translations', 'route' => 'admin.translations.index', 'permission' => 'translations.manage'],
            ],
        ],
        [
            'key' => 'communication',
            'label' => 'Communication',
            'items' => [
                ['label' => 'Inbox', 'route' => 'admin.notifications.index', 'permission' => 'notifications.view', 'icon' => 'notification'],
                ['label' => 'Student support', 'route' => 'admin.student-support.index', 'permission' => 'notifications.view'],
                ['label' => 'Email templates', 'route' => 'admin.email-templates.index', 'permission' => 'notifications.view'],
                ['label' => 'Email logs', 'route' => 'admin.email-logs.index', 'permission' => 'notifications.view'],
                ['label' => 'AI knowledge', 'route' => 'admin.ai.index', 'permission' => 'ai_knowledge.manage'],
            ],
        ],
        [
            'key' => 'system',
            'label' => 'System',
            'items' => [
                ['label' => 'Users', 'route' => 'admin.users.index', 'permission' => 'users.manage'],
                ['label' => 'Roles', 'route' => 'admin.roles.index', 'permission' => 'roles.manage'],
                ['label' => 'Audit logs', 'route' => 'admin.audit.index', 'permission' => 'audit.view'],
                ['label' => 'Settings', 'route' => 'admin.settings.index', 'permission' => 'settings.manage'],
            ],
        ],
    ],
];
