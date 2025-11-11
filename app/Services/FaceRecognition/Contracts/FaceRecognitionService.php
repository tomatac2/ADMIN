<?php

namespace App\Services\FaceRecognition\Contracts;

use App\Models\Attachment; // or your media model type

interface FaceRecognitionService
{
    /**
     * Compare driver profile image with a fresh-captured image.
     *
     * @param  Attachment $profileImage  The driver’s profile image record
     * @param  Attachment $probeImage    The just-uploaded face image record
     * @return array{
     *   matched: bool,
     *   similarity: float|null,
     *   reason?: string
     * }
     */
    public function compare(Attachment $profileImage, Attachment $probeImage): array;
}
