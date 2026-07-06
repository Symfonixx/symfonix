<?php

namespace Modules\User\app\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\User\app\Models\AdminEventTrack;
use Symfony\Component\HttpFoundation\Response;

class AdminEventTracker
{
    private const IGNORED_ROUTE_SUFFIXES = [
        'dashboard.index',
        'profile.index',
    ];

    public static function trackFromRequest(Request $request, Response $response): void
    {
        $user = auth()->user();

        if (! $user instanceof User || $user->type !== User::TYPE_ADMIN) {
            return;
        }

        if ($response->getStatusCode() >= 400 || ! self::isAdminRequest($request)) {
            return;
        }

        $route = $request->route();

        if (! $route) {
            return;
        }

        $routeName = $route->getName() ?? '';

        if (self::shouldIgnore($request, $routeName)) {
            return;
        }

        [$subjectType, $subjectId, $subjectLabel] = self::resolveSubject($route->parameters());

        AdminEventTrack::create([
            'user_id' => $user->id,
            'event' => self::resolveEvent($request->method(), $routeName),
            'route_name' => $routeName ?: null,
            'method' => $request->method(),
            'description' => self::describeRoute($routeName, $request->method()),
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'subject_label' => $subjectLabel,
            'metadata' => [
                'url' => $request->fullUrl(),
                'route_parameters' => self::serializeRouteParameters($route->parameters()),
            ],
            'ip_address' => $request->ip(),
        ]);
    }

    private static function isAdminRequest(Request $request): bool
    {
        $path = trim($request->path(), '/');

        return Str::contains($path, '/admin/') || Str::startsWith($path, 'admin/');
    }

    private static function shouldIgnore(Request $request, string $routeName): bool
    {
        if ($routeName === 'admin.admins.show') {
            return true;
        }

        foreach (self::IGNORED_ROUTE_SUFFIXES as $suffix) {
            if (Str::endsWith($routeName, $suffix)) {
                return true;
            }
        }

        if ($request->isMethod('GET')) {
            return ! Str::endsWith($routeName, '.show');
        }

        return false;
    }

    private static function resolveEvent(string $method, string $routeName): string
    {
        if ($method === 'DELETE' || Str::endsWith($routeName, '.destroy') || Str::contains($routeName, 'delete')) {
            return AdminEventTrack::EVENT_DELETED;
        }

        if (in_array($method, ['POST'], true) && ! Str::endsWith($routeName, '.index')) {
            return AdminEventTrack::EVENT_CREATED;
        }

        if (in_array($method, ['PUT', 'PATCH'], true)) {
            return AdminEventTrack::EVENT_UPDATED;
        }

        if ($method === 'GET' && Str::endsWith($routeName, '.show')) {
            return AdminEventTrack::EVENT_VIEWED;
        }

        return AdminEventTrack::EVENT_ACTION;
    }

    private static function describeRoute(string $routeName, string $method): string
    {
        if ($routeName === '') {
            return __('Performed an action');
        }

        $parts = explode('.', $routeName);
        $action = array_pop($parts) ?: 'action';
        $resource = array_pop($parts) ?? 'page';
        $resourceLabel = self::resourceLabel($resource);

        return match ($action) {
            'index' => __('Listed :resource', ['resource' => $resourceLabel]),
            'show' => __('Viewed :resource', ['resource' => $resourceLabel]),
            'store', 'create' => __('Created :resource', ['resource' => $resourceLabel]),
            'update', 'edit' => __('Updated :resource', ['resource' => $resourceLabel]),
            'destroy' => __('Deleted :resource', ['resource' => $resourceLabel]),
            default => __('Performed :action on :resource', [
                'action' => Str::title(str_replace('_', ' ', $action)),
                'resource' => $resourceLabel,
            ]),
        };
    }

    private static function resourceLabel(string $resource): string
    {
        $key = 'user::event_tracks.resources.'.$resource;

        return \Illuminate\Support\Facades\Lang::has($key)
            ? __($key)
            : Str::title(str_replace(['-', '_'], ' ', $resource));
    }

  /**
     * @param  array<string, mixed>  $parameters
     * @return array{0: ?string, 1: ?int, 2: ?string}
     */
    private static function resolveSubject(array $parameters): array
    {
        foreach ($parameters as $value) {
            if ($value instanceof Model) {
                return [
                    $value->getMorphClass(),
                    (int) $value->getKey(),
                    self::subjectLabel($value),
                ];
            }
        }

        return [null, null, null];
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    private static function serializeRouteParameters(array $parameters): array
    {
        $serialized = [];

        foreach ($parameters as $key => $value) {
            if ($value instanceof Model) {
                $serialized[$key] = [
                    'type' => $value->getMorphClass(),
                    'id' => $value->getKey(),
                    'label' => self::subjectLabel($value),
                ];

                continue;
            }

            if (is_scalar($value) || $value === null) {
                $serialized[$key] = $value;
            }
        }

        return $serialized;
    }

    private static function subjectLabel(Model $model): string
    {
        foreach (['name', 'title', 'number', 'email'] as $attribute) {
            $value = $model->getAttribute($attribute);

            if (filled($value)) {
                return (string) $value;
            }
        }

        return class_basename($model).' #'.$model->getKey();
    }
}
