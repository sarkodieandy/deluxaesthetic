<?php

namespace Database\Seeders;

use App\Models\ClientProfile;
use App\Models\PractitionerProfile;
use App\Models\StudentProfile;
use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('SEED_DEFAULT_PASSWORD', Str::password(16));

        $admin = User::updateOrCreate(
            ['email' => env('SEED_ADMIN_EMAIL', 'admin@deluxaesthetic.test')],
            [
                'name' => 'System Administrator',
                'password' => Hash::make(env('SEED_ADMIN_PASSWORD', $password)),
                'email_verified_at' => now(),
                'locale' => 'en',
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['Super Administrator']);

        $ceo = User::updateOrCreate(
            ['email' => env('SEED_CEO_EMAIL', 'ceo@deluxaesthetic.test')],
            [
                'name' => config('clinic.ceo.name', 'Dr Evelyn Ejaife'),
                'password' => Hash::make(env('SEED_CEO_PASSWORD', $password)),
                'email_verified_at' => now(),
                'locale' => 'en',
                'is_active' => true,
                'phone' => config('clinic.phone'),
            ]
        );
        $ceo->syncRoles(['Clinic Administrator', 'Practitioner']);

        PractitionerProfile::updateOrCreate(
            ['user_id' => $ceo->id],
            [
                'slug' => 'dr-evelyn-ejaife',
                'title' => config('clinic.ceo.title'),
                'professional_title' => 'Founder & Aesthetic Director',
                'biography' => 'Dr Evelyn Ejaife is a CPD licensed aesthetic trainer and specialist, leading De Lux Aesthetic Clinic with a premium standard of clinical aesthetics, spa wellness, and professional beauty education in Ghana.',
                'qualifications' => ['Aesthetic Clinic Leadership', 'Professional Beauty Education'],
                'certifications' => ['Clinic Governance', 'Professional Standards'],
                'specialities' => ['Clinic Direction', 'Client Experience', 'Academy Leadership'],
                'years_experience' => 10,
                'photo_path' => config('clinic.ceo.portrait_a'),
                'is_ceo' => true,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $client = User::updateOrCreate(
            ['email' => env('SEED_CLIENT_EMAIL', 'client@deluxaesthetic.test')],
            [
                'name' => 'Demo Client',
                'password' => Hash::make(env('SEED_CLIENT_PASSWORD', $password)),
                'email_verified_at' => now(),
                'locale' => 'en',
                'is_active' => true,
            ]
        );
        $client->syncRoles(['Client']);
        ClientProfile::updateOrCreate(
            ['user_id' => $client->id],
            ['referral_code' => strtoupper(Str::random(8))]
        );

        $student = User::updateOrCreate(
            ['email' => env('SEED_STUDENT_EMAIL', 'student@deluxaesthetic.test')],
            [
                'name' => 'Demo Student',
                'password' => Hash::make(env('SEED_STUDENT_PASSWORD', $password)),
                'email_verified_at' => now(),
                'locale' => 'en',
                'is_active' => true,
            ]
        );
        $student->syncRoles(['Student']);
        StudentProfile::updateOrCreate(
            ['user_id' => $student->id],
            ['student_number' => 'STU-'.now()->format('Y').'-0001']
        );

        $trainer = User::updateOrCreate(
            ['email' => env('SEED_TRAINER_EMAIL', 'trainer@deluxaesthetic.test')],
            [
                'name' => 'Demo Trainer',
                'password' => Hash::make(env('SEED_TRAINER_PASSWORD', $password)),
                'email_verified_at' => now(),
                'locale' => 'en',
                'is_active' => true,
            ]
        );
        $trainer->syncRoles(['Trainer']);
        TrainerProfile::updateOrCreate(
            ['user_id' => $trainer->id],
            [
                'slug' => 'demo-trainer',
                'professional_title' => 'Lead Trainer',
                'biography' => 'Lead trainer for professional aesthetic programmes.',
                'is_active' => true,
            ]
        );

        $team = [
            [
                'email' => 'ama.mensah@deluxaesthetic.test',
                'name' => 'Ama Mensah',
                'slug' => 'ama-mensah',
                'title' => 'Senior Aesthetician',
                'professional_title' => 'Senior Aesthetician',
                'biography' => 'Specialises in clinical facial protocols and barrier repair for humid climates.',
                'photo' => 'assets/web/images/team/practitioner-ama.jpg',
                'sort' => 2,
                'social' => ['instagram' => 'https://instagram.com', 'linkedin' => 'https://linkedin.com'],
            ],
            [
                'email' => 'efua.boateng@deluxaesthetic.test',
                'name' => 'Efua Boateng',
                'slug' => 'efua-boateng',
                'title' => 'Spa Therapist',
                'professional_title' => 'Spa Therapist',
                'biography' => 'Delivers restorative body treatments with a calm, measured approach.',
                'photo' => 'assets/web/images/team/practitioner-efua.jpg',
                'sort' => 3,
                'social' => ['instagram' => 'https://instagram.com', 'facebook' => 'https://facebook.com'],
            ],
            [
                'email' => 'kwesi.adu@deluxaesthetic.test',
                'name' => 'Kwesi Adu',
                'slug' => 'kwesi-adu',
                'title' => 'Clinical Skin Specialist',
                'professional_title' => 'Clinical Skin Specialist',
                'biography' => 'Focuses on clarity protocols and consultation-led treatment planning.',
                'photo' => 'assets/web/images/team/practitioner-kwesi.jpg',
                'sort' => 4,
                'social' => ['linkedin' => 'https://linkedin.com', 'twitter' => 'https://x.com'],
            ],
        ];

        foreach ($team as $member) {
            $user = User::updateOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => Hash::make(env('SEED_DEFAULT_PASSWORD', $password)),
                    'email_verified_at' => now(),
                    'locale' => 'en',
                    'is_active' => true,
                ]
            );
            $user->syncRoles(['Practitioner']);

            PractitionerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'slug' => $member['slug'],
                    'title' => $member['title'],
                    'professional_title' => $member['professional_title'],
                    'biography' => $member['biography'],
                    'qualifications' => [],
                    'certifications' => [],
                    'specialities' => [],
                    'years_experience' => 5,
                    'photo_path' => $member['photo'],
                    'is_ceo' => false,
                    'is_featured' => true,
                    'is_active' => true,
                    'sort_order' => $member['sort'],
                    'social_links' => $member['social'],
                ]
            );
        }

        if (! env('SEED_ADMIN_PASSWORD')) {
            $this->command?->warn('Demo passwords were generated. Set SEED_*_PASSWORD in .env for stable credentials.');
            $this->command?->warn('Shared generated password for unset seed users: '.$password);
        }
    }
}
