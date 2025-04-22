<?php

namespace App\Services;

use App\Enums\FilterResourceType;

class FilterService
{
    /**
     * Generate filters for a specific resource.
     *
     * @param FilterResourceType $filterResourceType
     * @return array
     */
    public function getFiltersByResourceType(FilterResourceType $filterResourceType): array
    {
        switch ($filterResourceType) {
            case FilterResourceType::ORGANIZATIONS:
                return self::getOrganizationFilters();
            case FilterResourceType::USERS:
                return self::getUserFilters();
            case FilterResourceType::CONTACTS:
                return self::getContactFilters();
            case FilterResourceType::ROLES:
                return self::getRoleFilters();
            case FilterResourceType::DEPARTMENTS:
                return self::getDepartmentFilters();
            case FilterResourceType::QUEUES:
                return self::getQueueFilters();
            case FilterResourceType::CALLS:
                return self::getCallFilters();
            default:
                return [];
        }
    }

    /**
     * Get filters for users.
     *
     * @return array
     */
    private function getUserFilters(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'type' => 'date',
                'options' => self::getOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Email',
                'target' => 'email',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Type',
                'target' => 'type',
                'type' => 'select',
                'options' => [
                    ['label' => 'Super Admin', 'value' => 'super_admin'],
                    ['label' => 'Regular', 'value' => 'regular']
                ]
            ],
            [
                'priority' => true,
                'label' => 'Organization',
                'target' => 'organization_id',
                'type' => 'uuid',
                'options' => self::getUuidOperatorOptions()
            ],
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Get filters for organizations.
     *
     * @return array
     */
    private function getOrganizationFilters(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'type' => 'date',
                'options' => self::getOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Country',
                'target' => 'country',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Get filters for contacts.
     *
     * @return array
     */
    private function getContactFilters(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'type' => 'date',
                'options' => self::getOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'First Name',
                'target' => 'first_name',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Last Name',
                'target' => 'last_name',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Email',
                'target' => 'email',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Phone Number',
                'target' => 'phone_number',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Organization',
                'target' => 'organization_id',
                'type' => 'uuid',
                'options' => self::getUuidOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Metadata Source',
                'target' => 'metadata->source',
                'type' => 'select',
                'options' => [
                    ['label' => 'Website', 'value' => 'website'],
                    ['label' => 'Call', 'value' => 'call'],
                    ['label' => 'Email', 'value' => 'email']
                ]
            ],
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Get filters for roles.
     *
     * @return array
     */
    private function getRoleFilters(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'type' => 'date',
                'options' => self::getOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Guard Name',
                'target' => 'guard_name',
                'type' => 'select',
                'options' => [
                    ['label' => 'Web', 'value' => 'web'],
                    ['label' => 'Sanctum', 'value' => 'sanctum']
                ]
            ],
            [
                'priority' => true,
                'label' => 'Organization',
                'target' => 'organization_id',
                'type' => 'uuid',
                'options' => self::getUuidOperatorOptions()
            ],
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Get filters for departments.
     *
     * @return array
     */
    private function getDepartmentFilters(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'type' => 'date',
                'options' => self::getOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Organization',
                'target' => 'organization_id',
                'type' => 'uuid',
                'options' => self::getUuidOperatorOptions()
            ],
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Get filters for queues.
     *
     * @return array
     */
    private function getQueueFilters(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'type' => 'date',
                'options' => self::getOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'type' => 'text',
                'options' => self::getTextOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'SLA Pickup Seconds',
                'target' => 'sla_pickup_seconds',
                'type' => 'number',
                'options' => self::getOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'SLA Callback Seconds',
                'target' => 'sla_callback_seconds',
                'type' => 'number',
                'options' => self::getOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Callback Enabled',
                'target' => 'callback_enabled',
                'type' => 'select',
                'options' => [
                    ['label' => 'Yes', 'value' => '1'],
                    ['label' => 'No', 'value' => '0']
                ]
            ],
            [
                'priority' => true,
                'label' => 'Organization',
                'target' => 'organization_id',
                'type' => 'uuid',
                'options' => self::getUuidOperatorOptions()
            ],
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Get filters for calls.
     *
     * @return array
     */
    private function getCallFilters(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'type' => 'date',
                'options' => self::getOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Direction',
                'target' => 'direction',
                'type' => 'select',
                'options' => [
                    ['label' => 'Inbound', 'value' => 'inbound'],
                    ['label' => 'Outbound', 'value' => 'outbound']
                ]
            ],
            [
                'priority' => true,
                'label' => 'Status',
                'target' => 'status',
                'type' => 'select',
                'options' => [
                    ['label' => 'Completed', 'value' => 'completed'],
                    ['label' => 'Missed', 'value' => 'missed'],
                    ['label' => 'Voicemail', 'value' => 'voicemail'],
                    ['label' => 'In Progress', 'value' => 'in-progress']
                ]
            ],
            [
                'priority' => true,
                'label' => 'Organization',
                'target' => 'organization_id',
                'type' => 'uuid',
                'options' => self::getUuidOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Agent',
                'target' => 'agent_id',
                'type' => 'uuid',
                'options' => self::getUuidOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Contact',
                'target' => 'contact_id',
                'type' => 'uuid',
                'options' => self::getUuidOperatorOptions()
            ],
            [
                'priority' => true,
                'label' => 'Queue',
                'target' => 'queue_id',
                'type' => 'uuid',
                'options' => self::getUuidOperatorOptions()
            ],
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Get operator options for date and number fields.
     *
     * @return array
     */
    private function getOperatorOptions(): array
    {
        return [
            ['label' => 'Greater or Equal to', 'value' => 'gte'],
            ['label' => 'Less or Equal to', 'value' => 'lte'],
            ['label' => 'Greater than', 'value' => 'gt'],
            ['label' => 'Less than', 'value' => 'lt'],
            ['label' => 'Equal to', 'value' => 'eq'],
            ['label' => 'Not equal to', 'value' => 'neq'],
            ['label' => 'Between (Including)', 'value' => 'bt'],
            ['label' => 'Between (Excluding)', 'value' => 'bt_ex'],
        ];
    }

    /**
     * Get operator options for text fields.
     *
     * @return array
     */
    private function getTextOperatorOptions(): array
    {
        return [
            ['label' => 'Equal to', 'value' => 'eq'],
            ['label' => 'Not equal to', 'value' => 'neq'],
            ['label' => 'Contains', 'value' => 'like'],
        ];
    }

    /**
     * Get operator options for UUID fields.
     *
     * @return array
     */
    private function getUuidOperatorOptions(): array
    {
        return [
            ['label' => 'Equal to', 'value' => 'eq'],
            ['label' => 'Not equal to', 'value' => 'neq'],
            ['label' => 'In', 'value' => 'in'],
            ['label' => 'Not In', 'value' => 'not_in'],
        ];
    }
}
