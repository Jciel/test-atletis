<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Inflector;

/**
 * This is the model class for table "expense_category".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Expense[] $expenses
 */
class ExpenseCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'expense_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'slug'], 'required'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['slug', 'name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Expenses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpenses(): ActiveQuery
    {
        return $this->hasMany(Expense::class, ['category_id' => 'id']);
    }

    public function behaviors(): array
    {
        return [['class' => TimestampBehavior::class, 'value' => new Expression('NOW()')]];
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            if (empty($this->slug)) {
                $this->slug = Inflector::slug($this->name);
            }
            return true;
        }
        return false;
    }
}
