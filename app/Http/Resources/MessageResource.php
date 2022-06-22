<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'from' => UserResource::make($this->whenLoaded('from')),
            'to' => UserResource::make($this->whenLoaded('to')),
            'replies' => self::collection($this->whenLoaded('replies')),
            'replied' => self::make($this->whenLoaded('replied')),
            'group_id' => $this->group_id,
            'text' => $this->text,
            'media' => $this->media,
            'mediaFileName' => $this->mediaFileName,
            'time' => $this->time,
            'unread_msgs' => $this->unread_msgs,
            'seen' => $this->seen,
        ];
    }
}
