FROM nginx:alpine

# Копирование конфигурации
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Создание директории для PHP файлов
RUN mkdir -p /var/www/html

EXPOSE 80