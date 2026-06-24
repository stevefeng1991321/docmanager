<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        $documents = Resource::with('category')
            ->orderBy('title')
            ->get(['id', 'title', 'status', 'category_id', 'created_at']);

        $selectedA = $request->integer('a');
        $selectedB = $request->integer('b');

        if ($selectedA && $selectedB && $selectedA !== $selectedB) {
            return redirect()->route('admin.compare.show', [$selectedA, $selectedB]);
        }

        return view('admin.compare.index', compact('documents', 'selectedA', 'selectedB'));
    }

    public function show(Resource $a, Resource $b)
    {
        $a->load('category', 'tags', 'uploader');
        $b->load('category', 'tags', 'uploader');

        $metaDiff  = $this->compareMetadata($a, $b);
        $textDiff  = $this->diffLines(
            $this->normaliseText($a->content ?? ''),
            $this->normaliseText($b->content ?? '')
        );

        return view('admin.compare.show', compact('a', 'b', 'metaDiff', 'textDiff'));
    }

    // ─── helpers ─────────────────────────────────────────────────────────────

    private function compareMetadata(Resource $a, Resource $b): array
    {
        $aTagNames = $a->tags->pluck('name')->sort()->values()->implode(', ');
        $bTagNames = $b->tags->pluck('name')->sort()->values()->implode(', ');

        return [
            ['label' => 'Title',             'a' => $a->title,                     'b' => $b->title],
            ['label' => 'Status',            'a' => $a->status,                    'b' => $b->status],
            ['label' => 'Category',          'a' => $a->category?->name ?? '—',    'b' => $b->category?->name ?? '—'],
            ['label' => 'Tags',              'a' => $aTagNames ?: '—',             'b' => $bTagNames ?: '—'],
            ['label' => 'File type',         'a' => $a->file_type ?? '—',          'b' => $b->file_type ?? '—'],
            ['label' => 'File size',         'a' => $this->humanBytes($a->file_size), 'b' => $this->humanBytes($b->file_size)],
            ['label' => 'Uploaded by',       'a' => $a->uploader?->name ?? '—',   'b' => $b->uploader?->name ?? '—'],
            ['label' => 'Uploaded at',       'a' => $a->created_at?->format('Y-m-d H:i'), 'b' => $b->created_at?->format('Y-m-d H:i')],
            ['label' => 'Original filename', 'a' => $a->original_filename ?? '—', 'b' => $b->original_filename ?? '—'],
            ['label' => 'Description',       'a' => $a->description ?? '—',       'b' => $b->description ?? '—'],
        ];
    }

    private function normaliseText(string $text): string
    {
        return trim(preg_replace('/\r\n?/', "\n", $text));
    }

    /**
     * Myers-inspired LCS line diff.
     * Returns an array of ['type' => 'context'|'added'|'removed', 'text' => string].
     */
    private function diffLines(string $aText, string $bText): array
    {
        if ($aText === '' && $bText === '') {
            return [];
        }

        $aLines = $aText === '' ? [] : explode("\n", $aText);
        $bLines = $bText === '' ? [] : explode("\n", $bText);

        $lcs = $this->lcs($aLines, $bLines);

        $result = [];
        $ia = 0; $ib = 0;

        foreach ($lcs as $pair) {
            [$la, $lb] = $pair;
            while ($ia < $la) {
                $result[] = ['type' => 'removed', 'text' => $aLines[$ia++]];
            }
            while ($ib < $lb) {
                $result[] = ['type' => 'added', 'text' => $bLines[$ib++]];
            }
            $result[] = ['type' => 'context', 'text' => $aLines[$ia]];
            $ia++; $ib++;
        }

        while ($ia < count($aLines)) {
            $result[] = ['type' => 'removed', 'text' => $aLines[$ia++]];
        }
        while ($ib < count($bLines)) {
            $result[] = ['type' => 'added', 'text' => $bLines[$ib++]];
        }

        return $result;
    }

    /** Returns pairs [ia, ib] of matching lines (LCS). Capped at 2000 lines each to avoid OOM. */
    private function lcs(array $a, array $b): array
    {
        $a = array_slice($a, 0, 2000);
        $b = array_slice($b, 0, 2000);
        $na = count($a); $nb = count($b);
        if ($na === 0 || $nb === 0) return [];

        // Build DP table
        $dp = array_fill(0, $na + 1, array_fill(0, $nb + 1, 0));
        for ($i = 1; $i <= $na; $i++) {
            for ($j = 1; $j <= $nb; $j++) {
                $dp[$i][$j] = ($a[$i-1] === $b[$j-1])
                    ? $dp[$i-1][$j-1] + 1
                    : max($dp[$i-1][$j], $dp[$i][$j-1]);
            }
        }

        // Traceback
        $pairs = [];
        $i = $na; $j = $nb;
        while ($i > 0 && $j > 0) {
            if ($a[$i-1] === $b[$j-1]) {
                array_unshift($pairs, [$i-1, $j-1]);
                $i--; $j--;
            } elseif ($dp[$i-1][$j] >= $dp[$i][$j-1]) {
                $i--;
            } else {
                $j--;
            }
        }
        return $pairs;
    }

    private function humanBytes(?int $bytes): string
    {
        if ($bytes === null) return '—';
        if ($bytes < 1024)        return $bytes . ' B';
        if ($bytes < 1048576)     return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1073741824)  return round($bytes / 1048576, 1) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }
}
