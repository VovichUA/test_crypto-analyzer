<?php

namespace App\Console\Commands;

use App\Services\PairAnalyzer;
use Illuminate\Console\Command;

class AnalyzeProfit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:analyze-profit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze profit percentage for all common pairs';

    private PairAnalyzer $analyzer;

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
        $pairses = $this->analyzer->getCommonPairs();

        foreach ($pairses as $pairs) {
            foreach ($pairs as $pair) {
                $data = $this->analyzer->getPriceDataForPair($pair);

                $lowest = collect($data)->sortBy('price')->first();
                $highest = collect($data)->sortByDesc('price')->first();

                if ($highest['price'] === null || $lowest['price'] === null) {
                    $profit = null;
                } else {
                    $profit = (($highest['price'] - $lowest['price']) / $lowest['price']) * 100;
                }

                $this->line("{$pair}: Profit = {$profit}% (Buy on {$lowest['exchange']}, Sell on {$highest['exchange']})");
            }
        }

        $this->info("Finish!");
    }
}
