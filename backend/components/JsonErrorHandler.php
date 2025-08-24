<?php
declare(strict_types=1);

namespace app\components;

use Yii;
use yii\web\ErrorHandler;
use yii\web\Response;

/**
 * Обработчик ошибок для API, возвращающий JSON
 */
class JsonErrorHandler extends ErrorHandler
{
    protected function renderException($exception)
    {
        if (!Yii::$app->has('response')) {
            return parent::renderException($exception);
        }

        $response = Yii::$app->getResponse();
        $response->clear();
        $response->format = Response::FORMAT_JSON;

        $statusCode = property_exists($exception, 'statusCode') ? $exception->statusCode : 500;
        $response->setStatusCode($statusCode);

        $errorData = [
            'success' => false,
            'error' => [
                'code' => $statusCode,
                'message' => $exception->getMessage() ?: 'Внутренняя ошибка сервера',
            ],
        ];

        // Добавляем детали валидации для InvalidArgumentException
        if ($exception instanceof \InvalidArgumentException) {
            $decoded = json_decode($exception->getMessage(), true);
            if (is_array($decoded)) {
                $errorData['errors'] = $decoded;
                $errorData['error']['message'] = 'Ошибка валидации';
            }
        }

        // Отладочная информация в dev режиме
        if (defined('YII_DEBUG') && YII_DEBUG) {
            $errorData['error']['debug'] = [
                'type' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        $response->data = $errorData;
        return $response;
    }
}