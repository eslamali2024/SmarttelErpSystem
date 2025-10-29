<?php


if (!function_exists('get_vite_path')) {
    function get_vite_path($component)
    {
        if (!is_null($component)) {
            if (Illuminate\Support\Str::contains($component, '::')) {
                $component = explode('::', $component);
                $path = "Modules/{$component[0]}/resources/js/pages/{$component[1]}.vue";
            } else {
                $path = "resources/js/pages/{$component}.vue";
            }
        }
        return $path;
    }
}
