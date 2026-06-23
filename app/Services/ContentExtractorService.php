<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ContentExtractorService
{
    /**
     * Extract plain text from a stored file.
     * Returns null if the format is unsupported or extraction fails.
     */
    public function extract(string $filePath, string $originalFilename): ?string
    {
        $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));

        try {
            return match(true) {
                $ext === 'pdf'               => $this->extractPdf($filePath),
                in_array($ext, ['docx'])     => $this->extractDocx($filePath),
                in_array($ext, ['txt', 'md'])=> $this->extractText($filePath),
                default                      => null,
            };
        } catch (\Throwable $e) {
            \Log::warning("Content extraction failed for {$originalFilename}: " . $e->getMessage());
            return null;
        }
    }

    private function extractPdf(string $filePath): ?string
    {
        $absolutePath = Storage::disk('local')->path($filePath);

        $parser   = new \Smalot\PdfParser\Parser();
        $pdf      = $parser->parseFile($absolutePath);
        $text     = $pdf->getText();

        return $this->clean($text);
    }

    private function extractDocx(string $filePath): ?string
    {
        $absolutePath = Storage::disk('local')->path($filePath);

        $phpWord  = \PhpOffice\PhpWord\IOFactory::load($absolutePath);
        $sections = $phpWord->getSections();
        $parts    = [];

        foreach ($sections as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $parts[] = $element->getText();
                } elseif (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $child) {
                        if (method_exists($child, 'getText')) {
                            $parts[] = $child->getText();
                        }
                    }
                }
            }
        }

        return $this->clean(implode(' ', array_filter($parts)));
    }

    private function extractText(string $filePath): ?string
    {
        $text = Storage::disk('local')->get($filePath);
        return $this->clean($text ?? '');
    }

    private function clean(string $text): ?string
    {
        // Normalize whitespace, strip null bytes
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);

        // MySQL LONGTEXT limit: 4GB — but truncate at 5MB for sane indexing
        if (strlen($text) > 5_000_000) {
            $text = substr($text, 0, 5_000_000);
        }

        return $text !== '' ? $text : null;
    }
}
