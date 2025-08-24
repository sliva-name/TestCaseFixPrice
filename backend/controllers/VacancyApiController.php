<?php
declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\services\VacancyService;

/**
 * API контроллер для работы с вакансиями
 */
class VacancyApiController extends BaseApiController
{
    private VacancyService $vacancyService;

    private function getVacancyService(): VacancyService
    {
        if (!isset($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
        }
        return $this->vacancyService;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['POST'],
            ],
        ];
        
        return $behaviors;
    }

    /**
     * GET /api/vacancies - список вакансий
     */
    public function actionIndex(): array
    {
        $request = Yii::$app->request;
        $params = [
            'page' => $request->get('page'),
            'per_page' => $request->get('per_page'),
            'sort_by' => $request->get('sort_by'),
            'sort_order' => $request->get('sort_order'),
        ];
        
        $dataProvider = $this->getVacancyService()->getList($params);
        $models = $dataProvider->getModels();
        $pagination = $dataProvider->getPagination();

        $data = [
            'items' => array_map(
                fn($v) => $v->toArray(['title', 'salary', 'description']), 
                $models
            ),
            'pagination' => [
                'page' => $pagination->getPage() + 1,
                'per_page' => $pagination->getPageSize(),
                'total' => $dataProvider->getTotalCount(),
                'pages' => $pagination->getPageCount(),
            ],
            'sorting' => [
                'sort_by' => $request->get('sort_by', 'created_at'),
                'sort_order' => $request->get('sort_order', 'desc'),
            ],
        ];

        return $this->successResponse($data);
    }

    /**
     * GET /api/vacancy/{id} - одна вакансия
     */
    public function actionView(int $id): array
    {
        $vacancy = $this->getVacancyService()->findById($id);
        
        if (!$vacancy) {
            return $this->notFoundResponse('Вакансия не найдена');
        }
        
        $fields = Yii::$app->request->get('fields');
        
        if ($fields) {
            // Если указан параметр fields - возвращаем только указанные опциональные поля
            $requestedFields = $this->parseFields($fields);
            // Ограничиваем только опциональными полями: description, title
            $allowedOptionalFields = ['description', 'title'];
            $validFields = array_intersect($requestedFields, $allowedOptionalFields);
            
            if (!empty($validFields)) {
                // Формируем данные вручную для точного контроля
                $data = [];
                foreach ($validFields as $field) {
                    if ($field === 'title') {
                        $data['title'] = $vacancy->title;
                    } elseif ($field === 'description') {
                        $data['description'] = $vacancy->description;
                    }
                }
            } else {
                // Если запрошены недопустимые поля, возвращаем пустой результат
                $data = [];
            }
        } else {
            // Без параметра fields - возвращаем все обязательные поля
            $data = [
                'title' => $vacancy->title,
                'salary' => (float) $vacancy->salary,
                'description' => $vacancy->description,
            ];
        }
        
        return $this->successResponse($data);
    }

    /**
     * POST /api/vacancy - создание вакансии
     */
    public function actionCreate(): array
    {
        $data = Yii::$app->request->getBodyParams();
        
        if (empty($data)) {
            return $this->errorResponse([], 'Отсутствуют данные для создания вакансии', 400);
        }
        
        try {
            $vacancy = $this->getVacancyService()->create($data);
            
            // Устанавливаем Location header
            Yii::$app->response->headers->set('Location', '/api/vacancy/' . $vacancy->id);
            
            $responseData = [
                'id' => $vacancy->id,
                'success' => true,
            ];
            
            return $this->successResponse($responseData, 'Вакансия создана', 201);
            
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true) ?: ['general' => $e->getMessage()];
            return $this->validationErrorResponse($errors);
        }
    }

    /**
     * Парсит строку полей в массив
     */
    private function parseFields(string $fields): array
    {
        $requestedFields = array_map('trim', explode(',', $fields));
        return array_filter($requestedFields);
    }
}
