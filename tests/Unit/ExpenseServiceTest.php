<?php

namespace Unit;

use app\models\Expense;
use app\models\forms\ExpenseSearchForm;
use app\models\User;
use app\services\ExpenseService;
use app\services\MoneyFactory;
use Codeception\Test\Unit;
use Yii;
use app\models\ExpenseCategory;
use app\models\forms\ExpenseForm;
use yii\web\NotFoundHttpException;

class ExpenseServiceTest extends Unit
{
    protected ExpenseService $service;

    protected function _before(): void
    {
        $this->service = Yii::$container->get(ExpenseService::class);

        Expense::deleteAll();
        ExpenseCategory::deleteAll();
        User::deleteAll();

        $user = new User();

        $user->username = 'john';
        $user->email = 'john@email.com';
        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->save(false);

        Yii::$app->user->setIdentity($user);
    }

    public function testCreateExpenseSuccessfully(): void
    {
        $category = new ExpenseCategory();
        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $form = new ExpenseForm();
        $form->category_id = $category->id;
        $form->description = 'Lunch';
        $form->amount = '120.90';
        $form->expense_date = '2025-01-15';

        $result = $this->service->create($form);

        $this->assertIsArray($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('amount', $result);

        $this->assertEquals('Lunch', $result['description']);
        $this->assertEquals(120.90, $result['amount']);

        $expense = Expense::findOne($result['id']);

        $this->assertNotNull($expense);
        $this->assertEquals(Yii::$app->user->id, $expense->user_id);
    }

    public function testCannotCreateExpenseWithInvalidCategory(): void
    {
        $form = new ExpenseForm();
        $form->category_id = 99999;
        $form->description = 'Invalid expense';
        $form->amount = '100';
        $form->expense_date = '2025-01-15';

        $this->expectException(NotFoundHttpException::class);
        $this->service->create($form);
    }

    public function testFindExpenseById(): void
    {
        $category = new ExpenseCategory();
        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $expense = new Expense();
        $expense->user_id = Yii::$app->user->id;
        $expense->category_id = $category->id;
        $expense->description = 'Supermarket';
        $expense->amount = MoneyFactory::fromDecimal('250.75');
        $expense->expense_date = '2025-01-15';
        $expense->save(false);

        $result = $this->service->findById($expense->id);

        $this->assertIsArray($result);
        $this->assertEquals($expense->id, $result['id']);
        $this->assertEquals('Supermarket', $result['description']);
        $this->assertEquals(250.75, $result['amount']);
    }

    public function testCannotFindExpenseFromAnotherUser(): void
    {
        $category = new ExpenseCategory();
        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $anotherUser = new User();
        $anotherUser->username = 'mary';
        $anotherUser->email = 'mary@email.com';
        $anotherUser->setPassword('123456');
        $anotherUser->generateAuthKey();
        $anotherUser->save(false);


        $expense = new Expense();
        $expense->user_id = $anotherUser->id;
        $expense->category_id = $category->id;
        $expense->description = 'Private expense';
        $expense->amount = MoneyFactory::fromDecimal('100');
        $expense->expense_date = '2025-01-15';
        $expense->save(false);

        $this->expectException(NotFoundHttpException::class);
        $this->service->findById($expense->id);
    }

    public function testUpdateExpense(): void
    {
        $category = new ExpenseCategory();
        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $newCategory = new ExpenseCategory();
        $newCategory->name = 'Transport';
        $newCategory->slug = 'transport';
        $newCategory->save(false);

        $expense = new Expense();
        $expense->user_id = Yii::$app->user->id;
        $expense->category_id = $category->id;
        $expense->description = 'Lunch';
        $expense->amount = MoneyFactory::fromDecimal('39.90');
        $expense->expense_date = '2025-01-15';
        $expense->save(false);

        $form = new ExpenseForm();
        $form->category_id = $newCategory->id;
        $form->description = 'Fuel';
        $form->amount = 200.00;
        $form->expense_date = '2025-02-01';

        $result = $this->service->update($expense->id, $form);

        $this->assertIsArray($result);
        $this->assertEquals('Fuel', $result['description']);
        $this->assertEquals('200.00', $result['amount']);
        $expense->refresh();
        $this->assertEquals($newCategory->id, $expense->category_id);
        $this->assertEquals('Fuel', $expense->description);
        $this->assertEquals(20000, $expense->amount->getAmount());
    }

    public function testCannotUpdateExpenseFromAnotherUser(): void
    {
        $category = new ExpenseCategory();
        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $anotherUser = new User();
        $anotherUser->username = 'mary';
        $anotherUser->email = 'mary@email.com';
        $anotherUser->setPassword('123456');
        $anotherUser->generateAuthKey();
        $anotherUser->save(false);

        $expense = new Expense();
        $expense->user_id = $anotherUser->id;
        $expense->category_id = $category->id;
        $expense->description = 'Private expense';
        $expense->amount = MoneyFactory::fromDecimal('100.00');
        $expense->expense_date = '2025-01-15';
        $expense->save(false);

        $form = new ExpenseForm();
        $form->category_id = $category->id;
        $form->description = 'Attempted modification';
        $form->amount = 999;
        $form->expense_date = '2025-02-01';

        $this->expectException(NotFoundHttpException::class);
        $this->service->update($expense->id, $form);
    }

    public function testDeleteExpense(): void
    {
        $category = new ExpenseCategory();
        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $expense = new Expense();
        $expense->user_id = Yii::$app->user->id;
        $expense->category_id = $category->id;
        $expense->description = 'Temporary expense';
        $expense->amount = MoneyFactory::fromDecimal('50.00');
        $expense->expense_date = '2025-01-15';
        $expense->save(false);

        $this->service->delete($expense->id);
        $this->assertNull(Expense::findOne($expense->id));
    }

    public function testCannotDeleteExpenseFromAnotherUser(): void
    {
        $category = new ExpenseCategory();
        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $anotherUser = new User();
        $anotherUser->username = 'mary';
        $anotherUser->email = 'mary@email.com';
        $anotherUser->setPassword('123456');
        $anotherUser->generateAuthKey();
        $anotherUser->save(false);

        $expense = new Expense();
        $expense->user_id = $anotherUser->id;
        $expense->category_id = $category->id;
        $expense->description = 'Private expense';
        $expense->amount = MoneyFactory::fromDecimal('80.00');
        $expense->expense_date = '2025-01-15';
        $expense->save(false);

        $this->expectException(NotFoundHttpException::class);
        $this->service->delete($expense->id);
        $this->assertNotNull(Expense::findOne($expense->id));
    }

    public function testFindAllExpenses(): void
    {
        $category = new ExpenseCategory();
        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $expense = new Expense();
        $expense->user_id = Yii::$app->user->id;
        $expense->category_id = $category->id;
        $expense->description = 'Supermarket';
        $expense->amount = MoneyFactory::fromDecimal('200.00');
        $expense->expense_date = '2025-01-15';
        $expense->save(false);

        $anotherUser = new User();
        $anotherUser->username = 'mary';
        $anotherUser->email = 'mary@email.com';
        $anotherUser->setPassword('123456');
        $anotherUser->generateAuthKey();
        $anotherUser->save(false);

        $otherExpense = new Expense();
        $otherExpense->user_id = $anotherUser->id;
        $otherExpense->category_id = $category->id;
        $otherExpense->description = 'Private expense';
        $otherExpense->amount = MoneyFactory::fromDecimal('500.00');
        $otherExpense->expense_date = '2025-01-20';
        $otherExpense->save(false);

        $form = new ExpenseSearchForm();
        $form->page = 1;
        $form->per_page = 10;
        $form->sort = 'desc';

        $result = $this->service->findAll($form);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(1, $result['items']);
        $this->assertEquals('Supermarket', $result['items'][0]['description']);
        $this->assertEquals(1, $result['pagination']['total']);
    }

    public function testFindAllExpensesByCategory(): void
    {
        $foodCategory = new ExpenseCategory();
        $foodCategory->name = 'Food';
        $foodCategory->slug = 'food';
        $foodCategory->save(false);

        $transportCategory = new ExpenseCategory();
        $transportCategory->name = 'Transport';
        $transportCategory->slug = 'transport';
        $transportCategory->save(false);

        $expense1 = new Expense();
        $expense1->user_id = Yii::$app->user->id;
        $expense1->category_id = $foodCategory->id;
        $expense1->description = 'Lunch';
        $expense1->amount = MoneyFactory::fromDecimal('50.00');
        $expense1->expense_date = '2025-01-15';
        $expense1->save(false);

        $expense2 = new Expense();
        $expense2->user_id = Yii::$app->user->id;
        $expense2->category_id = $transportCategory->id;
        $expense2->description = 'Fuel';
        $expense2->amount = MoneyFactory::fromDecimal('200.00');
        $expense2->expense_date = '2025-01-16';
        $expense2->save(false);

        $form = new ExpenseSearchForm();
        $form->category_id = $foodCategory->id;
        $form->page = 1;
        $form->per_page = 10;
        $form->sort = 'desc';

        $result = $this->service->findAll($form);

        $this->assertCount(1, $result['items']);
        $this->assertEquals('Lunch', $result['items'][0]['description']);
        $this->assertEquals(1, $result['pagination']['total']);
    }

    public function testFindAllExpensesByPeriod(): void
    {
        $category = new ExpenseCategory();
        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $expenseJanuary = new Expense();
        $expenseJanuary->user_id = Yii::$app->user->id;
        $expenseJanuary->category_id = $category->id;
        $expenseJanuary->description = 'January expense';
        $expenseJanuary->amount = MoneyFactory::fromDecimal('100.00');
        $expenseJanuary->expense_date = '2025-01-15';
        $expenseJanuary->save(false);

        $expenseFebruary = new Expense();
        $expenseFebruary->user_id = Yii::$app->user->id;
        $expenseFebruary->category_id = $category->id;
        $expenseFebruary->description = 'February expense';
        $expenseFebruary->amount = MoneyFactory::fromDecimal('200.00');
        $expenseFebruary->expense_date = '2025-02-15';
        $expenseFebruary->save(false);

        $form = new ExpenseSearchForm();
        $form->year = 2025;
        $form->month = 1;
        $form->page = 1;
        $form->per_page = 10;
        $form->sort = 'desc';

        $result = $this->service->findAll($form);

        $this->assertCount(1, $result['items']);
        $this->assertEquals('January expense', $result['items'][0]['description']);
        $this->assertEquals(1, $result['pagination']['total']);
    }

    public function testFindAllExpensesSortAndPagination(): void
    {
        $category = new ExpenseCategory();
        $category->name = 'Food';
        $category->slug = 'food';
        $category->save(false);

        $expenses = [
            ['description' => 'March expense', 'expense_date' => '2025-03-15', 'amount' => '300.00'],
            ['description' => 'January expense', 'expense_date' => '2025-01-15', 'amount' => '100.00'],
            ['description' => 'February expense', 'expense_date' => '2025-02-15', 'amount' => '200.00'],
        ];

        foreach ($expenses as $data) {
            $expense = new Expense();
            $expense->user_id = Yii::$app->user->id;
            $expense->category_id = $category->id;
            $expense->description = $data['description'];
            $expense->amount = MoneyFactory::fromDecimal($data['amount']);
            $expense->expense_date = $data['expense_date'];
            $expense->save(false);
        }

        $form = new ExpenseSearchForm();
        $form->page = 1;
        $form->per_page = 2;
        $form->sort = 'asc';

        $result = $this->service->findAll($form);

        $this->assertCount(2, $result['items']);
        $this->assertEquals('January expense', $result['items'][0]['description']);
        $this->assertEquals('February expense', $result['items'][1]['description']);
        $this->assertEquals(3, $result['pagination']['total']);
        $this->assertEquals(2, $result['pagination']['per_page']);
        $this->assertEquals(2, $result['pagination']['pages']);
    }
}
