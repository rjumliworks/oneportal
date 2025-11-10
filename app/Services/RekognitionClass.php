<?php

namespace App\Services;

use Aws\Rekognition\RekognitionClient;

class RekognitionClass
{
    protected $client;

    public function __construct()
    {
        $this->client = new RekognitionClient([
            'region' => env('AWS_DEFAULT_REGION', 'ap-southeast-1'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function detectLabels($imagePath)
    {
        $imageBytes = file_get_contents($imagePath);

        $result = $this->client->detectLabels([
            'Image' => [
                'Bytes' => $imageBytes,
            ],
            'MaxLabels' => 10,
            'MinConfidence' => 70,
        ]);

        return $result->get('Labels');
    }

    public function detectText($imagePath)
    {
        $imageBytes = file_get_contents($imagePath);

        $result = $this->client->detectText([
            'Image' => [
                'Bytes' => $imageBytes,
            ],
        ]);

        return $result->get('TextDetections');
    }
}
