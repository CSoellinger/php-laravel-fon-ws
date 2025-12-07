<?php

declare(strict_types=1);

namespace CSoellinger\Laravel\FonWebservices\Console;

use CSoellinger\FonWebservices\Enum\VatIdCheckLevel;
use CSoellinger\FonWebservices\Model\VatIdCheckInvalid;
use CSoellinger\FonWebservices\Model\VatIdCheckValidLevelOne;
use CSoellinger\FonWebservices\Model\VatIdCheckValidLevelTwo;
use CSoellinger\FonWebservices\VatIdCheckWs;
use Illuminate\Console\Command;

class CheckVatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fon:check-vat
                            {vat-id : The VAT ID to check}
                            {--level=1 : Check level (1 or 2)}
                            {--json : Output as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check a VAT ID against FinanzOnline';

    /**
     * Execute the console command.
     */
    public function handle(VatIdCheckWs $vatIdCheck): int
    {
        $vatId = $this->argument('vat-id');
        $level = (int) $this->option('level');
        $asJson = $this->option('json');

        // Validate level
        if (! in_array($level, [1, 2], true)) {
            $this->error('Level must be 1 or 2');

            return self::FAILURE;
        }

        $checkLevel = $level === 1 ? VatIdCheckLevel::SimpleCheck : VatIdCheckLevel::FullCheck;

        try {
            if (! $asJson) {
                $this->info("Checking VAT ID: {$vatId} (Level {$level})");
            }

            $result = $vatIdCheck->check($vatId, $checkLevel);

            if ($asJson) {
                $this->info(json_encode($this->formatResult($vatId, $result)));

                return self::SUCCESS;
            }

            $this->displayResult($vatId, $result);

            return self::SUCCESS;
        } catch (\Exception $e) {
            if ($asJson) {
                $error = ['error' => $e->getMessage()];
                $this->error(json_encode($error, JSON_PRETTY_PRINT));
            } else {
                $this->error("Error checking VAT ID: {$e->getMessage()}");
            }

            return self::FAILURE;
        }
    }

    /**
     * Display the VAT check result in a formatted way.
     */
    private function displayResult(string $vatId, VatIdCheckInvalid|VatIdCheckValidLevelOne|VatIdCheckValidLevelTwo $result): void
    {
        if ($result instanceof VatIdCheckInvalid) {
            $this->error('❌ VAT ID is INVALID');
            $this->table(
                ['Field', 'Value'],
                [
                    ['VAT ID', $vatId],
                    ['Valid', 'No'],
                    ['Error Code', $result->code],
                    ['Message', $result->msg],
                ]
            );

            return;
        }

        $this->info('✅ VAT ID is VALID');

        if ($result instanceof VatIdCheckValidLevelTwo) {
            $this->table(
                ['Field', 'Value'],
                [
                    ['VAT ID', $vatId],
                    ['Valid', 'Yes'],
                    ['Name', $result->name],
                    ['Address', $result->address],
                ]
            );

            return;
        }

        // Level 1 only confirms valid
        $this->table(
            ['Field', 'Value'],
            [
                ['VAT ID', $vatId],
                ['Valid', 'Yes'],
            ]
        );
    }

    /**
     * Format the result for JSON output.
     *
     * @return array<string, mixed>
     */
    private function formatResult(string $vatId, VatIdCheckInvalid|VatIdCheckValidLevelOne|VatIdCheckValidLevelTwo $result): array
    {
        if ($result instanceof VatIdCheckInvalid) {
            return [
                'vat_id' => $vatId,
                'valid' => false,
                'error_code' => $result->code,
                'error_message' => $result->msg,
            ];
        }

        $data = [
            'vat_id' => $vatId,
            'valid' => true,
        ];

        if ($result instanceof VatIdCheckValidLevelTwo) {
            $data['name'] = $result->name;
            $data['address'] = $result->address;
        }

        return $data;
    }
}
