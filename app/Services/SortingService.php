<?php

namespace App\Services;

use App\Enums\SortResourceType;

class SortingService
{
    /**
     * Get sorting options for a specific resource type.
     *
     * @param SortResourceType $sortResourceType
     * @return array
     */
    public function getSortingOptionsByResourceType(SortResourceType $sortResourceType): array
    {
        switch ($sortResourceType) {
            case SortResourceType::ORGANIZATIONS:
                return self::getOrganizationSorting();
            case SortResourceType::USERS:
                return self::getUserSorting();
            case SortResourceType::CONTACTS:
                return self::getContactSorting();
            case SortResourceType::ROLES:
                return self::getRoleSorting();
            case SortResourceType::DEPARTMENTS:
                return self::getDepartmentSorting();
            case SortResourceType::QUEUES:
                return self::getQueueSorting();
            case SortResourceType::CALLS:
                return self::getCallSorting();
            default:
                return [];
        }
    }

    /**
     * Sorting options for users.
     *
     * @return array
     */
    private function getUserSorting(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'options' => $this->getSortEarliestAndOldestOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'options' => $this->getSortAlphabeticalOptions()
            ],
            [
                'priority' => true,
                'label' => 'Email',
                'target' => 'email',
                'options' => $this->getSortAlphabeticalOptions()
            ]
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Sorting options for organizations.
     *
     * @return array
     */
    private function getOrganizationSorting(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'options' => $this->getSortEarliestAndOldestOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'options' => $this->getSortAlphabeticalOptions()
            ],
            [
                'priority' => true,
                'label' => 'Country',
                'target' => 'country',
                'options' => $this->getSortAlphabeticalOptions()
            ]
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Sorting options for contacts.
     *
     * @return array
     */
    private function getContactSorting(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'options' => $this->getSortEarliestAndOldestOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'options' => $this->getSortAlphabeticalOptions()
            ],
            [
                'priority' => true,
                'label' => 'Email',
                'target' => 'email',
                'options' => $this->getSortAlphabeticalOptions()
            ],
            [
                'priority' => true,
                'label' => 'Phone Number',
                'target' => 'phone_number',
                'options' => $this->getSortAlphabeticalOptions()
            ]
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Sorting options for roles.
     *
     * @return array
     */
    private function getRoleSorting(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'options' => $this->getSortEarliestAndOldestOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'options' => $this->getSortAlphabeticalOptions()
            ],
            [
                'priority' => true,
                'label' => 'Guard Name',
                'target' => 'guard_name',
                'options' => $this->getSortAlphabeticalOptions()
            ]
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Sorting options for departments.
     *
     * @return array
     */
    private function getDepartmentSorting(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'options' => $this->getSortEarliestAndOldestOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'options' => $this->getSortAlphabeticalOptions()
            ]
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Sorting options for queues.
     *
     * @return array
     */
    private function getQueueSorting(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'options' => $this->getSortEarliestAndOldestOptions()
            ],
            [
                'priority' => true,
                'label' => 'Name',
                'target' => 'name',
                'options' => $this->getSortAlphabeticalOptions()
            ],
            [
                'priority' => true,
                'label' => 'SLA Pickup Seconds',
                'target' => 'sla_pickup_seconds',
                'options' => $this->getSortHighestAndLowestOptions()
            ],
            [
                'priority' => true,
                'label' => 'SLA Callback Seconds',
                'target' => 'sla_callback_seconds',
                'options' => $this->getSortHighestAndLowestOptions()
            ]
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Sorting options for calls.
     *
     * @return array
     */
    private function getCallSorting(): array
    {
        return collect([
            [
                'priority' => true,
                'label' => 'Created Date',
                'target' => 'created_at',
                'options' => $this->getSortEarliestAndOldestOptions()
            ],
            [
                'priority' => true,
                'label' => 'Direction',
                'target' => 'direction',
                'options' => $this->getSortAlphabeticalOptions()
            ],
            [
                'priority' => true,
                'label' => 'Status',
                'target' => 'status',
                'options' => $this->getSortAlphabeticalOptions()
            ]
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Get sort highest and lowest options.
     *
     * @return array
     */
    private function getSortHighestAndLowestOptions(): array
    {
        return [
            ['label' => 'Highest first', 'value' => 'desc'],
            ['label' => 'Lowest first', 'value' => 'asc'],
        ];
    }

    /**
     * Get sort earliest and latest options.
     *
     * @return array
     */
    private function getSortEarliestAndOldestOptions(): array
    {
        return [
            ['label' => 'Earliest first', 'value' => 'desc'],
            ['label' => 'Oldest first', 'value' => 'asc']
        ];
    }

    /**
     * Get sort alphabetical options.
     *
     * @return array
     */
    private function getSortAlphabeticalOptions(): array
    {
        return [
            ['label' => 'A to Z', 'value' => 'asc'],
            ['label' => 'Z to A', 'value' => 'desc'],
        ];
    }
}
