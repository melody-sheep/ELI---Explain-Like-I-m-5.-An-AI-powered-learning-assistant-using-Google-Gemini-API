<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TextExtractorService
{
    public function extract($filePath, $extension)
    {
        $content = '';
        
        switch (strtolower($extension)) {
            case 'txt':
                $content = file_get_contents($filePath);
                break;
            case 'pdf':
                $content = $this->extractPdf($filePath);
                break;
            case 'docx':
                $content = $this->extractDocx($filePath);
                break;
            case 'doc':
                $content = $this->extractDoc($filePath);
                break;
            default:
                $content = file_get_contents($filePath);
        }
        
        // Clean up the content
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        // Limit content length for AI
        if (strlen($content) > 15000) {
            $content = substr($content, 0, 15000) . "...";
        }
        
        Log::info('Text extraction completed', [
            'extension' => $extension,
            'length' => strlen($content)
        ]);
        
        return $content;
    }
    
    private function extractPdf($filePath)
    {
        // Try pdftotext first
        $pdftotext = base_path('pdftotext.exe');
        
        if (file_exists($pdftotext)) {
            $command = '"' . $pdftotext . '" -layout ' . escapeshellarg($filePath) . ' - 2>nul';
            $output = shell_exec($command);
            
            if ($output && strlen($output) > 100) {
                Log::info('PDF extracted using pdftotext', ['length' => strlen($output)]);
                return $output;
            }
        }
        
        // Fallback: Try to extract readable text from raw PDF
        $raw = file_get_contents($filePath);
        // Remove binary garbage and keep readable text
        $text = preg_replace('/[^\x20-\x7E\x0A\x0D]/', ' ', $raw);
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Look for common PDF text patterns
        if (preg_match_all('/\(([^)]+)\)/i', $raw, $matches)) {
            $extracted = implode(' ', $matches[1]);
            if (strlen($extracted) > 100) {
                return $extracted;
            }
        }
        
        Log::warning('PDF extraction limited', ['length' => strlen($text)]);
        return substr($text, 0, 5000);
    }
    
    private function extractDocx($filePath)
    {
        try {
            $zip = new \ZipArchive();
            $content = '';
            
            if ($zip->open($filePath) === true) {
                if (($index = $zip->locateName('word/document.xml')) !== false) {
                    $data = $zip->getFromIndex($index);
                    // Remove XML tags and decode HTML entities
                    $content = strip_tags(str_replace(['</w:p>', '</w:t>'], [' ', ' '], $data));
                    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5);
                }
                $zip->close();
            }
            
            return $content;
        } catch (\Exception $e) {
            Log::error('DOCX extraction failed: ' . $e->getMessage());
            return '';
        }
    }
    
    private function extractDoc($filePath)
    {
        // Old .doc format is complex - try basic extraction
        $content = file_get_contents($filePath);
        // Remove non-printable characters
        $content = preg_replace('/[^\x20-\x7E\x0A\x0D]/', ' ', $content);
        return substr($content, 0, 5000);
    }
}