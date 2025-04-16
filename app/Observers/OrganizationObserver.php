<?php

namespace App\Observers;

use Illuminate\Support\Str;
use App\Models\Organization;

class OrganizationObserver
{
    public function saving(Organization $organization): void
    {
        $this->setAlias($organization);
    }

    public function created(Organization $organization): void
    {
        //
    }

    public function updated(Organization $organization): void
    {
        //
    }

    public function deleting(Organization $organization): void
    {
        //
    }

    public function deleted(Organization $organization): void
    {
        //
    }

    public function restored(Organization $organization): void
    {
        //
    }

    public function forceDeleted(Organization $organization): void
    {
        //
    }

    private function setAlias(Organization $organization): void
    {
        if (empty($organization->alias)) {
            $baseAlias = Str::slug($organization->name);
            $similarAliases = Organization::where('alias', 'like', "{$baseAlias}%")->pluck('alias')->toArray();

            if (!in_array($baseAlias, $similarAliases)) {
                $organization->alias = $baseAlias;
            } else {
                $maxSuffix = $this->getMaxSuffix($baseAlias, $similarAliases);
                $organization->alias = "{$baseAlias}-" . ($maxSuffix + 1);
            }
        } else {
            $organization->alias = Str::slug($organization->alias);
        }
    }

    private function getMaxSuffix(string $baseAlias, array $similarAliases): int
    {
        $maxSuffix = 0;

        foreach ($similarAliases as $alias) {
            if (preg_match('/^' . preg_quote($baseAlias, '/') . '-(\d+)$/', $alias, $matches)) {
                $maxSuffix = max($maxSuffix, (int) $matches[1]);
            }
        }

        return $maxSuffix;
    }
}
