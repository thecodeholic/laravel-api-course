<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneratePromptRequest;
use App\Models\ImageGeneration;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageGenerationController extends Controller
{
    public function __construct(private OpenAIService $openAIService)
    {
        # code...
    }

    public function index()
    {

    }

    public function generatePrompt(GeneratePromptRequest $request)
    {
        try {
            $user = $request->user();
            $image = $request->file('image');

            $originalName = $image->getClientOriginalName();
            $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));

            $extension = $image->getClientOriginalExtension();
            $safeFilename = $sanitizedName . '_' . time() . '.' . $extension;

            $imagePath = $image->storeAs('uploads/images', $safeFilename, 'public');

            $generatedPrompt = $this->openAIService->generatePromptFromImage($image);

            $imageGeneration = ImageGeneration::create([
                'user_id' => $user->id,
                'image_path' => $imagePath,
                'generated_prompt' => $generatedPrompt,
                'original_filename' => $originalName,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
            ]);

            return response()->json([
                'id' => $imageGeneration->id,
                'prompt' => $generatedPrompt
            ]);
        } catch (\Exception $e) {
            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return response()->json([
                'error' => 'Failed to generate prompt from image',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
