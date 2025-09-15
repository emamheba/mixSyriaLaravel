<?php

namespace App\Actions\Media\v1;



use App\Models\Backend\MediaUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Facades\Image;

class MediaHelper
{
    private const IMAGE_SIZES = [
        'grid' => [350, null],
        'large' => [740, null],
        'semi-large' => [540, 350],
        'tiny' => [15, 15],
        'thumb' => [150, 150],
        'square_120' => [120, 120], // المقاس الجديد المضاف
        'square_80' => [80, 80],   // المقاس الجديد المضاف
    ];

    private const STORAGE_DISK = 'public';
    private const BASE_DIR = 'uploads/media-uploader';
    private const PER_PAGE = 20;

    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function fetchMediaImages(string $type = 'admin', ?int $selectedId = null): array
    {
        $query = MediaUpload::query()
            ->when($type === 'web', fn ($q) => $q->where('user_id', Auth::id()))
            ->where('type', $type)
            ->latest()
            ->take(self::PER_PAGE);

        $images = $query->get();
        $selectedImage = $selectedId ? MediaUpload::find($selectedId) : null;

        return [
            'selected' => $selectedImage ? $this->formatImage($selectedImage) : null,
            'images' => $images->map(fn ($image) => $this->formatImage($image))->filter()
        ];
    }

    public function deleteMediaImage($imageId, string $type = 'web'): bool
    {
        $image = MediaUpload::where('id', $imageId)
            ->when($type === 'web', fn ($q) => $q->where('user_id', Auth::id()))
            ->firstOrFail();

        $this->deleteImageFiles($image);
        return $image->delete();
    }

    public function uploadMedia(UploadedFile $file, string $type = 'admin'): ?MediaUpload
    {
        try {
            $fileName = $this->generateFileName($file);
            $filePath = $this->storeOriginalImage($file, $fileName);
            $variations = $this->createImageVariations($file, $fileName);

            return MediaUpload::create([
                'title' => $file->getClientOriginalName(),
                'size' => $this->formatBytes($file->getSize()),
                'path' => $fileName,
                'dimensions' => $this->getImageDimensions($file),
                'type' => $type,
                'user_id' => Auth::id(),
                'meta' => ['variations' => $variations]
            ]);

        } catch (\Exception $e) {
            logger()->error('Media upload failed: ' . $e->getMessage());
            $this->rollbackUpload($fileName);
            return null;
        }
    }

    public function loadMoreImages(int $skip, string $type = 'admin'): array
    {
        return MediaUpload::query()
            ->when($type === 'web', fn ($q) => $q->where('user_id', Auth::id()))
            ->where('type', $type)
            ->latest()
            ->skip($skip)
            ->take(self::PER_PAGE)
            ->get()
            ->map(fn ($image) => $this->formatImage($image))
            ->filter()
            ->toArray();
    }

    private function formatImage(MediaUpload $image): ?array
    {
        $mainPath = self::BASE_DIR.'/'.$image->path;
        $gridPath = self::BASE_DIR.'/grid/'.$image->path;

        if (!Storage::disk(self::STORAGE_DISK)->exists($mainPath)) {
            return null;
        }

        return [
            'image_id' => $image->id,
            'title' => $image->title,
            'dimensions' => $image->dimensions,
            'alt' => $image->alt,
            'size' => $image->size,
            'path' => $image->path,
            'img_url' => Storage::disk(self::STORAGE_DISK)->url($gridPath),
            'upload_at' => $image->created_at->format('d M Y')
        ];
    }

    private function generateFileName(UploadedFile $file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        return Str::slug($name).'-'.time().'.'.$file->getClientOriginalExtension();
    }

    private function storeOriginalImage(UploadedFile $file, string $fileName): string
    {
        $path = self::BASE_DIR.'/'.$fileName;
        Storage::disk(self::STORAGE_DISK)->put(
            $path,
            $this->imageManager->read($file)->resize(1200, null)->encode()
        );
        return $path;
    }

    private function createImageVariations(UploadedFile $file, string $fileName): array
    {
        $variations = [];
        $image = $this->imageManager->read($file);

        foreach (self::IMAGE_SIZES as $size => $dimensions) {
            $path = self::BASE_DIR."/{$size}/{$fileName}";
            $this->processVariation($image, $dimensions, $path, $size === 'tiny');
            $variations[$size] = $path;
        }

        return $variations;
    }

    private function processVariation($image, array $dimensions, string $path, bool $blur = false): void
    {
        [$width, $height] = $dimensions;
        $img = clone $image;

        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            if (is_null($height)) $constraint->upsize();
        });

        if ($blur) {
            $img->blur(50);
        }

        Storage::disk(self::STORAGE_DISK)->put($path, $img->encode());
    }

    private function deleteImageFiles(MediaUpload $image): void
    {
        $paths = array_merge(
            [$image->path],
            $image->meta['variations'] ?? []
        );

        foreach ($paths as $path) {
            Storage::disk(self::STORAGE_DISK)->delete($path);
        }
    }

    private function rollbackUpload(string $fileName): void
    {
        $paths = array_map(fn ($size) => self::BASE_DIR."/{$size}/{$fileName}", array_keys(self::IMAGE_SIZES));
        $paths[] = self::BASE_DIR.'/'.$fileName;

        Storage::disk(self::STORAGE_DISK)->delete($paths);
    }

    private function getImageDimensions(UploadedFile $file): string
    {
        [$width, $height] = getimagesize($file->getPathname());
        return "{$width}x{$height} pixels";
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision).' '.$units[$pow];
    }
}
