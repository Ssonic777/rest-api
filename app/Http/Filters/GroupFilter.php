<?php

declare(strict_types=1);

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class GroupFilter
 * @package App\Http\Filters
 */
class GroupFilter extends Filter
{
    /**
     * Filter the groups by the given name.
     *
     * @param  string|null  $value
     * @return Builder
     */
    public function name(string $value = null): Builder
    {
        return $this->builder->where(function ($query) use ($value) {
            $query->where('group_name', 'LIKE', "%$value%")
                  ->orWhere('group_title', 'LIKE', "%$value%");
        })->where('active', '1');
    }

    /**
     * @param string|null $value
     * @return Builder
     */
    public function tab(string $value = null): Builder
    {
        switch ($value) {
            case 'suggested':
                return $this->suggestedTab();
            case 'joined':
                return $this->joinedTab();
            case 'my':
                return $this->myTab();
        }

        return $this->builder;
    }

    /**
     * @return Builder
     */
    protected function suggestedTab(): Builder
    {
        return $this->builder->where(function ($query) {
            $query->whereHas('members', function ($query) {
                $query->where('Wo_Group_Members.user_id', '!=', auth('api')->user()->getAuthIdentifier());
            })->orDoesntHave('members');
        })->where('user_id', '!=', auth('api')->user()->getAuthIdentifier());
    }

    /**
     * @return Builder
     */
    protected function joinedTab(): Builder
    {
        return $this->builder->wherehas('members', function ($query) {
            $query->where('Wo_Group_Members.user_id', auth('api')->user()->getAuthIdentifier());
        })->where('user_id', '!=', auth('api')->user()->getAuthIdentifier());
    }

    /**
     * @return Builder
     */
    protected function myTab(): Builder
    {
        return $this->builder
            ->where('user_id', auth('api')->user()->getAuthIdentifier())
            ->orWhereHas('admins', function ($query) {
                $query->where('Wo_GroupAdmins.user_id', auth('api')->user()->getAuthIdentifier());
            });
    }
}
