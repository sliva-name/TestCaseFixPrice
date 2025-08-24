<?php
declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;

/**
 * Базовый контроллер для всех API endpoints
 */
abstract class BaseApiController extends Controller
{
    /**
     * Стандартные поведения для всех API контроллеров
     */
    public function behaviors(): array
    {
        return [
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => false,
                    'Access-Control-Max-Age' => 86400,
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => ['application/json' => Response::FORMAT_JSON],
            ],
        ];
    }

    /**
     * Централизованная обработка ответов
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        
        // Если уже есть envelope с success, возвращаем как есть
        if (is_array($result) && isset($result['success'])) {
            return $result;
        }
        
        // Оборачиваем успешный результат
        return $this->successResponse($result);
    }

    /**
     * Стандартный успешный ответ
     */
    protected function successResponse($data = null, string $message = null, int $statusCode = 200): array
    {
        Yii::$app->response->statusCode = $statusCode;
        
        $response = ['success' => true];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        return $response;
    }

    /**
     * Стандартный ответ с ошибкой
     */
    protected function errorResponse($errors, string $message = 'Ошибка', int $statusCode = 400): array
    {
        Yii::$app->response->statusCode = $statusCode;
        
        $response = [
            'success' => false,
            'error' => [
                'code' => $statusCode,
                'message' => $message,
            ],
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        
        return $response;
    }

    /**
     * Ответ для несуществующего ресурса
     */
    protected function notFoundResponse(string $message = 'Ресурс не найден'): array
    {
        return $this->errorResponse([], $message, 404);
    }

    /**
     * Ответ для ошибок валидации
     */
    protected function validationErrorResponse($errors, string $message = 'Ошибка валидации'): array
    {
        return $this->errorResponse($errors, $message, 422);
    }
}
