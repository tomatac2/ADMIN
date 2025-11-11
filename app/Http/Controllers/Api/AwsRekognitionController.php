<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Aws\Rekognition\RekognitionClient;
use App\Http\Traits\MessageTrait;


use App\Http\Controllers\Controller;


class AwsRekognitionController extends Controller

{
    use MessageTrait;

    protected $rekognition;

    public function __construct()
    {
        $this->rekognition = new RekognitionClient([
            'version'     => 'latest',
            'region'      => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

public function verifyIdentity(Request $request)
{
    try {
        [$selfieBytes, $documentBytes] = $this->validateAndReadImages($request);
        $this->validateFaces($selfieBytes, $documentBytes);
        $similarity = $this->compareFaces($selfieBytes, $documentBytes);

        return response()->json([
            'success' => true,
            'verified' => $similarity >= 95,
            'similarity' => round($similarity, 2),
            'message' => $similarity >= 95 ? 'تمت مطابقة الوجه بنجاح.' : 'الوجوه غير متطابقة.'
        ]);
    } catch (\Throwable $e) {
        return $this->errorResponse($e->getMessage());
    }
}


private function validateAndReadImages($request)
{
    if (!$request->hasFile('selfieImage') || !$request->hasFile('documentImage'))
        throw new \Exception('يجب إرسال selfieImage و documentImage.');

    $allowed = ['jpg', 'jpeg', 'png'];
    $files = [$request->file('selfieImage'), $request->file('documentImage')];

    foreach ($files as $file)
        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowed))
            throw new \Exception('الصور يجب أن تكون بصيغة JPG أو JPEG أو PNG فقط.');

    $bytes = array_map(fn($f) => file_get_contents($f->getRealPath()), $files);
    if (empty($bytes[0]) || empty($bytes[1]))
        throw new \Exception('إحدى الصور فارغة أو غير صالحة.');

    return $bytes;
}

private function validateFaces($selfieBytes, $documentBytes)
{
    foreach ([$selfieBytes, $documentBytes] as $img)
        if (!count($this->rekognition->detectFaces(['Image' => ['Bytes' => $img]])['FaceDetails'] ?? []))
            throw new \Exception('لم يتم اكتشاف وجه في إحدى الصور. تأكد أن الوجه ظاهر بوضوح.');
}

private function compareFaces($selfieBytes, $documentBytes)
{
    $result = $this->rekognition->compareFaces([
        'SimilarityThreshold' => 80,
        'SourceImage' => ['Bytes' => $selfieBytes],
        'TargetImage' => ['Bytes' => $documentBytes],
    ]);
    return $result['FaceMatches'][0]['Similarity'] ?? 0;
}

private function errorResponse($message, $status = 422)
{
    return response()->json([
        'success' => false,
        'verified' => false,
        'similarity' => 0,
        'message' => $message
    ], $status);
}



}
