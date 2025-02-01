#!/bin/bash

# Laravelスケジュールを毎分実行
echo "* * * * * php /workspace/artisan schedule:run >> /workspace/storage/logs/schedule.log 2>&1" > /etc/cron.d/laravel-scheduler

# crontab権限を修正
chmod 0644 /etc/cron.d/laravel-scheduler

# cronサービスを起動
cron -f
