<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LocalDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('roles')->updateOrInsert(
            ['id' => 1],
            [
                'role_name' => 'Super Admin',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@volunteer.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'role_id' => 1,
                'is_active' => 1,
                'email_verified_at' => $now,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $this->seedAdminMenus($now);

        DB::table('settings')->updateOrInsert(
            ['id' => 1],
            [
                'event_commission' => 10,
                'logo' => 'assets_front/images/logo.webp',
                'waiver_text' => 'I understand this is a volunteer cleanup activity and agree to follow organizer safety instructions.',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        if (Schema::hasTable('release_notes')) {
            DB::table('release_notes')->updateOrInsert(
                ['version' => 'demo-1.0'],
                [
                    'title' => 'Demo Site Ready',
                    'type' => 1,
                    'description' => 'Sample data has been added so you can test volunteer login, organizer dashboards, events, and admin tools locally.',
                    'status' => 1,
                    'release_date' => $now->toDateString(),
                    'published_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        DB::table('countries')->updateOrInsert(
            ['country_code' => 'US'],
            [
                'country_name' => 'United States',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $categories = [
            ['slug' => 'beach', 'name' => 'Beach', 'translations' => ['es' => 'Playa', 'ht' => 'Plaj']],
            ['slug' => 'neighborhood', 'name' => 'Neighborhood', 'translations' => ['es' => 'Vecindario', 'ht' => 'Katye']],
            ['slug' => 'park', 'name' => 'Park', 'translations' => ['es' => 'Parque', 'ht' => 'Pak']],
            ['slug' => 'river', 'name' => 'River', 'translations' => ['es' => 'Río', 'ht' => 'Rivyè']],
            ['slug' => 'shoreline', 'name' => 'Shoreline / Mixed Terrain', 'translations' => ['es' => 'Costa / terreno mixto', 'ht' => 'Rivaj / teren melanje']],
        ];

        $categoryIds = [];

        foreach ($categories as $category) {
            DB::table('event_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $categoryIds[$category['slug']] = DB::table('event_categories')
                ->where('slug', $category['slug'])
                ->value('id');

            $this->seedContentTranslation(
                'App\\Models\\EventCategory',
                $categoryIds[$category['slug']],
                'name',
                $category['name'],
                $category['translations'],
                $now
            );
        }

        $organizerId = $this->upsertVolunteer([
            'first_name' => 'Jane',
            'last_name' => 'Organizer',
            'email_address' => 'organizer@volunteer.com',
            'phone' => '1234567890',
            'address_line_1' => '123 Cleanup Blvd',
            'pincode' => '90210',
            'is_organiser' => 1,
            'organisation_name' => 'Eco Cleaners',
            'organisation_landing_slug' => 'eco-cleaners',
        ], $now);

        $this->upsertVolunteer([
            'first_name' => 'Bob',
            'last_name' => 'Helper',
            'email_address' => 'bob@volunteer.com',
            'phone' => '0987654321',
            'address_line_1' => '456 Green Way',
            'pincode' => '90210',
            'is_organiser' => 0,
        ], $now);

        $countryId = DB::table('countries')->where('country_code', 'US')->value('country_id');

        $events = [
            [
                'event_name' => 'Sunrise Beach Cleanup',
                'slug' => 'sunrise-beach-cleanup',
                'event_category_id' => $categoryIds['beach'],
                'description' => 'Join a friendly morning cleanup at the beach. Gloves, bags, and basic supplies are provided.',
                'translations' => [
                    'event_name' => [
                        'es' => 'Limpieza de Playa al Amanecer',
                        'ht' => 'Netwayaj Plaj nan Solèy Leve',
                    ],
                    'description' => [
                        'es' => 'Únete a una limpieza matutina amigable en la playa. Se proporcionan guantes, bolsas y suministros básicos.',
                        'ht' => 'Vin patisipe nan yon netwayaj maten amikal sou plaj la. Gan, sak, ak founiti debaz ap disponib.',
                    ],
                    'venue' => [
                        'es' => 'Parque Sunny Beach',
                        'ht' => 'Pak Sunny Beach',
                    ],
                ],
                'start_datetime' => $now->copy()->addDays(5)->setTime(9, 0),
                'end_datetime' => $now->copy()->addDays(5)->setTime(12, 0),
                'venue' => 'Sunny Beach Park',
                'pincode' => '90210',
                'latitude' => 34.01940000,
                'longitude' => -118.49120000,
            ],
            [
                'event_name' => 'Downtown Park Litter Sweep',
                'slug' => 'downtown-park-litter-sweep',
                'event_category_id' => $categoryIds['park'],
                'description' => 'Help refresh the walking paths, picnic areas, and playground edges at Downtown Park.',
                'translations' => [
                    'event_name' => [
                        'es' => 'Barrido de Basura en el Parque del Centro',
                        'ht' => 'Ranmasaj Fatra nan Pak Anba Lavil',
                    ],
                    'description' => [
                        'es' => 'Ayuda a renovar los senderos, las áreas de picnic y los bordes del área de juegos en Downtown Park.',
                        'ht' => 'Ede rafrechi chemen mache yo, zòn piknik yo, ak bò lakou jwèt la nan Downtown Park.',
                    ],
                    'venue' => [
                        'es' => 'Parque del Centro',
                        'ht' => 'Pak Anba Lavil',
                    ],
                ],
                'start_datetime' => $now->copy()->addDays(9)->setTime(10, 0),
                'end_datetime' => $now->copy()->addDays(9)->setTime(13, 0),
                'venue' => 'Downtown Park',
                'pincode' => '90012',
                'latitude' => 34.05220000,
                'longitude' => -118.24370000,
            ],
            [
                'event_name' => 'Riverbank Restoration Morning',
                'slug' => 'riverbank-restoration-morning',
                'event_category_id' => $categoryIds['river'],
                'description' => 'Remove litter and sort recyclables along the riverbank with a small organizer-led team.',
                'translations' => [
                    'event_name' => [
                        'es' => 'Mañana de Restauración de la Ribera',
                        'ht' => 'Maten Restorasyon Bò Rivyè',
                    ],
                    'description' => [
                        'es' => 'Retira basura y separa reciclables a lo largo de la ribera con un pequeño equipo dirigido por el organizador.',
                        'ht' => 'Retire fatra epi separe materyèl resiklab sou bò rivyè a ak yon ti ekip òganizatè a ap dirije.',
                    ],
                    'venue' => [
                        'es' => 'Sendero Green River',
                        'ht' => 'Santye Green River',
                    ],
                ],
                'start_datetime' => $now->copy()->addDays(14)->setTime(8, 30),
                'end_datetime' => $now->copy()->addDays(14)->setTime(11, 30),
                'venue' => 'Green River Trail',
                'pincode' => '91105',
                'latitude' => 34.14780000,
                'longitude' => -118.14450000,
            ],
        ];

        foreach ($events as $event) {
            $eventTranslations = $event['translations'];
            unset($event['translations']);

            DB::table('events')->updateOrInsert(
                ['slug' => $event['slug']],
                array_merge($event, [
                    'creator_type' => 'App\\Models\\Volunteer',
                    'creator_id' => $organizerId,
                    'event_reoccurance' => 'once',
                    'event_timezone' => 'pacific',
                    'registration_deadline' => $now->copy()->addDays(4)->toDateString(),
                    'country_id' => $countryId,
                    'status' => 'published',
                    'is_private_ticket' => 0,
                    'ticket_sell_start_time' => $now->copy()->subDay()->toDateString(),
                    'ticket_sell_end_time' => $now->copy()->addDays(30)->toDateString(),
                    'show_ticket_description' => 1,
                    'show_remaining_tickets' => 1,
                    'sell_tickets_individually' => 0,
                    'admin_commission' => 0,
                    'meeting_spot_details' => 'Meet near the main entrance check-in table.',
                    'parking_info_details' => 'Free public parking is available nearby.',
                    'is_private' => 0,
                    'is_active' => 1,
                    'is_online' => 0,
                    'is_featured' => 1,
                    'created_by' => 1,
                    'site_publish_date' => $now->copy()->subDay()->toDateString(),
                    'site_depublish_date' => $now->copy()->addDays(60)->toDateString(),
                    'allow_community_service_form' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );

            $eventId = DB::table('events')->where('slug', $event['slug'])->value('id');

            foreach ($eventTranslations as $field => $translations) {
                $this->seedContentTranslation(
                    'App\\Models\\Event',
                    $eventId,
                    $field,
                    $event[$field],
                    $translations,
                    $now
                );
            }

            $this->seedContentTranslation(
                'App\\Models\\Event',
                $eventId,
                'meeting_spot_details',
                'Meet near the main entrance check-in table.',
                [
                    'es' => 'Reúnete cerca de la mesa de registro de la entrada principal.',
                    'ht' => 'Rankontre toupre tab anrejistreman antre prensipal la.',
                ],
                $now
            );

            $this->seedContentTranslation(
                'App\\Models\\Event',
                $eventId,
                'parking_info_details',
                'Free public parking is available nearby.',
                [
                    'es' => 'Hay estacionamiento público gratuito cerca.',
                    'ht' => 'Gen pakin piblik gratis toupre a.',
                ],
                $now
            );

            DB::table('event_tickets')->updateOrInsert(
                [
                    'event_id' => $eventId,
                    'ticket_name' => 'Volunteer Spot',
                ],
                [
                    'ticket_description' => 'Free volunteer registration spot.',
                    'ticket_type' => 'free',
                    'ticket_price' => 0,
                    'available_quantity' => 50,
                    'minimum_quantity_to_sell' => 1,
                    'is_active' => 1,
                    'sell_start_time' => $now->copy()->subDay()->toDateString(),
                    'sell_end_time' => $now->copy()->addDays(45)->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $ticketId = DB::table('event_tickets')
                ->where('event_id', $eventId)
                ->where('ticket_name', 'Volunteer Spot')
                ->value('id');

            $this->seedContentTranslation(
                'App\\Models\\EventTicket',
                $ticketId,
                'ticket_name',
                'Volunteer Spot',
                [
                    'es' => 'Cupo de voluntario',
                    'ht' => 'Plas volontè',
                ],
                $now
            );

            $this->seedContentTranslation(
                'App\\Models\\EventTicket',
                $ticketId,
                'ticket_description',
                'Free volunteer registration spot.',
                [
                    'es' => 'Cupo gratuito de registro para voluntarios.',
                    'ht' => 'Plas enskripsyon gratis pou volontè.',
                ],
                $now
            );
        }
    }

    private function upsertVolunteer(array $data, $now): int
    {
        DB::table('volunteers')->updateOrInsert(
            ['email_address' => $data['email_address']],
            array_merge($data, [
                'username' => Str::slug($data['first_name'].' '.$data['last_name']),
                'password' => Hash::make('12345678'),
                'email_verified_at' => $now,
                'status' => 1,
                'city_id' => 0,
                'is_newsletter' => 1,
                'is_weekly_alert_sent' => 0,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ])
        );

        return DB::table('volunteers')->where('email_address', $data['email_address'])->value('id');
    }

    private function seedAdminMenus($now): void
    {
        if (!Schema::hasTable('menus')) {
            return;
        }

        $menus = [
            [1, 'dashboard', 'dashboard', 0, 'mdi-view-dashboard-outline', 'Dashboard', '1', 1, 0, 1],
            [2, 'user', 'users', 21, 'mdi-account', 'Users', '1,2,3,4,5', 1, 0, 9],
            [3, 'role', 'roles', 21, 'mdi-account-group', 'Roles', '1,2,3,4', 1, 0, 10],
            [4, 'permission', 'permissions', 21, 'mdi-account-cog', 'Permission', '1,3', 1, 0, 11],
            [21, 'system-settings', '', 0, 'mdi-account-lock-open-outline', 'System Users', '1', 1, 1, 6],
            [29, 'settings', 'settings', 0, 'mdi-cog-outline', 'Settings', '1,2,3,4', 1, 0, 7],
            [30, 'volunteers', 'volunteers', 0, 'mdi-account-group-outline', 'Volunteers & Organizers', '1,2,3,4', 1, 0, 2],
            [31, 'global-types', '', 0, 'mdi-animation-outline', 'Global Types', '1', 1, 1, 5],
            [32, 'tags', 'global-types/tags', 31, 'mdi-tag-outline', 'Tags', '1,2,3,4', 1, 0, 1],
            [33, 'tools', 'global-types/tools', 31, 'mdi-tools', 'Event Tools', '1,2,3,4', 1, 0, 2],
            [34, 'categories', 'global-types/categories', 31, 'mdi-shape-outline', 'Event Categories', '1,2,3,4', 1, 0, 3],
            [35, 'events', 'events', 0, 'mdi-broom', 'Events', '1,2,3,4', 1, 0, 3],
            [36, 'orders', 'orders', 0, 'mdi-ticket-confirmation-outline', 'Orders', '1', 1, 0, 4],
            [37, 'release-notes', 'release-notes', 0, 'mdi-note-text-outline', 'Release Notes', '1,2,3,4', 1, 0, 8],
            [38, 'subscribers', 'subscribers', 0, 'mdi-bell-outline', 'Subscribers', '1', 1, 0, 12],
            [39, 'weekly-alerts', 'weekly-alerts', 0, 'mdi-email-alert-outline', 'Weekly Alerts', '1,3', 1, 0, 13],
            [40, 'weekly-alert-analytics', 'weekly-alert-analytics', 0, 'mdi-chart-line', 'Alert Analytics', '1', 1, 0, 14],
            [41, 'campaign-analytics', 'campaign-analytics', 0, 'mdi-chart-box-outline', 'Campaign Analytics', '1', 1, 0, 15],
        ];

        foreach ($menus as [$id, $name, $href, $parent, $icon, $placeholder, $permissions, $active, $section, $order]) {
            DB::table('menus')->updateOrInsert(
                ['id' => $id],
                [
                    'menu_name' => $name,
                    'menu_href' => $href,
                    'menu_parent' => $parent,
                    'menu_icon' => $icon,
                    'menu_placeholder' => $placeholder,
                    'menu_permissions' => $permissions,
                    'is_active' => $active,
                    'is_section' => $section,
                    'order' => $order,
                    'deleted_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    private function seedContentTranslation(string $modelType, $modelId, string $field, string $sourceText, array $translations, $now): void
    {
        if (!Schema::hasTable('content_translations') || empty($modelId) || $sourceText === '') {
            return;
        }

        foreach ($translations as $locale => $translatedText) {
            DB::table('content_translations')->updateOrInsert(
                [
                    'model_type' => $modelType,
                    'model_id' => (string) $modelId,
                    'field' => $field,
                    'locale' => $locale,
                    'source_hash' => hash('sha256', $sourceText),
                ],
                [
                    'source_text' => $sourceText,
                    'translated_text' => $translatedText,
                    'provider' => 'demo',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
