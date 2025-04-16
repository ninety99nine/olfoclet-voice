<?php

namespace App\Traits;

use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles as SpatieHasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    use SpatieHasRoles {
        roles as spatieRoles;
    }

    /**
     * Override the default Spatie `roles()` relationship to support team-based role assignments
     * using UUIDs and organization scoping (via `organization_id` as `team_foreign_key`).
     *
     * This method ensures:
     * - Role relationships are pulled from the correct table (`model_has_roles`)
     * - The pivot keys use UUIDs and match configured column names
     * - System-level roles (where `organization_id` is NULL) are still supported
     * - Roles scoped to an organization (via `organization_id`) are returned when applicable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $roleModel = config('permission.models.role');
        $modelHasRolesTable = config('permission.table_names.model_has_roles');
        $modelMorphKey = config('permission.column_names.model_morph_key');
        $pivotRoleKey = app(PermissionRegistrar::class)->pivotRole;
        $teamsEnabled = app(PermissionRegistrar::class)->teams;
        $teamsKey = app(PermissionRegistrar::class)->teamsKey;
        $rolesTable = config('permission.table_names.roles');
        $teamField = "$rolesTable.$teamsKey";

        $relation = $this->morphToMany(
            $roleModel,
            'model',
            $modelHasRolesTable,
            $modelMorphKey,
            $pivotRoleKey
        );

        if (! $teamsEnabled) {
            return $relation;
        }

        $relation->withPivot($teamsKey);

        return $relation
            ->where(function ($query) use ($teamsKey, $modelHasRolesTable) {
                $query->whereNull("$modelHasRolesTable.$teamsKey")
                      ->orWhere("$modelHasRolesTable.$teamsKey", getPermissionsTeamId());
            })
            ->where(function ($query) use ($teamField) {
                $query->whereNull($teamField)
                      ->orWhere($teamField, getPermissionsTeamId());
            });
    }
}
