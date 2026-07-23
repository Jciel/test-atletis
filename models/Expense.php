<?php

namespace app\models;

use app\behaviors\CastBehavior;
use app\models\cast\MoneyCast;
use Money\Money;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "expense".
 *
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $description
 * @property Money $amount
 * @property string $expense_date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ExpenseCategory $category
 * @property Users $user
 */
class Expense extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'expense';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'category_id', 'description', 'amount', 'expense_date'], 'required'],
            [['user_id', 'category_id'], 'integer'],
            [['currency'], 'string', 'length' => 3],
            [['expense_date', 'created_at', 'updated_at'], 'safe'],
            [['description'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExpenseCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'category_id' => 'Category ID',
            'description' => 'Description',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'expense_date' => 'Expense Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(ExpenseCategory::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function behaviors(): array
    {
        return [
            ['class' => TimestampBehavior::class, 'value' => new Expression('NOW()')],
            ['class' => CastBehavior::class, 'casts' => ['amount' => ['class' => MoneyCast::class, 'currencyAttribute' => 'currency']]],
        ];
    }
}
