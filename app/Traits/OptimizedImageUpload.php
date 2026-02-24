<?php

namespace App\Traits;

use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

trait OptimizedImageUpload
{
    /**
     * Optimize and upload image with multiple sizes
     */
    public function optimizeAndUploadImage(UploadedFile $file, string $directory = 'uploads'): array
    {
        $filename = uniqid() . '_' . time();
        $sizes = [
            'thumbnail' => [150, 150],
            'medium' => [400, 400],
            'large' => [800, 800],
            'original' => null
        ];
        
        $uploadedFiles = [];
        
        foreach ($sizes as $sizeName => $dimensions) {
            $image = Image::read($file);
            
            if ($dimensions) {
                // Resize while maintaining aspect ratio
                $image->scale(width: $dimensions[0], height: $dimensions[1]);
            }
            
            // Convert to WebP for better compression
            $webpData = $image->encode(new WebpEncoder(quality: 85));
            
            $path = "$directory/{$sizeName}_{$filename}.webp";
            Storage::disk('public')->put($path, $webpData);
            
            $uploadedFiles[$sizeName] = $path;
        }
        
        return $uploadedFiles;
    }
    
    /**
     * Delete all image variants
     */
    public function deleteImageVariants(array $paths): void
    {
        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }
    }
    
    /**
     * Generate responsive image HTML
     */
    public function generateResponsiveImageHtml(array $imagePaths, string $alt = '', string $class = ''): string
    {
        if (empty($imagePaths)) {
            return '';
        }
        
        $srcset = [];
        foreach ($imagePaths as $size => $path) {
            if ($size !== 'original') {
                $url = Storage::url($path);
                $srcset[] = "$url {$this->getSizeWidth($size)}w";
            }
        }
        
        $mainSrc = Storage::url($imagePaths['medium'] ?? $imagePaths['original']);
        $srcsetAttr = implode(', ', $srcset);
        
        return "<img src=\"$mainSrc\" srcset=\"$srcsetAttr\" sizes=\"(max-width: 768px) 100vw, 50vw\" alt=\"$alt\" class=\"$class lazy\" loading=\"lazy\">";
    }
    
    private function getSizeWidth(string $size): int
    {
        return match ($size) {
            'thumbnail' => 150,
            'medium' => 400,
            'large' => 800,
            default => 800
        };
    }
}
