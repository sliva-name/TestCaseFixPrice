<?php
declare(strict_types=1);

namespace app\services;

use app\models\Vacancy;
use yii\data\ActiveDataProvider;

/**
 * Сервис для работы с вакансиями
 */
class VacancyService
{
    private const DEFAULT_PAGE_SIZE = 10;
    private const MAX_PAGE_SIZE = 100;
    private const ALLOWED_SORT_FIELDS = ['id', 'title', 'salary', 'created_at'];

    /**
     * Получает список вакансий с пагинацией и сортировкой
     */
    public function getList(array $params = []): ActiveDataProvider
    {
        $query = Vacancy::find();

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => $this->buildPagination($params),
            'sort' => $this->buildSort($params),
        ]);
    }

    /**
     * Находит вакансию по ID
     */
    public function findById(int $id): ?Vacancy
    {
        return Vacancy::findOne($id);
    }

    /**
     * Создает новую вакансию
     */
    public function create(array $data): Vacancy
    {
        $vacancy = new Vacancy();
        $vacancy->load($data, '');

        if (!$vacancy->validate()) {
            throw new \InvalidArgumentException($this->formatValidationErrors($vacancy));
        }

        if (!$vacancy->save()) {
            throw new \RuntimeException('Не удалось сохранить вакансию');
        }

        return $vacancy;
    }

    /**
     * Строит конфигурацию пагинации
     */
    private function buildPagination(array $params): array
    {
        $page = max((int)($params['page'] ?? 1), 1);
        $perPage = max(1, min((int)($params['per_page'] ?? self::DEFAULT_PAGE_SIZE), self::MAX_PAGE_SIZE));

        return [
            'pageSize' => $perPage,
            'page' => $page - 1,
        ];
    }

    /**
     * Строит конфигурацию сортировки
     */
    private function buildSort(array $params): array
    {
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = strtolower($params['sort_order'] ?? 'desc');

        // Валидация параметров
        if (!in_array($sortBy, self::ALLOWED_SORT_FIELDS)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        return [
            'defaultOrder' => [$sortBy => $sortOrder === 'asc' ? SORT_ASC : SORT_DESC],
            'attributes' => self::ALLOWED_SORT_FIELDS,
        ];
    }

    /**
     * Форматирует ошибки валидации в JSON
     */
    private function formatValidationErrors(Vacancy $vacancy): string
    {
        $errors = [];
        foreach ($vacancy->getErrors() as $field => $messages) {
            $errors[$field] = implode(', ', $messages);
        }
        return json_encode($errors, JSON_UNESCAPED_UNICODE);
    }
}