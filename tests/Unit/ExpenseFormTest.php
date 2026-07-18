<?php

namespace Unit;

use Codeception\Test\Unit;
use app\models\forms\ExpenseForm;

class ExpenseFormTest extends Unit
{
    public function testExpenseFormIsValidWithCorrectData(): void
    {
        $form = new ExpenseForm();
        $form->category_id = 1;
        $form->description = 'Lunch';
        $form->amount = 35.90;
        $form->expense_date = '2025-01-15';

        $this->assertTrue($form->validate());
        $this->assertEmpty($form->errors);
    }

    public function testCategoryIsRequired(): void
    {
        $form = new ExpenseForm();
        $form->description = 'Lunch';
        $form->amount = 35.90;
        $form->expense_date = '2025-01-15';

        $this->assertFalse($form->validate());

        $this->assertArrayHasKey('category_id', $form->errors);
    }

    public function testAmountIsRequired(): void
    {
        $form = new ExpenseForm();
        $form->category_id = 1;
        $form->description = 'Lunch';
        $form->expense_date = '2025-01-15';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('amount', $form->errors);
    }

    public function testAmountMustBeGreaterThanZero(): void
    {
        $form = new ExpenseForm();
        $form->category_id = 1;
        $form->description = 'Lunch';
        $form->amount = 0;
        $form->expense_date = '2025-01-15';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('amount', $form->errors);
    }

    public function testDescriptionCannotExceed255Characters(): void
    {
        $form = new ExpenseForm();
        $form->category_id = 1;
        $form->description = str_repeat('a', 256);
        $form->amount = 10;
        $form->expense_date = '2025-01-15';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('description', $form->errors);
    }

    public function testExpenseDateMustHaveValidFormat(): void
    {
        $form = new ExpenseForm();
        $form->category_id = 1;
        $form->description = 'Lunch';
        $form->amount = 10;
        $form->expense_date = '15/01/2025';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('expense_date', $form->errors);
    }
}
