<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Vacancy;

/**
 * Контроллер для заполнения базы данных тестовыми данными
 */
class SeedController extends Controller
{
    /**
     * Заполнить базу данных тестовыми вакансиями
     * 
     * @return int
     */
    public function actionVacancies()
    {
        $this->stdout("Создание тестовых вакансий...\n");
        
        $vacancies = [
            [
                'title' => 'Senior PHP Developer',
                'description' => 'Требуется опытный PHP разработчик для работы с Yii2, MySQL, Docker. Опыт работы от 3 лет.',
                'salary' => 150000,
            ],
            [
                'title' => 'Frontend Developer (Vue.js)',
                'description' => 'Разработка пользовательских интерфейсов на Vue.js 3 и Nuxt 3. Знание TypeScript обязательно.',
                'salary' => 120000,
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'Настройка и поддержка CI/CD, работа с Docker, Kubernetes, AWS. Опыт работы с Linux.',
                'salary' => 180000,
            ],
            [
                'title' => 'QA Engineer',
                'description' => 'Тестирование веб-приложений, автоматизация тестов, работа с Selenium, Postman.',
                'salary' => 90000,
            ],
            [
                'title' => 'Product Manager',
                'description' => 'Управление продуктом, анализ требований, работа с командой разработки.',
                'salary' => 140000,
            ],
            [
                'title' => 'UI/UX Designer',
                'description' => 'Создание дизайн-макетов, прототипирование, работа с Figma, Adobe Creative Suite.',
                'salary' => 110000,
            ],
            [
                'title' => 'Backend Developer (Python)',
                'description' => 'Разработка API на Python (Django/Flask), работа с PostgreSQL, Redis.',
                'salary' => 130000,
            ],
            [
                'title' => 'Mobile Developer (React Native)',
                'description' => 'Разработка мобильных приложений на React Native для iOS и Android.',
                'salary' => 140000,
            ],
            [
                'title' => 'Data Analyst',
                'description' => 'Анализ данных, создание отчетов, работа с SQL, Python, Tableau.',
                'salary' => 100000,
            ],
            [
                'title' => 'System Administrator',
                'description' => 'Администрирование серверов Linux, настройка сетевого оборудования, мониторинг.',
                'salary' => 95000,
            ],
        ];
        
        $created = 0;
        foreach ($vacancies as $vacancyData) {
            $vacancy = new Vacancy();
            $vacancy->setAttributes($vacancyData);
            
            if ($vacancy->save()) {
                $created++;
                $this->stdout("Создана вакансия: {$vacancy->title}\n");
            } else {
                $this->stderr("Ошибка при создании вакансии: {$vacancy->title}\n");
                $this->stderr(print_r($vacancy->getErrors(), true) . "\n");
            }
        }
        
        $this->stdout("Создано вакансий: {$created}\n");
        
        return ExitCode::OK;
    }
}
