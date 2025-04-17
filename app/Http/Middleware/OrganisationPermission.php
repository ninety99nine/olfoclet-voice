<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\Response;

class OrganisationPermission
{
    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * Create a new middleware instance.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum');

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Check if the user is a super admin
        $isSuperAdmin = $this->authService->isSuperAdmin($user);

        // Get organization_id from route or input
        $organizationId = $request->route('organization') ?? $request->input('organization_id');

        if ($organizationId) {
            // Validate organization_id exists
            $organizationExists = Organization::where('id', $organizationId)->exists();

            if (!$organizationExists) {
                return response()->json(['message' => 'This organization does not exist'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Super admins can access any organization
            if (!$isSuperAdmin) {
                // Check if the user is a member of the organization
                $isOrgMember = $user->organizations()->where('organization_id', $organizationId)->exists();

                if (!$isOrgMember) {
                    return response()->json(['message' => 'You do not have access to this organization'], Response::HTTP_FORBIDDEN);
                }
            }

            // Set the permissions team ID for Spatie's permission checks
            setPermissionsTeamId($organizationId);
        } elseif (!$isSuperAdmin) {
            // Non-super admins must provide an organization_id
            return response()->json(['message' => 'The organization ID is required'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $next($request);
    }
}
