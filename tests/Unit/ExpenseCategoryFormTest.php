<?php

namespace Unit;

use Codeception\Test\Unit;
use app\models\forms\ExpenseCategoryForm;

class ExpenseCategoryFormTest extends Unit
{
    public function testExpenseCategoryFormIsValidWithCorrectData(): void
    {
        $form = new ExpenseCategoryForm();
        $form->name = 'Food';
        $form->slug = 'food';

        $this->assertTrue($form->validate());
        $this->assertEmpty($form->errors);
    }

    public function testNameIsRequired(): void
    {
        $form = new ExpenseCategoryForm();
        $form->slug = 'food';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('name', $form->errors);
    }

    public function testNameCannotExceed255Characters(): void
    {
        $form = new ExpenseCategoryForm();
        $form->name = str_repeat('a', 256);

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('name', $form->errors);
    }

    public function testSlugIsOptional(): void
    {
        $form = new ExpenseCategoryForm();
        $form->name = 'Food';

        $this->assertTrue($form->validate());
        $this->assertEmpty($form->errors);
    }

    public function testSlugCannotExceed255Characters(): void
    {
        $form = new ExpenseCategoryForm();
        $form->name = 'Food';
        $form->slug = str_repeat('a', 256);

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('slug', $form->errors);
    }
}
