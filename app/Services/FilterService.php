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
        ])->filter(fn($filter) => count($filter['options']))->toArray();
    }

    /**
     * Get operator options.
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
}
