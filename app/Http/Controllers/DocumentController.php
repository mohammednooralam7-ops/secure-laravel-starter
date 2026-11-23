<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\AuditLogger;
use App\Services\SafeUpload;
use Illuminate\Http\Request;
use InvalidArgumentException;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['file' => 'required|file']);
        try {
            $path = SafeUpload::store($request->file('file'));
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
        $doc = Document::create([
            'user_id' => $request->user()->id,
            'path' => $path,
            'original_name' => substr($request->file('file')->getClientOriginalName(), 0, 120),
        ]);
        AuditLogger::log('document.upload', $request->user()->id, ['document_id' => $doc->id]);
        return response()->json(['id' => $doc->id], 201);
    }

    public function show(Request $request, Document $document)
    {
        $this->authorize('view', $document); // IDOR protection
        AuditLogger::log('document.view', $request->user()->id, ['document_id' => $document->id]);
        return response()->json(['id' => $document->id, 'name' => $document->original_name]);
    }
}
