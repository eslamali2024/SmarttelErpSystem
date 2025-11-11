<?php

namespace Modules\UserManagement\Services;

use Illuminate\Support\Facades\File;
use App\Models\Approval\ApprovalFlow;
use App\Enums\Approval\ApprovalStatusEnum;

class ApprovalFlowService
{
    /**
     * Create a new flow.
     *
     * @param array<string, mixed>  $data
     * @return ApprovalFlow
     */
    public function store(array $data)
    {
        return ApprovalFlow::create($data);
    }

    /**
     * Update a flow.
     *
     * @param ApprovalFlow $approval_flow
     * @param array<string, mixed> $data
     * @return ApprovalFlow
     */
    public function update(ApprovalFlow $approval_flow, array $data)
    {
        return $approval_flow->update($data);
    }

    /**
     * Delete a flow.
     *
     * @param ApprovalFlow  $flow
     * @return bool
     */
    public function destroy(ApprovalFlow $approval_flow): bool
    {
        $approval_flow->approvalFlowSteps()->delete();
        return $approval_flow->delete();
    }

    /**
     * Returns an array of all models in the application, both from the default app/Models
     * directory and from the Models directory of each HMVC module.
     *
     * The array is keyed by the FQCN of each model, with the value being the same as the key.
     * This allows for easy retrieval of a model class by its name.
     *
     * @return array<string, string>
     */
    function getAllModels(): array
    {
        $models = [];

        // Laravel default models (app/Models)
        $defaultModelsPath = app_path('Models');
        if (File::exists($defaultModelsPath)) {
            foreach (File::allFiles($defaultModelsPath) as $file) {
                $class = 'App\\Models\\' . $file->getFilenameWithoutExtension();
                if (!class_exists($class)) {
                    require_once $file->getPathname();
                }
                if (class_exists($class) && is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class)) {
                    $models[] = $class;
                }
            }
        }

        // HMVC modules (Modules/*/Entities or Modules/*/Models)
        foreach (File::directories(base_path('Modules')) as $modulePath) {
            $moduleName = basename($modulePath);
            // dd($moduleName);
            $modelDir = "$modulePath/App/Models";
            if (!File::exists($modelDir)) continue;

            foreach (File::allFiles($modelDir) as $file) {
                $class = "Modules\\" . $moduleName . "\Models\\" . $file->getFilenameWithoutExtension();
                if (!class_exists($class)) {
                    require_once $file->getPathname();
                }
                if (class_exists($class) && is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class)) {
                    $models[] = $class;
                }
            }
        }

        return array_combine($models, $models);
    }

    /**
     * Sync all items of the given flow with approval requests.
     *
     * @param ApprovalFlow $approval_flow
     * @return void
     */
    public function syncRequestsForFlow(ApprovalFlow $approval_flow)
    {
        $flow_requests_ids = $approval_flow->approvalRequests()->pluck('approvable_id')->toArray();
        $items             = $approval_flow->approvable()->get(['id']);
        $items_need_requests = $items->filter(function ($item) use ($flow_requests_ids) {
            return !in_array($item->id, $flow_requests_ids);
        });

        if ($items_need_requests->count() > 0) {
            foreach ($items_need_requests as $item) {
                $approval_flow->approvalRequests()->create([
                    'approvable_id'     => $item->id,
                    'approvable_type'   => get_class($item),
                    'creator_id'        => auth()?->user()?->id,
                    'status'            => ApprovalStatusEnum::PENDING->value
                ]);
            }
        }
    }
}
