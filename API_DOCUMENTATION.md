# Image to Prompt API Documentation

## Overview
This API endpoint allows authenticated users to upload an image and receive a detailed prompt that can be used to generate similar images with AI image generation tools.

## Endpoints

### Generate Prompt from Image
```
POST /api/v1/generate-prompt
```

### Get User Generations
```
GET /api/v1/generations
```

## Authentication
- **Required**: Bearer token authentication via Laravel Sanctum
- **Header**: `Authorization: Bearer {your_token}`

## Request Format
- **Content-Type**: `multipart/form-data`
- **Body**: Form data with image file

### Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `image` | File | Yes | Image file (jpeg, jpg, png, gif, webp) |
| | | | Max size: 10MB, Min size: 1KB |
| | | | Dimensions: 64x64 to 4096x4096 pixels |
| | | | Aspect ratio: 1:10 to 10:1 |

## Response Format

### Success Response (200) - Generate Prompt
```json
{
    "prompt": "A detailed description of the image that can be used for AI image generation..."
}
```

### Success Response (200) - Get Generations
```json
{
    "data": [
        {
            "id": 1,
            "image_url": "http://localhost:8000/storage/uploads/images/example_1234567890.jpg",
            "generated_prompt": "A beautiful landscape with mountains...",
            "original_filename": "example.jpg",
            "file_size": 1024000,
            "mime_type": "image/jpeg",
            "created_at": "2025-10-22T15:30:00.000000Z",
            "updated_at": "2025-10-22T15:30:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/v1/generations?page=1",
        "last": "http://localhost:8000/api/v1/generations?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 15,
        "to": 1,
        "total": 1
    }
}
```

### Error Response (422 - Validation Error)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "image": [
            "The image dimensions must be between 64x64 and 4096x4096 pixels."
        ]
    }
}
```

### Error Response (429 - Rate Limit)
```json
{
    "message": "Too Many Attempts.",
    "exception": "Illuminate\\Http\\Exceptions\\ThrottleRequestsException"
}
```

### Error Response (500 - Server Error)
```json
{
    "error": "Failed to generate prompt from image",
    "message": "Detailed error message"
}
```

## Example Usage

### cURL - Generate Prompt
```bash
curl -X POST \
  http://localhost:8000/api/v1/generate-prompt \
  -H 'Authorization: Bearer YOUR_TOKEN_HERE' \
  -F 'image=@/path/to/your/image.jpg'
```

### cURL - Get Generations
```bash
curl -X GET \
  http://localhost:8000/api/v1/generations \
  -H 'Authorization: Bearer YOUR_TOKEN_HERE' \
  -H 'Content-Type: application/json'
```

### JavaScript (Fetch) - Generate Prompt
```javascript
const formData = new FormData();
formData.append('image', fileInput.files[0]);

fetch('/api/v1/generate-prompt', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer YOUR_TOKEN_HERE'
    },
    body: formData
})
.then(response => response.json())
.then(data => console.log(data.prompt));
```

### JavaScript (Fetch) - Get Generations
```javascript
fetch('/api/v1/generations', {
    method: 'GET',
    headers: {
        'Authorization': 'Bearer YOUR_TOKEN_HERE',
        'Content-Type': 'application/json'
    }
})
.then(response => response.json())
.then(data => console.log(data.data)); // Array of generations
```

## Enhanced Validation Rules

The API includes comprehensive image validation:

### Basic Validation
- **File Type**: Only jpeg, jpg, png, gif, webp allowed
- **File Size**: 1KB minimum, 10MB maximum
- **Dimensions**: 64x64 to 4096x4096 pixels
- **Aspect Ratio**: Between 1:10 and 10:1 (prevents extremely distorted images)

### Security Validation
- **File Type Validation**: Validates file types and extensions
- **File Size Validation**: Enforces size limits
- **Dimension Validation**: Ensures reasonable image dimensions
- **Filename Sanitization**: Removes potentially dangerous characters

### Rate Limiting
- **Per User**: 5 requests per minute per authenticated user (handled at route level)
- **Automatic Cleanup**: Failed uploads are automatically cleaned up

## Setup Requirements

1. **Environment Variables**: Add your OpenAI API key to `.env`:
   ```
   OPENAI_API_KEY=your_openai_api_key_here
   ```

2. **Storage**: Ensure the storage link is created:
   ```bash
   php artisan storage:link
   ```

3. **Database**: Run migrations:
   ```bash
   php artisan migrate
   ```

## Features
- ✅ User authentication required
- ✅ Comprehensive image validation (type, size, dimensions, aspect ratio)
- ✅ Request-level validation with custom rules
- ✅ Rate limiting (5 requests per minute per user)
- ✅ Filename sanitization for security
- ✅ OpenAI GPT-4 Vision integration
- ✅ Database storage of generation history
- ✅ Retrieve user's generation history with pagination
- ✅ File cleanup on errors
- ✅ Detailed error handling

## Database Schema
The API stores generation records in the `image_generations` table with the following fields:
- `user_id`: Foreign key to users table
- `image_path`: Path to stored image
- `generated_prompt`: The AI-generated prompt
- `original_filename`: Original uploaded filename
- `file_size`: Size of uploaded file
- `mime_type`: MIME type of uploaded file
- `created_at` / `updated_at`: Timestamps

