<?php

namespace App\Services\FaceRecognition;

use App\Services\FaceRecognition\Contracts\FaceRecognitionService;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Attachment;

// your Spatie Media subclass

class AwsRekognitionService implements FaceRecognitionService
{
    protected RekognitionClient $client;
    protected int $threshold;
    protected int $maxFaces;

    public function __construct()
    {
        $this->client = new RekognitionClient([
            'region' => config('services.rekognition.region', env('AWS_DEFAULT_REGION', 'eu-central-1')),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $this->threshold = (int)config('services.rekognition.threshold', 90);
        $this->maxFaces = (int)config('services.rekognition.max_faces', 1);
    }

    public function compare(Attachment $profileImage, Attachment $probeImage): array
    {
        $sourceImageArg = $this->getRekognitionImageArg($profileImage);
        $targetImageArg = $this->getRekognitionImageArg($probeImage);

        if (!$sourceImageArg || !$targetImageArg) {
            return ['matched' => false, 'similarity' => null, 'reason' => 'Unable to resolve image sources.'];
        }

        // (Optional) sanity: exactly one face per image
        if (!$this->hasExactlyOneFace($sourceImageArg)) {
            return ['matched' => false, 'similarity' => null, 'reason' => 'Profile image must contain exactly one face.'];
        }
        if (!$this->hasExactlyOneFace($targetImageArg)) {
            return ['matched' => false, 'similarity' => null, 'reason' => 'Captured image must contain exactly one face.'];
        }

        $result = $this->client->compareFaces([
            'SourceImage' => $sourceImageArg,
            'TargetImage' => $targetImageArg,
            'SimilarityThreshold' => $this->threshold,
        ]);

        $matches = $result->get('FaceMatches') ?? [];
        if (empty($matches)) {
            return ['matched' => false, 'similarity' => null, 'reason' => 'No face match above threshold.'];
        }

        usort($matches, fn($a, $b) => ($b['Similarity'] ?? 0) <=> ($a['Similarity'] ?? 0));
        $best = $matches[0];
        $similarity = (float)($best['Similarity'] ?? 0);

        return ['matched' => $similarity >= $this->threshold, 'similarity' => $similarity];
    }

    /* -------------------------------------------------------
     | Helpers
     |--------------------------------------------------------*/

    /**
     * Build Rekognition "Image" arg: either ['S3Object'=>...] or ['Bytes'=>...].
     */
    protected function getRekognitionImageArg(Attachment $media): ?array
    {
        $disk = $media->disk ?: config('filesystems.default');
        $driver = config("filesystems.disks.{$disk}.driver", 'local');

        if ($driver === 's3') {
            $bucket = config('filesystems.disks.s3.bucket');
            $key = $this->resolveS3Key($media, $disk);
            if ($bucket && $key) {
                return ['S3Object' => ['Bucket' => $bucket, 'Name' => $key]];
            }
            // fallback to bytes if key/bucket unavailable
        }

        // local/public/backup: read bytes from disk
        $bytes = $this->readLocalLikeBytes($media);
        return $bytes ? ['Bytes' => $bytes] : null;
    }

    /**
     * Ensure exactly one face exists in the provided Rekognition "Image".
     */
    protected function hasExactlyOneFace(array $imageArg): bool
    {
        $res = $this->client->detectFaces([
            'Image' => $imageArg,
            'Attributes' => ['DEFAULT'],
        ]);

        $count = count($res->get('FaceDetails') ?? []);
        return $count === $this->maxFaces;
    }

    /**
     * For local/public disks, resolve absolute path and read bytes.
     */
    protected function readLocalLikeBytes(Attachment $media): ?string
    {
        // Spatie: getPath() returns absolute path for local-like disks
        if (method_exists($media, 'getPath')) {
            $abs = $media->getPath();
            if ($abs && is_file($abs)) {
                $b = @file_get_contents($abs);
                if ($b !== false) return $b;
            }
        }

        // fallback via Storage (in case you have a relative path)
        $disk = $media->disk ?: config('filesystems.default');
        $relative = $this->resolveRelativePath($media, $disk);
        if ($relative && Storage::disk($disk)->exists($relative)) {
            try {
                return Storage::disk($disk)->get($relative);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Resolve S3 key for the media:
     * 1) Use Spatie relative path if available
     * 2) Derive from disk URL + original_url
     * 3) Fallback: URL path part
     */
    protected function resolveS3Key(Attachment $media, string $disk): ?string
    {
        // 1) Spatie relative path (best)
        if (method_exists($media, 'getPathRelativeToRoot')) {
            $rel = $media->getPathRelativeToRoot(); // e.g. media/123/filename.jpg
            if ($rel) return ltrim($rel, '/');
        }

        // 2) Derive from disk URL
        $url = $media->original_url ?? $media->getUrl();
        if ($url) {
            $diskUrl = rtrim((string)config("filesystems.disks.{$disk}.url"), '/');
            if ($diskUrl && Str::startsWith($url, $diskUrl)) {
                return ltrim(Str::after($url, $diskUrl), '/');
            }

            // 3) Fallback to URL path
            $path = parse_url($url, PHP_URL_PATH);
            if ($path) return ltrim($path, '/');
        }

        return null;
    }

    /**
     * Resolve a relative path (for Storage::get) on local/public.
     */
    protected function resolveRelativePath(Attachment $media, string $disk): ?string
    {
        if (method_exists($media, 'getPathRelativeToRoot')) {
            $rel = $media->getPathRelativeToRoot();
            if ($rel) return ltrim($rel, '/');
        }

        // Try to strip disk URL from original_url
        $url = $media->original_url ?? $media->getUrl();
        if ($url) {
            $diskUrl = rtrim((string)config("filesystems.disks.{$disk}.url"), '/');
            if ($diskUrl && Str::startsWith($url, $diskUrl)) {
                return ltrim(Str::after($url, $diskUrl), '/');
            }
            $path = parse_url($url, PHP_URL_PATH);
            if ($path) return ltrim($path, '/');
        }

        return null;
    }
}
