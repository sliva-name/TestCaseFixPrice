FROM node:22-alpine

WORKDIR /app

# Копирование package.json
COPY package*.json ./

# Установка зависимостей
RUN npm install

# Копирование остальных файлов
COPY . .

# Сборка приложения
RUN npm run build

# Экспорт порта
EXPOSE 3000

# Запуск приложения
CMD ["npm", "run", "dev"]