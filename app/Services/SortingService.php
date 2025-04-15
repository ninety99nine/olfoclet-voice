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
}
