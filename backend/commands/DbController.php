<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use Yii;

/**
 * Контроллер для работы с базой данных
 */
class DbController extends Controller
{
    /**
     * Проверить подключение к базе данных
     * 
     * @return int
     */
    public function actionTest()
    {
        $this->stdout("Проверка подключения к базе данных...\n");
        
        try {
            $db = Yii::$app->db;
            $connection = $db->getIsActive() ? 'активно' : 'неактивно';
            $this->stdout("Статус подключения: {$connection}\n");
            
            // Принудительно активируем подключение
            if (!$db->getIsActive()) {
                $db->open();
            }
            
            if ($db->getIsActive()) {
                $version = $db->createCommand('SELECT VERSION()')->queryScalar();
                $this->stdout("Версия MySQL: {$version}\n");
                
                $databases = $db->createCommand('SHOW DATABASES')->queryColumn();
                $this->stdout("Доступные базы данных:\n");
                foreach ($databases as $database) {
                    $this->stdout("  - {$database}\n");
                }
                
                $this->stdout("✅ Подключение к базе данных успешно!\n");
                return ExitCode::OK;
            } else {
                $this->stderr("❌ Подключение к базе данных неактивно\n");
                return ExitCode::UNSPECIFIED_ERROR;
            }
        } catch (\Exception $e) {
            $this->stderr("❌ Ошибка подключения к базе данных: {$e->getMessage()}\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Создать базу данных если не существует
     * 
     * @return int
     */
    public function actionCreate()
    {
        $this->stdout("Создание базы данных...\n");
        
        try {
            $db = Yii::$app->db;
            
            // Создаем основную базу данных
            $db->createCommand('CREATE DATABASE IF NOT EXISTS yii2app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->execute();
            $this->stdout("✅ База данных 'yii2app' создана или уже существует\n");
            
            // Создаем тестовую базу данных
            $db->createCommand('CREATE DATABASE IF NOT EXISTS yii2app_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->execute();
            $this->stdout("✅ База данных 'yii2app_test' создана или уже существует\n");
            
            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("❌ Ошибка создания базы данных: {$e->getMessage()}\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Показать информацию о таблицах
     * 
     * @return int
     */
    public function actionTables()
    {
        $this->stdout("Информация о таблицах в базе данных:\n");
        
        try {
            $db = Yii::$app->db;
            
            $tables = $db->createCommand('SHOW TABLES')->queryColumn();
            
            if (empty($tables)) {
                $this->stdout("📭 Таблицы не найдены\n");
                return ExitCode::OK;
            }
            
            foreach ($tables as $table) {
                $this->stdout("📋 Таблица: {$table}\n");
                
                // Показываем структуру таблицы
                $columns = $db->createCommand("DESCRIBE {$table}")->queryAll();
                foreach ($columns as $column) {
                    $this->stdout("  - {$column['Field']} ({$column['Type']}) {$column['Null']} {$column['Key']}\n");
                }
                
                // Показываем количество записей
                $count = $db->createCommand("SELECT COUNT(*) FROM {$table}")->queryScalar();
                $this->stdout("  📊 Записей: {$count}\n\n");
            }
            
            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("❌ Ошибка получения информации о таблицах: {$e->getMessage()}\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
