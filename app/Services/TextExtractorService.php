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
        }
        
        // Clean up the content
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        if (strlen($content) > 10000) {
            $content = substr($content, 0, 10000);
        }
        
        return $content;
    }
    
    private function extractPdf($filePath)
    {
        // Path to pdftotext.exe in your project
        $pdftotext = base_path('pdftotext.exe');
        
        if (file_exists($pdftotext)) {
            $command = '"' . $pdftotext . '" -layout ' . escapeshellarg($filePath) . ' -';
            $output = shell_exec($command);
            
            if ($output && strlen($output) > 100) {
                Log::info('PDF extracted using pdftotext', ['length' => strlen($output)]);
                return $output;
            }
        }
        
        // Fallback: basic extraction
        Log::warning('pdftotext failed, using fallback');
        $raw = file_get_contents($filePath);
        return preg_replace('/[^\x20-\x7E\x0A\x0D]/', ' ', $raw);
    }
    
    private function extractDocx($filePath)
    {
        try {
            $zip = new \ZipArchive();
            $content = '';
            if ($zip->open($filePath) === true) {
                if (($index = $zip->locateName('word/document.xml')) !== false) {
                    $data = $zip->getFromIndex($index);
                    $content = strip_tags(str_replace(['</w:p>', '</w:t>'], [' ', ' '], $data));
                    $content = html_entity_decode($content);
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
        $content = file_get_contents($filePath);
        $content = preg_replace('/[^\x20-\x7E\x0A\x0D]/', ' ', $content);
        return substr($content, 0, 5000);
    }
}