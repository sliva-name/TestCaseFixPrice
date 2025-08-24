<?php
declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Модель вакансии
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property float $salary
 * @property int $created_at
 * @property int $updated_at
 */
class Vacancy extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'vacancy';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['title', 'description', 'salary'], 'required'],
            [['description'], 'string'],
            [['salary'], 'number', 'min' => 0],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Название',
            'description' => 'Описание',
            'salary' => 'Зарплата',
        ];
    }

    public function fields(): array
    {
        return [
            'title',
            'description', 
            'salary' => fn() => (float) $this->salary,
        ];
    }
}