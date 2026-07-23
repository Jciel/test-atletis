<?php

namespace app\services;

use app\models\forms\ExpenseSearchForm;
use Yii;
use app\models\Expense;
use app\models\ExpenseCategory;
use app\models\forms\ExpenseForm;
use app\resources\ExpenseResource;
use yii\data\ActiveDataProvider;

class ExpenseService extends BaseService
{
    public function create(ExpenseForm $form): array
    {
        $this->findOrFail(ExpenseCategory::class, $form->category_id);
        $expense = new Expense();

        $expense->user_id = Yii::$app->user->id;
        $expense->category_id = $form->category_id;
        $expense->description = $form->description;
        $expense->amount = MoneyFactory::fromDecimal($form->amount, $form->currency);
        $expense->expense_date = $form->expense_date;

        $this->saveOrFail($expense);

        return ExpenseResource::make($expense);
    }

    public function findById(int $id): array
    {
        $expense = $this->findOneOrFail(
            Expense::find()
                ->with('category')
                ->where(['id' => $id, 'user_id' => Yii::$app->user->id])
        );

        return ExpenseResource::make($expense);
    }

    public function update(int $id, ExpenseForm $form): array
    {
        $expense = $this->findOneOrFail(Expense::find()->where(['id' => $id, 'user_id' => Yii::$app->user->id]));

        $this->findOrFail(ExpenseCategory::class, $form->category_id);

        $expense->category_id = $form->category_id;
        $expense->description = $form->description;
        $expense->amount = MoneyFactory::fromDecimal($form->amount, $form->currency);
        $expense->expense_date = $form->expense_date;

        $this->saveOrFail($expense);

        return ExpenseResource::make($expense);
    }

    public function delete(int $id): void
    {
        $expense = $this->findOneOrFail(
            Expense::find()->where(['id' => $id, 'user_id' => Yii::$app->user->id])
        );

        $this->deleteOrFail($expense);
    }

    public function findAll(ExpenseSearchForm $form): array
    {
        $query = Expense::find()
            ->with('category')
            ->where(['user_id' => Yii::$app->user->id]);

        if ($form->hasCategory()) {
            $query->andWhere(['category_id' => $form->category_id]);
        }

        if ($form->hasPeriod()) {
            $query
                ->andWhere('YEAR(expense_date) = :year', [':year' => $form->year])
                ->andWhere('MONTH(expense_date) = :month', [':month' => $form->month]);
        }

        $query->orderBy(['expense_date' => $form->sort === 'asc' ? SORT_ASC : SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => $form->page - 1,
                'pageSize' => $form->per_page,
            ],
        ]);

        return [
            'items' => ExpenseResource::collection($dataProvider->getModels()),
            'pagination' => [
                'page' => $form->page,
                'per_page' => $form->per_page,
                'total' => $dataProvider->getTotalCount(),
                'pages' => $dataProvider->getPagination()->getPageCount(),
            ],
        ];
    }
}
