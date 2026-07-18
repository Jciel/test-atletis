<?php

namespace Unit;

use app\models\Expense;
use app\models\User;
use Yii;
use app\models\ExpenseCategory;
use app\models\forms\ExpenseCategoryForm;
use app\services\ExpenseCategoryService;
use Codeception\Test\Unit;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class ExpenseCategoryServiceTest extends Unit
{
    protected ExpenseCategoryService $service;

    protected function _before(): void
    {
        $this->service = Yii::$container->get(ExpenseCategoryService::class);

        ExpenseCategory::deleteAll();

        $user = new User();

        $user->username = 'john';
        $user->email = 'john@email.com';
        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->save(false);

        Yii::$app->user->setIdentity($user);
    }

    public function testCreateCategorySuccessfully(): void
    {
        $form = new ExpenseCategoryForm();

        $form->name = 'Food';

        $result = $this->service->create($form);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('slug', $result);
        $this->assertEquals('Food', $result['name']);

        $category = ExpenseCategory::findOne($result['id']);

        $this->assertNotNull($category);
        $this->assertEquals('Food', $category->name);
    }

    public function testFindAllCategories(): void
    {
        $category1 = new ExpenseCategory();
        $category1->name = 'Food';
        $category1->slug = 'food';
        $category1->save(false);

        $category2 = new ExpenseCategory();
        $category2->name = 'Transport';
        $category2->slug = 'transport';
        $category2->save(false);

        $result = $this->service->findAll();

        $this->assertCount(2, $result);
        $this->assertEquals('Food', $result[0]['name']);
        $this->assertEquals('Transport', $result[1]['name']);
    }

    public function testFindCategoryById(): void
    {
        $category = new ExpenseCategory();

        $category->name = 'Leisure';
        $category->slug = 'leisure';
        $category->save(false);

        $result = $this->service->findById($category->id);

        $this->assertEquals($category->id, $result['id']);
        $this->assertEquals('Leisure', $result['name']);
    }

    public function testCategoryNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->service->findById(99999);
    }

    public function testUpdateCategory(): void
    {
        $category = new ExpenseCategory();

        $category->name = 'Leisure';
        $category->slug = 'leisure';
        $category->save(false);

        $form = new ExpenseCategoryForm();
        $form->name = 'Travel';

        $result = $this->service->update($category->id, $form);

        $this->assertEquals('Travel', $result['name']);
        $category->refresh();
        $this->assertEquals('Travel', $category->name);
    }

    public function testDeleteCategory(): void
    {
        $category = new ExpenseCategory();

        $category->name = 'Temporary';
        $category->slug = 'temporary';
        $category->save(false);

        $result = $this->service->delete($category->id);

        $this->assertEquals($category->id, $result['id']);
        $this->assertNull(ExpenseCategory::findOne($category->id));
    }

    public function testCannotDeleteCategoryWithExpenses(): void
    {
        $category = new ExpenseCategory();

        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $expense = new Expense();

        $expense->description = 'Lunch';
        $expense->amount = 35.90;
        $expense->expense_date = '2025-01-15';
        $expense->category_id = $category->id;
        $expense->user_id = Yii::$app->user->id;
        $expense->save(false);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Não é possível excluir uma categoria que possui despesas.');

        $this->service->delete($category->id);
    }
}
