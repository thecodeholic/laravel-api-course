<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneratePromptRequest;
use App\Http\Resources\ImageGenerationResource;
use App\Models\ImageGeneration;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageGenerationController extends Controller
{
    public function __construct(
        private OpenAIService $openAIService
    ) {}

    /**
     * Get all image generations for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $generations = $user->imageGenerations()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ImageGenerationResource::collection($generations)->response();
    }

    /**
     * Generate a prompt from an uploaded image.
     */
    public function generatePrompt(GeneratePromptRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $image = $request->file('image');

            // Sanitize filename for security
            $originalName = $image->getClientOriginalName();
            $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $image->getClientOriginalExtension();
            $safeFilename = $sanitizedName . '_' . time() . '.' . $extension;

            // Store the image with sanitized filename
            $imagePath = $image->storeAs('uploads/images', $safeFilename, 'public');

            // Generate prompt using OpenAI
            $generatedPrompt = $this->openAIService->generatePromptFromImage($image);

            // Save to database
            $imageGeneration = ImageGeneration::create([
                'user_id' => $user->id,
                'image_path' => $imagePath,
                'generated_prompt' => $generatedPrompt,
                'original_filename' => $originalName,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
            ]);

            return response()->json(new ImageGenerationResource($imageGeneration), 201);

        } catch (\Exception $e) {
            // Clean up uploaded file if generation fails
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
