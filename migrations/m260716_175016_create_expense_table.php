<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%expense}}`.
 */
class m260716_175016_create_expense_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%expense}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
            'description' => $this->string()->notNull(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'expense_date' => $this->timestamp()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);

        $this->createIndex('idx-expense-user_id', '{{%expense}}', 'user_id');
        $this->createIndex('idx-expense-category_id', '{{%expense}}', 'category_id');

        $this->addForeignKey(
            'fk-expense-user_id',
            '{{%expense}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-expense-category_id',
            '{{%expense}}',
            'category_id',
            '{{%expense_category}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-expense-category_id', '{{%expense}}');
        $this->dropForeignKey('fk-expense-user_id', '{{%expense}}');
        $this->dropIndex('idx-expense-category_id', '{{%expense}}');
        $this->dropIndex('idx-expense-user_id', '{{%expense}}');

        $this->dropTable('{{%expense}}');
    }
}

