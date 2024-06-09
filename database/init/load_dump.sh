#!/bin/bash
# Скрипт загрузки снимка структуры БД и необходимых для инициализации приложения данных

cd "$(dirname "$0")"

RED='\033[1;31m' # Red Color
NOCOLOR='\033[0m' # No Color

# загрузка переменных среды для БД
export $(grep -e '^DB_.*' ./../../.env | xargs -d '\n')

# если БД не пустая, загрузка дампа не производится
echo "Check database is not empty ... "
output=$(mysql --user=root --host=$DB_HOST --port=$DB_PORT -p$DB_PASSWORD -N -e "SELECT COUNT(*) AS tables_count FROM information_schema.tables WHERE table_schema='${DB_DATABASE}';")
if [[ ${output} -gt 1 ]]; then
  echo -e "${RED}Abort dump loading:${NOCOLOR} DB '${DB_DATABASE}' already exists and is not empty"
  exit 1
fi
echo "OK"

sed \
    -e "s/USE test_task;/USE $DB_DATABASE;/" \
    -e "s/utf8mb4_0900_ai_ci/utf8mb4_unicode_520_ci/" \
    < ./../../resources/test_task_202204011832.sql | mysql --user=root --host=$DB_HOST --port=$DB_PORT -p$DB_PASSWORD $DB_DATABASE
echo "Database loaded"
