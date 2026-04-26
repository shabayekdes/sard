<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Services\MediaDownloadService;
use Illuminate\Http\Request;

class TaskAttachmentController extends BaseController
{
    public function __construct(
        protected MediaDownloadService $mediaDownload
    ) {}

    public function store(Request $request, $taskId)
    {
        $task = Task::withPermissionCheck()
            ->where('id', $taskId)
            ->first();

        if (! $task) {
            return redirect()->back()->with('error', __('Task not found.'));
        }

        $validated = $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|string',
            'name' => 'nullable|string|max:255',
        ]);

        $sources = $validated['files'];
        $optionalName = $validated['name'] ?? null;

        $count = 0;
        foreach ($sources as $fileUrl) {
            $filePath = $this->convertToRelativePath($fileUrl);
            if ($filePath === '' || $filePath === null) {
                continue;
            }
            $baseName = basename(parse_url($filePath, PHP_URL_PATH) ?: $filePath);
            if (count($sources) === 1 && filled($optionalName)) {
                $name = $optionalName;
            } else {
                $name = $baseName;
            }
            if ($name === '' || $name === '.') {
                $name = 'attachment';
            }

            TaskAttachment::create([
                'task_id' => $task->id,
                'name' => $name,
                'file_path' => $filePath,
                'uploaded_by' => auth()->id(),
                'tenant_id' => $task->tenant_id,
            ]);
            $count++;
        }

        if ($count === 0) {
            return redirect()->back()->with('error', __('No valid files to add.'));
        }

        return redirect()->back()->with(
            'success',
            $count === 1
                ? __('File added to task.')
                : __(':count files added to task.', ['count' => $count])
        );
    }

    public function download($taskAttachmentId)
    {
        $attachment = $this->resolveAuthorizedAttachment($taskAttachmentId);
        if (! $attachment) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('File')]));
        }

        $response = $this->mediaDownload->download(
            $attachment->file_path,
            $attachment->name ?: 'attachment'
        );

        if ($response !== null) {
            return $response;
        }

        return redirect()->back()->with('error', __(':model not found.', ['model' => __('File')]));
    }

    public function destroy($taskAttachmentId)
    {
        $attachment = $this->resolveAuthorizedAttachment($taskAttachmentId);
        if (! $attachment) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('File')]));
        }

        $attachment->delete();

        return redirect()->back()->with('success', __(':model removed.', ['model' => __('File')]));
    }

    private function resolveAuthorizedAttachment(int|string $taskAttachmentId): ?TaskAttachment
    {
        $attachment = TaskAttachment::query()
            ->where('id', $taskAttachmentId)
            ->where('tenant_id', createdBy())
            ->first();
        if (! $attachment) {
            return null;
        }

        $canAccessTask = Task::withPermissionCheck()
            ->where('id', $attachment->task_id)
            ->exists();

        if (! $canAccessTask) {
            return null;
        }

        return $attachment;
    }

    private function convertToRelativePath(string $url): string
    {
        if (! $url) {
            return $url;
        }
        if (! str_starts_with($url, 'http')) {
            return $url;
        }
        $storageIndex = strpos($url, '/storage/');
        if ($storageIndex !== false) {
            return substr($url, $storageIndex);
        }

        return $url;
    }
}
