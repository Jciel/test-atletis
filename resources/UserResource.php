<?php

namespace app\resources;

use app\models\ExpenseCategory;

class UserResource extends BaseResource
{
    public static function make(object $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
    }
}
