<?php

namespace app\services;

use app\models\ExpenseCategory;
use app\models\forms\ExpenseCategoryForm;
use app\resources\ExpenseCategoryResource;
use yii\web\BadRequestHttpException;

class ExpenseCategoryService extends BaseService
{
    public function create(ExpenseCategoryForm $form): array
    {
        $category = new ExpenseCategory();

        $category->name = $form->name;

        if (!empty($form->slug)) {
            $category->slug = $form->slug;
        }

        $this->saveOrFail($category);

        return ExpenseCategoryResource::make($category);
    }

    public function findAll(): array
    {
        return ExpenseCategoryResource::collection(
            ExpenseCategory::find()->orderBy(['name' => SORT_ASC])->all()
        );
    }

    public function findById(int $id): array
    {
        $category = $this->findOrFail(ExpenseCategory::class, $id);

        return ExpenseCategoryResource::make($category);
    }

    public function update(int $id, ExpenseCategoryForm $form): array {
        $category = $this->findOrFail(ExpenseCategory::class, $id);

        $category->name = $form->name;

        if (!empty($form->slug)) {
            $category->slug = $form->slug;
        }

        $this->saveOrFail($category);

        return ExpenseCategoryResource::make($category);
    }

    public function delete(int $id): array {
        $category = $this->findOrFail(ExpenseCategory::class, $id);

        if ($category->getExpenses()->exists()) {
            throw new BadRequestHttpException('Não é possível excluir uma categoria que possui despesas.');
        }

        $this->deleteOrFail($category);

        return ExpenseCategoryResource::make($category);
    }
}
