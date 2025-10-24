<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use OpenAI\Client;
use OpenAI\Factory;

class OpenAIService
{
    /**
     * Generate a detailed prompt from an image using OpenAI's vision model.
     *
     * @param UploadedFile $image
     * @return string
     * @throws \Exception
     */
    public function generatePromptFromImage(UploadedFile $image): string
    {
        try {
            // Convert image to base64
            $imageData = base64_encode(file_get_contents($image->getPathname()));
            $mimeType = $image->getMimeType();

            $client = (new Factory())->withApiKey(config('services.openai.api_key'))->make();
            $response = $client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analyze this image and generate a detailed, descriptive prompt that could be used to recreate a similar image with AI image generation tools. The prompt should be comprehensive, describing the visual elements, style, composition, lighting, colors, and any other relevant details. Make it detailed enough that someone could use it to generate a similar image.',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$imageData}",
                                ],
                            ],
                        ],
                    ],
                ],
                'max_tokens' => 1000,
            ]);

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            throw new \Exception('Failed to generate prompt from image: ' . $e->getMessage());
        }
    }
}
