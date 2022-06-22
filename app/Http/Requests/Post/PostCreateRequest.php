<?php

namespace App\Http\Requests\Post;

use App\Models\Post;
use App\Rules\FileExistsRule;

class PostCreateRequest extends PostBaseRequest
{
    /**
     * @return string
     */
    protected function getMessage(): string
    {
        return 'Can’t create empty post.';
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'post_text' => 'required_without_all:service_gif,attachments|string|max:10|min:3',
            'post_privacy' => 'required|in:' . implode(',', Post::$privacyOptions),
            'comments_status' => 'required|boolean',
            'attachments' => ['required_without_all:service_gif,post_text', 'array', new FileExistsRule(Post::ATTACHMENT_MIMETYPES)],
            'recipient_id' => 'integer|exists:Wo_Users,user_id',
            'enable_notifications' => 'boolean',
            'service_gif' => 'required_without_all:post_text,attachments|string|url',
        ];
    }
}
