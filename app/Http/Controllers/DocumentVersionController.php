<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Services\MediaDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentVersionController extends Controller
{
    public function __construct(
        protected MediaDownloadService $mediaDownload
    ) {}
    public function index(Request $request)
    {
        $query = DocumentVersion::withPermissionCheck()
            ->with(['document', 'creator'])
            ->withPermissionCheck();

        if ($request->has('document_id') && !empty($request->document_id)) {
            $query->where('document_id', $request->document_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('version_number', 'like', '%' . $request->search . '%')
                    ->orWhere('changes_description', 'like', '%' . $request->search . '%');
            });
        }

        $query->latest('id');
        $versions = $query->paginate($request->per_page ?? 10);

        $documents = Document::where('tenant_id', createdBy())
            ->get(['id', 'name']);

        return Inertia::render('document-management/versions/index', [
            'versions' => $versions,
            'documents' => $documents,
            'filters' => $request->all(['search', 'document_id', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'file' => 'required|string',
            'changes_description' => 'nullable|string',
        ]);

        $document = Document::where('id', $validated['document_id'])
            ->where('tenant_id', createdBy())
            ->first();

        if (!$document) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Document')]));
        }

        // Convert file path to relative path
        $validated['file_path'] = $validated['file'];
        if (!empty($validated['file_path'])) {
            $validated['file_path'] = $this->convertToRelativePath($validated['file_path']);
        }
        unset($validated['file']);

        DB::transaction(function () use ($validated, $document, $request) {
            // Mark current version as not current
            DocumentVersion::where('document_id', $document->id)
                ->update(['is_current' => false]);

            // Get next version number
            $lastVersion = DocumentVersion::where('document_id', $document->id)
                ->orderBy('version_number', 'desc')
                ->first();
            
            $versionParts = explode('.', $lastVersion ? $lastVersion->version_number : '0.0');
            $newVersion = $versionParts[0] . '.' . ((int)$versionParts[1] + 1);

            // Create new version
            DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $newVersion,
                'file_path' => $validated['file_path'],
                'changes_description' => $validated['changes_description'] ?? null,
                'is_current' => true,
                'tenant_id' => createdBy(),
            ]);

            // Update document with new version info
            $document->update([
                'file_path' => $validated['file_path'],
            ]);
        });

        return redirect()->back()->with('success', __(':model created successfully.', ['model' => __('Version')]));
    }

    private function convertToRelativePath(string $url): string
    {
        if (!$url) return $url;

        // If it's already a relative path, return as is
        if (!str_starts_with($url, 'http')) {
            return $url;
        }

        // Extract the path after /storage/
        $storageIndex = strpos($url, '/storage/');
        if ($storageIndex !== false) {
            return substr($url, $storageIndex);
        }

        return $url;
    }

    public function destroy($versionId)
    {
        $version = DocumentVersion::withPermissionCheck()
            ->where('id', $versionId)
            ->first();

        if (!$version) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Version')]));
        }

        if ($version->is_current) {
            return redirect()->back()->with('error', __('Cannot delete current :model.', ['model' => __('Version')]));
        }

        // Delete file from storage
        if ($version->file_path && Storage::disk('public')->exists(str_replace('/storage/', '', $version->file_path))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $version->file_path));
        }

        $version->delete();

        return redirect()->back()->with('success', __(':model deleted successfully', ['model' => __('Version')]));
    }

    public function download($versionId)
    {
        $version = DocumentVersion::withPermissionCheck()
            ->with('document')
            ->where('id', $versionId)
            ->first();

        if (!$version || !$version->file_path) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Version')]));
        }

        $downloadName = $version->document?->name ?? basename($version->file_path);
        $response = $this->mediaDownload->download($version->file_path, $downloadName);

        if ($response !== null) {
            return $response;
        }

        return redirect()->back()->with('error', __('File not found.'));
    }

    public function restore($versionId)
    {
        $version = DocumentVersion::withPermissionCheck()
            ->where('id', $versionId)
            ->first();

        if (!$version) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Version')]));
        }

        DB::transaction(function () use ($version) {
            // Mark all versions as not current
            DocumentVersion::where('document_id', $version->document_id)
                ->update(['is_current' => false]);

            // Mark this version as current
            $version->update(['is_current' => true]);

            // Update document with this version's info
            $version->document->update([
                'file_path' => $version->file_path,
            ]);
        });

        return redirect()->back()->with('success', __(':model restored successfully.', ['model' => __('Version')]));
    }
}