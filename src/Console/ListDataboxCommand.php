<?php

declare(strict_types=1);

namespace CSoellinger\Laravel\FonWebservices\Console;

use CSoellinger\FonWebservices\DataboxDownloadWs;
use Illuminate\Console\Command;

class ListDataboxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fon:list-databox
                            {--type= : Filter by databox type}
                            {--from= : From date (Y-m-d format)}
                            {--to= : To date (Y-m-d format)}
                            {--json : Output as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List items from the FinanzOnline databox';

    /**
     * Execute the console command.
     */
    public function handle(DataboxDownloadWs $databoxDownload): int
    {
        $typeOption = $this->option('type');
        $type = is_string($typeOption) ? $typeOption : '';

        $fromOption = $this->option('from');
        $from = is_string($fromOption) ? $fromOption : null;

        $toOption = $this->option('to');
        $to = is_string($toOption) ? $toOption : null;

        $asJson = $this->option('json');

        $fromDate = $from ? new \DateTime($from) : null;
        $toDate = $to ? new \DateTime($to) : null;

        try {
            if (! $asJson) {
                $this->info('Fetching databox items...');
            }

            $items = $databoxDownload->get($type, $fromDate, $toDate);

            if (empty($items)) {
                if ($asJson) {
                    $this->info(json_encode([]));
                } else {
                    $this->warn('No items found in databox');
                }

                return self::SUCCESS;
            }

            if ($asJson) {
                $this->info(json_encode(array_map(
                    fn ($item) => $this->formatItem($item),
                    $items
                )));

                return self::SUCCESS;
            }

            $this->displayItems($items);

            return self::SUCCESS;
        } catch (\Exception $e) {
            if ($asJson) {
                $error = ['error' => $e->getMessage()];
                $this->error(json_encode($error, JSON_PRETTY_PRINT));
            } else {
                $this->error("Error fetching databox items: {$e->getMessage()}");
            }

            return self::FAILURE;
        }
    }

    /**
     * Display the databox items in a formatted table.
     *
     * @param  array<\CSoellinger\FonWebservices\Model\DataboxDownloadListItem>  $items
     */
    private function displayItems(array $items): void
    {
        $this->info('Found '.count($items).' item(s)');

        $rows = array_map(function ($item) {
            return [
                $item->applkey ?? 'N/A',
                $item->erltyp ?? 'N/A',
                $item->filebez ?? 'N/A',
                $item->ts_zust ? $item->ts_zust->format('Y-m-d H:i:s') : 'N/A',
            ];
        }, $items);

        $this->table(
            ['Application Key', 'Type', 'Description', 'Date'],
            $rows
        );
    }

    /**
     * Format an item for JSON output.
     *
     * @param  \CSoellinger\FonWebservices\Model\DataboxDownloadListItem  $item
     * @return array<string, mixed>
     */
    private function formatItem($item): array
    {
        return [
            'applkey' => $item->applkey ?? null,
            'type' => $item->erltyp ?? null,
            'description' => $item->filebez ?? null,
            'date' => $item->ts_zust ? $item->ts_zust->format('Y-m-d H:i:s') : null,
        ];
    }
}
