<?php

namespace app\behaviors;

use app\models\cast\MoneyCast;
use app\casts\CastInterface;
use yii\base\Behavior;
use yii\db\ActiveRecord;


class CastBehavior extends Behavior
{
    public array $casts = [];
    private array $castsInstances = [];

    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterInsert',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
        ];
    }

    public function afterInsert(): void
    {
        foreach ($this->casts as $attribute => $config) {
            $cast = $this->cast($config);
            $this->owner->$attribute = $cast->get($this->owner, $attribute, $this->owner->getOldAttribute($attribute));
        }
    }

    public function afterFind(): void
    {
        foreach ($this->casts as $attribute => $config) {
            $cast = $this->cast($config);
            $this->owner->$attribute = $cast->get($this->owner, $attribute, $this->owner->getOldAttribute($attribute));
        }
    }

    public function beforeSave(): void
    {
        foreach ($this->casts as $attribute => $config) {
            $cast = $this->cast($config);
            $value = $this->owner->$attribute;

            if (!$value) {
                continue;
            }

            foreach ($cast->set($this->owner, $attribute, $value) as $column => $columnValue) {
                $this->owner->$column = $columnValue;
            }
        }
    }

    private function cast(array $config): \app\models\cast\CastInterface
    {
        $key = md5(serialize($config));
        if (!isset($this->castsInstances[$key])) {
            $class = $config['class'];
            unset($config['class']);
            $this->castsInstances[$key] = new $class(...array_values($config));
        }
        return $this->castsInstances[$key];
    }
}
