<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\TransactionController;

class AppDataController extends TransactionController
{
    /**
     * Return all modules with children and their respective sidebar data
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return $this->withTransaction(function () use ($request) {
            if ($request->user()) {
                $modules = cache()->rememberForever('modules', function () {
                    $modules = Module::with('children')->where('status', true)->whereNull('parent_key')->get();
                    return $modules->map(fn($module) => $this->formatModule($module));
                });

                return response()->json([
                    'modules'          => $modules,
                    'auth_permissions' => $request->user() ? $request->user()?->getAllPermissions()?->pluck('name')?->toArray() : [],
                ]);
            }

            return response()->json([
                'modules'          => [],
                'auth_permissions' => [],
            ]);
        });
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
            'name' => $module->name,
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
