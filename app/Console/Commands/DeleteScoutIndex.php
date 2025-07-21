<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client;
use Meilisearch\Contracts\IndexesQuery;

class DeleteScoutIndex extends Command
{
    protected $signature = 'scout:delete-all-indexes';

    protected $description = 'Удаляет все индексы из MeiliSearch';

    protected Client $client;

    public function __construct()
    {
        parent::__construct();

        $this->client = new Client(
            config('scout.meilisearch.host'),
            config('scout.meilisearch.key')
        );
    }

    public function handle(): int
    {
        $this->info('Получаем список всех индексов...');

        $limit = 1000000;
        $query = new IndexesQuery();
        $query->setLimit($limit);

        $indexes = $this->client->getIndexes($query);

        if (empty($indexes->getResults())) {
            $this->info('Индексы не найдены.');
            return self::SUCCESS;
        }

        $tasks = [];

        foreach ($indexes->getResults() as $index) {
            $this->info("Удаляем индекс: {$index->getUid()}");
            $tasks[] = $index->delete();
        }

        $this->info('Запущено удаление всех индексов.');

        // Опционально: ждать завершения задач (если нужно)
        foreach ($tasks as $task) {
            $taskId = $task['taskUid'];
            $this->info("Ожидание завершения задачи $taskId...");
            $this->client->waitForTask($taskId);
        }

        $this->info('Все индексы удалены.');

        return self::SUCCESS;
    }
}
