<?php

use yii\db\Migration;

/**
 * Class m240101_000001_create_vacancy_table
 */
class m240101_000001_create_vacancy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vacancy}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->comment('Название вакансии'),
            'description' => $this->text()->notNull()->comment('Описание вакансии'),
            'salary' => $this->decimal(10, 2)->notNull()->comment('Зарплата'),
            'created_at' => $this->integer()->notNull()->comment('Дата создания'),
            'updated_at' => $this->integer()->notNull()->comment('Дата обновления'),
        ]);

        // Создаем индексы для оптимизации запросов
        $this->createIndex('idx_vacancy_created_at', '{{%vacancy}}', 'created_at');
        $this->createIndex('idx_vacancy_salary', '{{%vacancy}}', 'salary');
        $this->createIndex('idx_vacancy_title', '{{%vacancy}}', 'title');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%vacancy}}');
    }
}
