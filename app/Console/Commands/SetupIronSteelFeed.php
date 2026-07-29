<?php

namespace App\Console\Commands;

use App\Models\Feed\FeedProductLink;
use App\Models\Shop\Product;
use App\Services\Feed\FeedException;
use App\Services\Feed\IronSteelSetupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupIronSteelFeed extends Command
{
    protected $signature = 'feed:iron-steel:setup
        {--mappings=database/data/iron-steel-product-mappings.csv : Нормализованный CSV сопоставлений}
        {--dry-run : Только проверить файл и товары}';

    protected $description = 'Настроить источник ProMetall и импортировать подтверждённые сопоставления';

    public function handle(IronSteelSetupService $setup): int
    {
        try {
            $rows = $this->readAndValidate(base_path($this->option('mappings')));
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $counts = array_count_values(array_column($rows, 'decision'));
        $this->table(
            ['Показатель', 'Количество'],
            [
                ['Всего решений', count($rows)],
                ['Связать', $counts[FeedProductLink::DECISION_LINK] ?? 0],
                ['Создать новый', $counts[FeedProductLink::DECISION_CREATE] ?? 0],
            ]
        );

        if ($this->option('dry-run')) {
            $this->info('Проверка завершена, база не изменена.');

            return self::SUCCESS;
        }

        try {
            $source = $setup->sync();
            DB::transaction(function () use ($rows, $source) {
                foreach ($rows as $row) {
                    $link = FeedProductLink::query()->firstOrNew([
                        'feed_source_id' => $source->id,
                        'offer_id' => $row['offer_id'],
                    ]);
                    $metadata = $link->metadata ?? [];
                    if ($row['comment'] === '') {
                        unset($metadata['mapping_comment']);
                    } else {
                        $metadata['mapping_comment'] = $row['comment'];
                    }

                    $link->vendor_code = $row['vendor_code'] ?: null;
                    $link->metadata = $metadata ?: null;

                    if ($row['decision'] === FeedProductLink::DECISION_LINK) {
                        $link->decision = FeedProductLink::DECISION_LINK;
                        $link->product_id = (int) $row['product_id'];
                    } elseif (! $link->exists || ! $link->product_id) {
                        $link->decision = FeedProductLink::DECISION_CREATE;
                        $link->product_id = null;
                    }

                    $link->save();
                }
            });
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Источник ProMetall и сопоставления сохранены.');

        return self::SUCCESS;
    }

    private function readAndValidate(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new FeedException("Файл сопоставлений не найден: {$path}");
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new FeedException('Не удалось открыть файл сопоставлений.');
        }

        try {
            $header = fgetcsv($handle);
            $expected = ['offer_id', 'vendor_code', 'decision', 'product_id', 'comment'];
            if ($header !== $expected) {
                throw new FeedException('Некорректные колонки CSV. Ожидаются: '.implode(', ', $expected));
            }

            $rows = [];
            $offerIds = [];
            $productIds = [];
            $line = 1;
            while (($values = fgetcsv($handle)) !== false) {
                $line++;
                if ($values === [null] || $values === []) {
                    continue;
                }

                $values = array_pad($values, count($expected), '');
                $row = array_combine($expected, array_map('trim', $values));

                if ($row['offer_id'] === '' || ! ctype_digit($row['offer_id'])) {
                    throw new FeedException("Строка {$line}: некорректный offer_id.");
                }
                if (isset($offerIds[$row['offer_id']])) {
                    throw new FeedException("Строка {$line}: offer_id {$row['offer_id']} повторяется.");
                }
                $offerIds[$row['offer_id']] = true;

                if (! in_array($row['decision'], [
                    FeedProductLink::DECISION_LINK,
                    FeedProductLink::DECISION_CREATE,
                ], true)) {
                    throw new FeedException("Строка {$line}: неизвестное решение {$row['decision']}.");
                }

                if ($row['decision'] === FeedProductLink::DECISION_LINK) {
                    if (! ctype_digit($row['product_id'])) {
                        throw new FeedException("Строка {$line}: для связи нужен product_id.");
                    }
                    if (! Product::query()->whereKey((int) $row['product_id'])->exists()) {
                        throw new FeedException("Строка {$line}: товар ID {$row['product_id']} не найден.");
                    }
                    if (isset($productIds[$row['product_id']])) {
                        throw new FeedException(
                            "Строка {$line}: товар ID {$row['product_id']} уже связан с другим offer id."
                        );
                    }
                    $productIds[$row['product_id']] = true;
                } else {
                    $row['product_id'] = '';
                }

                $rows[] = $row;
            }
        } finally {
            fclose($handle);
        }

        return $rows;
    }
}
