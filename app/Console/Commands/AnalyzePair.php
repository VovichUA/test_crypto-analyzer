<?php

namespace App\Console\Commands;

use App\Services\PairAnalyzer;
use Illuminate\Console\Command;

class AnalyzePair extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:analyze-pair {pair}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze lowest and highest price for a given pair (e.g., BTC/USD). Please enter the pair separated by a slash (/)';

    private PairAnalyzer $analyzer;

    /**
     * @param PairAnalyzer $analyzer
     */
    public function __construct(PairAnalyzer $analyzer)
    {
        parent::__construct();
        $this->analyzer = $analyzer;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $pair = $this->argument('pair');
        if (preg_match('/^[A-Z]{3,4}\/[A-Z]{3,4}$/', $pair) === 0) {
            $this->error("Invalid argument pair:{$pair}");
            return;
        }
        $data = $this->analyzer->getPriceDataForPair($pair);

        if (empty($data)) {
            $this->error("No data available for pair {$pair}");
            return;
        }

        $lowest = collect($data)
            ->filter(fn ($item) => $item['price'] !== null && $item['price'] > 0)
            ->sortBy('price')->first();
        $highest = collect($data)->sortByDesc('price')->first();

        $this->info("Lowest price for {$pair}: {$lowest['price']} on {$lowest['exchange']}");
        $this->info("Highest price for {$pair}: {$highest['price']} on {$highest['exchange']}");
    }
}
