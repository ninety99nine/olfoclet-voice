<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Call;
use App\Models\Queue;
use App\Models\Number;
use App\Models\Contact;
use App\Models\Permission;
use App\Models\Department;
use App\Models\Tag;
use App\Models\CustomAttribute;
use App\Models\ContactIdentifier;
use App\Models\ContactCustomAttribute;
use Faker\Factory as Faker;
use App\Models\Organization;
use App\Models\CallActivity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimulateSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function run(): void
    {
        // 🔐 Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data to avoid duplication
        $this->truncateTables();

        $this->call(DefaultSuperAdminSeeder::class);

        // Create standard permissions
        $permissions = $this->createPermissions();

        // Create 2 organizations
        Organization::factory(2)->create()->each(function ($organization) use ($permissions) {
            // Create roles (Admin, Agent, Supervisor)
            $roles = $this->createRoles($organization);

            // Assign permissions to roles
            $this->assignPermissionsToRoles($roles, $permissions);

            // Create users (2-5 per organization)
            $users = $this->createUsers($organization, $roles);

            // Create departments (2-5 per organization)
            $this->createDepartments($organization);

            // Create custom attributes for the organization
            $this->createCustomAttributes($organization);

            // Create tags for the organization (5-10 per organization)
            $tags = $this->createTags($organization);

            // Create contacts (5-10 per organization)
            $contacts = $this->createContacts($organization, $tags);

            // Create queues (3-5 per organization)
            $queues = $this->createQueues($organization);

            // Create numbers (3-5 per organization)
            $this->createNumbers($organization, $queues);

            // Create calls (5-10 per organization)
            $this->createCalls($organization, $users, $queues, $contacts);
        });

        // ✅ Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function truncateTables(): void
    {
        CallActivity::truncate();
        Call::truncate();
        Queue::truncate();
        Number::truncate(); // Added to clear numbers table
        ContactCustomAttribute::truncate();
        CustomAttribute::truncate();
        ContactIdentifier::truncate();
        Contact::truncate();
        Tag::truncate();
        Department::truncate();
        User::truncate();
        Role::truncate();
        Permission::truncate();
        Organization::truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('contact_tag')->truncate();
    }

    private function createPermissions(): array
    {
        $permissions = [
            'view calls', 'manage calls', 'view contacts', 'manage contacts',
            'view queues', 'manage queues', 'view departments', 'manage departments',
            'manage users', 'view numbers', 'create numbers', 'edit numbers' // Added number permissions
        ];

        return collect($permissions)->map(function ($name) {
            return Permission::create(['name' => $name, 'guard_name' => 'sanctum']);
        })->all();
    }

    private function createRoles(Organization $organization): array
    {
        $roles = [
            ['name' => 'Admin', 'permissions' => [
                'view calls', 'manage calls', 'view contacts', 'manage contacts',
                'view queues', 'manage queues', 'view departments', 'manage departments',
                'manage users', 'view numbers', 'create numbers', 'edit numbers' // Added number permissions
            ]],
            ['name' => 'Agent', 'permissions' => ['view calls', 'manage calls', 'view contacts']],
            ['name' => 'Supervisor', 'permissions' => [
                'view calls', 'manage calls', 'view contacts', 'manage contacts',
                'view queues', 'view departments', 'view numbers'
            ]]
        ];

        return collect($roles)->map(function ($roleData) use ($organization) {
            $role = Role::create([
                'guard_name' => 'sanctum',
                'name' => $roleData['name'],
                'organization_id' => $organization->id
            ]);
            return ['role' => $role, 'permissions' => $roleData['permissions']];
        })->toArray();
    }

    private function assignPermissionsToRoles(array $roles, array $permissions): void
    {
        foreach ($roles as $roleData) {
            $role = $roleData['role'];
            $rolePermissions = collect($permissions)->filter(function ($permission) use ($roleData) {
                return in_array($permission->name, $roleData['permissions']);
            });
            $role->syncPermissions($rolePermissions);
        }
    }

    private function createUsers(Organization $organization, array $roles): array
    {
        $numUsers = rand(2, 5);
        $users = User::factory($numUsers)->create();

        $roleIds = collect($roles)->pluck('role.id')->toArray();
        $statuses = ['available', 'on call', 'on break', 'unavailable'];

        foreach ($users as $user) {
            $roleId = $roleIds[array_rand($roleIds)];
            $user->assignRole(Role::find($roleId));

            DB::table('organization_user')->insert([
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'status' => $statuses[array_rand($statuses)],
                'last_seen_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $users->toArray();
    }

    private function createDepartments(Organization $organization): void
    {
        Department::factory(rand(2, 5))->create([
            'organization_id' => $organization->id
        ]);
    }

    private function createCustomAttributes(Organization $organization): void
    {
        CustomAttribute::factory()->createMany([
            [
                'organization_id' => $organization->id,
                'name' => 'name',
                'type' => 'string',
            ],
            [
                'organization_id' => $organization->id,
                'name' => 'website',
                'type' => 'url',
            ],
            [
                'organization_id' => $organization->id,
                'name' => 'title',
                'type' => 'string',
            ],
            [
                'organization_id' => $organization->id,
                'name' => 'industry',
                'type' => 'string',
            ],
            [
                'organization_id' => $organization->id,
                'name' => 'address',
                'type' => 'string',
            ],
        ]);
    }

    private function createTags(Organization $organization): array
    {
        $numTags = rand(5, 10); // Ensure 5 to 10 tags per organization

        // Define a list of realistic tag names
        $tagNames = [
            'customer',
            'lead',
            'vip',
            'support',
            'sales',
            'marketing',
            'urgent',
            'follow-up',
            'new',
            'active',
            'inactive',
            'loyal',
            'prospect',
            'partner',
            'client'
        ];

        // Shuffle and select a random subset of tag names
        shuffle($tagNames);
        $selectedTagNames = array_slice($tagNames, 0, $numTags);

        // If we need more tags to reach the desired number, generate some using Faker
        if (count($selectedTagNames) < $numTags) {
            $additionalTags = $this->faker->words($numTags - count($selectedTagNames));
            $selectedTagNames = array_merge($selectedTagNames, $additionalTags);
        }

        // Ensure uniqueness by using array_unique
        $selectedTagNames = array_unique($selectedTagNames);

        // Create tags in the database
        $tags = [];
        foreach ($selectedTagNames as $tagName) {
            $tags[] = Tag::create([
                'name' => $tagName,
                'organization_id' => $organization->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $tags;
    }

    private function createContacts(Organization $organization, array $tags): array
    {
        return Contact::factory(rand(5, 10))
            ->create([
                'organization_id' => $organization->id
            ])
            ->each(function ($contact) use ($organization, $tags) {
                // Create identifiers
                ContactIdentifier::factory()->createMany([
                    [
                        'contact_id' => $contact->id,
                        'type' => 'phone',
                        'value' => $this->faker->unique()->e164PhoneNumber,
                        'is_primary' => true,
                    ],
                    [
                        'contact_id' => $contact->id,
                        'type' => 'email',
                        'value' => $this->faker->unique()->safeEmail,
                        'is_primary' => true,
                    ],
                ]);

                // Create custom attributes
                $customAttributes = CustomAttribute::where('organization_id', $organization->id)
                    ->get()
                    ->keyBy('name');

                ContactCustomAttribute::factory()->createMany([
                    [
                        'contact_id' => $contact->id,
                        'custom_attribute_id' => $customAttributes['name']->id,
                        'name' => 'name',
                        'type' => 'string',
                        'value' => $this->faker->name,
                    ],
                    [
                        'contact_id' => $contact->id,
                        'custom_attribute_id' => $customAttributes['website']->id,
                        'name' => 'website',
                        'type' => 'url',
                        'value' => $this->faker->url,
                    ],
                    [
                        'contact_id' => $contact->id,
                        'custom_attribute_id' => $customAttributes['title']->id,
                        'name' => 'title',
                        'type' => 'string',
                        'value' => $this->faker->jobTitle,
                    ],
                    [
                        'contact_id' => $contact->id,
                        'custom_attribute_id' => $customAttributes['industry']->id,
                        'name' => 'industry',
                        'type' => 'string',
                        'value' => $this->faker->randomElement(['Technology', 'Finance', 'Healthcare', 'Retail', 'Education']),
                    ],
                    [
                        'contact_id' => $contact->id,
                        'custom_attribute_id' => $customAttributes['address']->id,
                        'name' => 'address',
                        'type' => 'string',
                        'value' => $this->faker->address,
                    ],
                ]);

                // Assign tags
                $randomTags = array_slice($tags, 0, rand(1, 3));
                $contact->tags()->attach(array_column($randomTags, 'id'));
            })
            ->toArray();
    }

    private function createQueues(Organization $organization): array
    {
        $queues = Queue::factory(rand(3, 5))->create([
            'organization_id' => $organization->id
        ]);

        // Assign fallback queues
        foreach ($queues as $queue) {
            $fallback = $queues->where('id', '!=', $queue->id)->random();
            $queue->update(['fallback_queue_id' => $fallback->id]);
        }

        return $queues->toArray();
    }

    private function createNumbers(Organization $organization, array $queues): void
    {
        $numNumbers = rand(3, 5); // 3-5 numbers per organization

        // Define possible number names and types
        $numberNames = [
            'Customer Support Line',
            'Sales Hotline',
            'Technical Support',
            'Billing Support',
            'General Inquiries',
            'After-Hours Support'
        ];

        // Shuffle and select names
        shuffle($numberNames);
        $selectedNames = array_slice($numberNames, 0, $numNumbers);

        // Create numbers
        for ($i = 0; $i < $numNumbers; $i++) {
            $callFlow = null;
            $firstStep = null;

            Number::create([
                'id' => $this->faker->uuid,
                'is_active' => $this->faker->boolean(90), // 90% chance of being active
                'name' => $selectedNames[$i],
                'number' => $this->faker->unique()->e164PhoneNumber,
                'provider' => 'AfricasTalking',
                'first_step' => $firstStep,
                'call_flow' => $callFlow,
                'organization_id' => $organization->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createCalls(Organization $organization, array $users, array $queues, array $contacts): void
    {
        $numCalls = rand(5, 10);
        Call::factory($numCalls)->create([
            'organization_id' => $organization->id,
            'agent_id' => fn() => $users[array_rand($users)]['id'],
            'queue_id' => fn() => $queues[array_rand($queues)]['id'],
            'contact_id' => fn() => $contacts[array_rand($contacts)]['id']
        ])->each(function ($call) {
            CallActivity::factory(rand(1, 5))->create([
                'call_id' => $call->id
            ]);
        });
    }
}
