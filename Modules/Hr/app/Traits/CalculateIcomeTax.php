<?php

namespace Modules\Hr\Traits;

class CalculateIcomeTax
{
    /**
     * Calculate income tax for given net income
     *
     * @param float $netIncome
     * @return array containing tax and salary after tax
     */
    public function handle($netIncome): array
    {
        $bracketsData = config('hr.net_icome_tax')[0];
        $netIncome    = $netIncome - config('hr.net_icome_tax')[1]['amount'];

        $incomeLevels = array_map(function ($level) {
            return $level === 'INF' ? INF : (float) $level;
        }, array_keys($bracketsData));
        sort($incomeLevels);

        $selectedBracket = null;
        foreach ($incomeLevels as $level) {
            if ($netIncome <= $level) {
                $selectedBracket = $bracketsData[(string)($level === INF ? 'INF' : $level)];
                break;
            }
        }

        $tax = 0;
        foreach ($selectedBracket as $rate) {
            if ($netIncome > $rate['min']) {
                $taxable = min($netIncome, $rate['max']) - $rate['min'];
                $tax += $taxable * ($rate['rate'] / 100);
            }

            if ($netIncome <= $rate['max']) {
                break;
            }
        }

        $tax_in_month = $tax / 12;

        return [
            'tax'               => $tax_in_month,
            'salary'            => max($netIncome - $tax_in_month, 0),
        ];
    }
}
