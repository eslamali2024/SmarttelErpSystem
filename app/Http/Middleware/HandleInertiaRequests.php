<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Foundation\Inspiring;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $modules = cache()->rememberForever('modules', function () {
            $modules = Module::with('children')->where('status', true)->whereNull('parent_key')->get();
            return $modules->map(fn($module) => $this->formatModule($module));
        });

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
                'auth_permissions' => $request->user() ? $request->user()->getAllPermissions()->pluck('name')->toArray() : [],
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'modules' => $modules,
        ];
    }

    /**
     * Format a module into an array that can be used for the sidebar.
     *
     * @param  \App\Models\Module  $module
     * @return array
     */
    private  function formatModule($module)
    {
        return [
            'name' => str($module->name)->limit(15),
            'path' => "/" . $module->path,
            'icon' => $module->icon,
            'permission_title' => $module->permission_title,
            'parent_key' => $module->parent_key,
            'children' => $module->children->isNotEmpty()
                ? $module->children->map(fn($child) => $this->formatModule($child))
                : [],
        ];
    }
}
