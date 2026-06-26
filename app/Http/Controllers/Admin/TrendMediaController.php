<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BasicKnowledgeTrend;
use App\Models\ScienceTechTrend;
use App\Models\TrendMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrendMediaController extends Controller
{
    public function storeScienceTech(Request $request, ScienceTechTrend $trend)
    {
        return $this->store($request, $trend, 'science-tech');
    }

    public function storeBasicKnowledge(Request $request, BasicKnowledgeTrend $trend)
    {
        return $this->store($request, $trend, 'basic-knowledge');
    }

    public function update(Request $request, TrendMedia $trendMedia)
    {
        $request->validate([
            'title'     => ['nullable', 'string', 'max:255'],
            'embed_url' => ['nullable', 'url', 'max:500'],
            'file'      => ['nullable', 'file', 'max:51200', 'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov'],
        ]);

        $changes = ['title' => $request->input('title')];

        if ($request->hasFile('file')) {
            if ($trendMedia->file_path) {
                Storage::disk('public')->delete($trendMedia->file_path);
            }
            $section  = str_contains($trendMedia->mediable_type, 'ScienceTech') ? 'science-tech' : 'basic-knowledge';
            $folder   = "trend-media/{$section}/{$trendMedia->mediable_id}";
            $changes['file_path'] = $request->file('file')->store($folder, 'public');
            $changes['embed_url'] = null;
        } elseif ($trendMedia->embed_url && $request->filled('embed_url')) {
            $changes['embed_url'] = TrendMedia::normalizeEmbedUrl($request->input('embed_url'));
        }

        $trendMedia->update($changes);
        AuditLog::record('trend_media.updated', $trendMedia->id, []);

        return back()->with('message', 'Media updated.');
    }

    public function destroy(TrendMedia $trendMedia)
    {
        if ($trendMedia->file_path) {
            Storage::disk('public')->delete($trendMedia->file_path);
        }

        $trendMedia->delete();
        AuditLog::record('trend_media.deleted', $trendMedia->id, []);

        return back()->with('message', 'Media deleted.');
    }

    private function store(Request $request, $mediable, string $section)
    {
        $request->validate([
            'type'      => ['required', 'in:image,video'],
            'title'     => ['nullable', 'string', 'max:255'],
            'file'      => ['nullable', 'file', 'max:51200', 'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov'],
            'embed_url' => ['nullable', 'url', 'max:500'],
        ]);

        $type     = $request->input('type');
        $hasFile  = $request->hasFile('file');
        $embedUrl = $request->input('embed_url');

        if ($type === 'image' && !$hasFile) {
            return back()->withErrors(['file' => 'An image file is required.'])->withInput();
        }
        if ($type === 'video' && !$hasFile && !$embedUrl) {
            return back()->withErrors(['embed_url' => 'Provide a video file or an embed URL.'])->withInput();
        }

        $filePath = null;
        if ($hasFile) {
            $filePath = $request->file('file')->store("trend-media/{$section}/{$mediable->id}", 'public');
        }

        $mediable->media()->create([
            'type'        => $type,
            'title'       => $request->input('title'),
            'file_path'   => $filePath,
            'embed_url'   => $filePath ? null : TrendMedia::normalizeEmbedUrl($embedUrl),
            'uploaded_by' => auth()->id(),
        ]);

        AuditLog::record('trend_media.added', $mediable->id, ['section' => $section]);

        return back()->with('message', 'Media added.');
    }
}
