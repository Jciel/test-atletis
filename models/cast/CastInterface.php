<?php

namespace app\models\cast;

use yii\db\ActiveRecord;

interface CastInterface
{
    public function get(ActiveRecord $model, string $attribute, mixed $value): mixed;

    public function set(ActiveRecord $model, string $attribute, mixed $value): array;
}
