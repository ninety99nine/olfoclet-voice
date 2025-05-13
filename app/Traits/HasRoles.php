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
     * Fetch roles associated with the user, with optional team-based filtering.
     *
     * If a team ID is set (via getPermissionsTeamId()), applies team-based filtering using
     * organization_id as the team_foreign_key. If no team ID is set, fetches all roles
     * assigned to the user across all organizations.
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

        $relation->withPivot($teamsKey);

        if (! $teamsEnabled) {
            return $relation;
        }

        // Check if a team ID is set
        $teamId = app(PermissionRegistrar::class)->getPermissionsTeamId();

        if ($teamId !== null) {
            // Apply team-based filtering if a team ID is set
            return $relation
                ->where(function ($query) use ($teamsKey, $modelHasRolesTable, $teamId) {
                    $query->whereNull("$modelHasRolesTable.$teamsKey")
                          ->orWhere("$modelHasRolesTable.$teamsKey", $teamId);
                })
                ->where(function ($query) use ($teamField, $teamId) {
                    $query->whereNull($teamField)
                          ->orWhere($teamField, $teamId);
                });
        }

        // If no team ID is set, return all roles without filtering
        return $relation;
    }
}
