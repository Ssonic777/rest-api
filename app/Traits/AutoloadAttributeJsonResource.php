<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Str;

/**
 * This Trait for Autoloading loaded attributes at Model
 */
trait AutoloadAttributeJsonResource
{

    /**
     * @var Model $model
     */
    private Model $model;

    /**
     * @var array $result
     */
    private array $result = [];

    /**
     * @var array $modelAttributes
     */
    protected array $modifyAttributes = [];

    /**
     * @return array
     */
    protected function makeModifyAttributes(): array
    {
        return [
            // 'after' => 'before',
            // 'post_text' => 'postText',
            // 'post_privacy' => 'postPrivacy',
            // 'post_file' => 'postFile',
            // 'post_photos' => 'postPhoto',
            // 'post_video' => 'postVideo',
            // 'post_link' => 'postLink',
            // 'created_at' => 'time'
        ];
    }

    /**
     * @return array
     */
    protected function mergeAttributes(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function parse(): array
    {
        $this->model = $this->resource;
        $this->parseAttributes();
        $this->makeMerge();

        return $this->result;
    }

    private function parseAttributes(): void
    {
        $this->modifyAttributes = array_flip($this->makeModifyAttributes());

        foreach ($this->model->getAttributes() as $key => $val) {
            if (in_array($key, $this->model->getHidden())) {
                continue;
            }

            if (count($this->modifyAttributes) && array_key_exists($key, $this->modifyAttributes)) {
                $modifyKeyVal = $this->modifyAttributes[$key];
                $this->result[$modifyKeyVal] = $this->model->$key;
            } else {
                $this->result[$key] = $this->model->$key;
            }
        }
    }

    private function makeMerge(): void
    {
        $this->result = array_merge($this->result, $this->mergeAttributes());
    }
}
