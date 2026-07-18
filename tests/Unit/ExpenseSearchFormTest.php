<?php

namespace Unit;

use Codeception\Test\Unit;
use app\models\forms\ExpenseSearchForm;

class ExpenseSearchFormTest extends Unit
{
    public function testSearchFormIsValidWithDefaultValues(): void
    {
        $form = new ExpenseSearchForm();

        $this->assertTrue($form->validate());
        $this->assertEmpty($form->errors);
    }

    public function testCategoryIsOptional(): void
    {
        $form = new ExpenseSearchForm();
        $form->validate();

        $this->assertFalse($form->hasCategory());
    }

    public function testHasCategoryReturnsTrueWhenCategoryIsDefined(): void
    {
        $form = new ExpenseSearchForm();
        $form->category_id = 10;

        $this->assertTrue($form->hasCategory());
    }

    public function testHasPeriodReturnsFalseWithoutMonthAndYear(): void
    {
        $form = new ExpenseSearchForm();

        $this->assertFalse($form->hasPeriod());
    }

    public function testHasPeriodReturnsTrueWithMonthAndYear(): void
    {
        $form = new ExpenseSearchForm();
        $form->month = 7;
        $form->year = 2025;

        $this->assertTrue($form->hasPeriod());
    }

    public function testMonthMustBeBetween1And12(): void
    {
        $form = new ExpenseSearchForm();
        $form->month = 13;

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('month', $form->errors);
    }

    public function testYearMustBeBetween2000And2100(): void
    {
        $form = new ExpenseSearchForm();
        $form->year = 1999;

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('year', $form->errors);
    }

    public function testSortMustBeAscOrDesc(): void
    {
        $form = new ExpenseSearchForm();
        $form->sort = 'invalid';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('sort', $form->errors);
    }

    public function testPageMustBeGreaterThanZero(): void
    {
        $form = new ExpenseSearchForm();
        $form->page = 0;

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('page', $form->errors);
    }

    public function testPerPageCannotExceed100(): void
    {
        $form = new ExpenseSearchForm();
        $form->per_page = 101;

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('per_page', $form->errors);
    }
}
