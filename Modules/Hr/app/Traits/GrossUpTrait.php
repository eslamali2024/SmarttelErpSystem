<?php

namespace Modules\Hr\Traits;

use Modules\Hr\Traits\CalculateIcomeTax;

trait GrossUpTrait
{
    private $min_insurance = 2300;
    private $max_insurance = 14500;

    /**
     * Sets the CalculateIncomeTax object used to calculate the income tax.
     *
     * @param CalculateIcomeTax $calculateIcomeTax The CalculateIncomeTax object to use.
     */
    public function setCalculateIncomeTax(CalculateIcomeTax $calculateIcomeTax)
    {
        $this->calculateIcomeTax = $calculateIcomeTax;
    }

    /**
     * Calculate the social insurance for an employee given their insurance wage.
     * The social insurance is capped at $this->max_insurance and floored at $this->min_insurance.
     * The social insurance is calculated as 11% of the insured amount.
     *
     * @return float The social insurance amount for the employee.
     */
    private function calculateSocialInsurance()
    {
        $insurance                  =  $this->insurance_wage_rounded > $this->max_insurance ? $this->max_insurance : $this->insurance_wage_rounded;
        $insurance                  =  $insurance < $this->min_insurance ? $this->min_insurance : $insurance;

        return $insurance * 0.11;
    }

    /**
     * Calculate the company insurance for an employee given their insurance wage.
     * The company insurance is capped at $this->max_insurance and floored at $this->min_insurance.
     * The company insurance is calculated as 18.75% of the insured amount.
     *
     * @return float The company insurance amount for the employee.
     */
    private function calculateCompanyInsurance()
    {
        $company_insurance                  =  $this->insurance_wage_rounded > $this->max_insurance ? $this->max_insurance : $this->insurance_wage_rounded;
        $company_insurance                  =  $company_insurance < $this->min_insurance ? $this->min_insurance : $company_insurance;

        return $company_insurance * 0.1875;
    }

    /**
     * Calculates the net salary of an employee, given their gross salary monthly.
     *
     * @param float $gross_salary_monthly The gross salary of the employee monthly.
     *
     * @return array An array containing the net salary, tax, insurance, and deductions monthly.
     */
    public function calculate_net_salary($gross_salary_monthly)
    {
        // $this->insurance_wage = $gross_salary_monthly * 0.769231148;
        // $this->insurance_wage =  ceil(($gross_salary_monthly / 1.3) / 100) * 100;
        if (($gross_salary_monthly / 1.30) < $this->min_insurance) {
            $this->insurance_wage =  $this->min_insurance;
        } elseif (($gross_salary_monthly / 1.30) > $this->max_insurance) {
            $this->insurance_wage =  $this->max_insurance;
        } else {
            $this->insurance_wage =  $gross_salary_monthly / 1.30;
        }
        $this->insurance_wage_rounded =  ceil($this->insurance_wage / 100) * 100;

        $monthly_insurance      = $this->calculateSocialInsurance();
        $company_insurance      = $this->calculateCompanyInsurance();

        $annual_insurance       = $monthly_insurance * 12;
        $gross_salary_annual    = $gross_salary_monthly * 12;


        // Calculate tax
        $martyrs_families_fund = $gross_salary_monthly * 0.0005;
        // $monthly_tax = $this->calculateIcomeTax->handle($gross_salary_annual - $annual_insurance - ($martyrs_families_fund * 12))['tax'];
        $monthly_tax = $this->calculateIcomeTax->handle($gross_salary_annual - $annual_insurance)['tax'];
        $total_monthly_deductions = $monthly_insurance +  $monthly_tax + $martyrs_families_fund;
        $net_salary_calculated = $gross_salary_monthly - $total_monthly_deductions;

        return [
            'net_salary'                => $net_salary_calculated,
            'tax_monthly'               => $monthly_tax,
            'deductions_monthly'        => $total_monthly_deductions,
            'insurance_monthly'         => $monthly_insurance,
            'company_insurance'         => $company_insurance,
            'martyrs_families_fund'     => $martyrs_families_fund,
            'insurance_wage'            => $this->insurance_wage,
            'insurance_wage_rounded'    => $this->insurance_wage_rounded
        ];
    }


    /**
     * Calculate the gross salary monthly given a target net salary monthly.
     * The algorithm starts with an initial guess of the gross salary being the target net salary divided by 0.75 (25% above the target net salary).
     * It then iterates up to 100 times, adjusting the gross salary based on the difference between the target net salary and the calculated net salary.
     * The adjustment factor is 1 - 0.25, meaning that the gross salary is adjusted by 75% of the difference.
     * If the difference between the target net salary and the calculated net salary is within 0.01 of the target net salary, the iteration stops.
     * If the maximum number of iterations is reached without finding a suitable gross salary, an error message is printed.
     *
     * @param float $net_salary_target The target net salary monthly.
     *
     * @return array An array containing the calculated gross salary, net salary, tax, insurance, and deductions monthly.
     */
    function gross_up_salary($net_salary_target)
    {
        $salary_per_year    = 0;
        $salary_per_day     = 0;
        $salary_per_hour    = 0;

        // Calculate the gross salary based on the target net salary
        if (is_numeric($net_salary_target)) {
            $initial_guess = $net_salary_target / 0.75;
            $gross_salary = $initial_guess;
            $max_iterations = 100;
            $tolerance = 0.001; // Adjust this value as needed

            for ($i = 0; $i < $max_iterations; $i++) {
                // Calculate the net salary
                $result = $this->calculate_net_salary($gross_salary);
                $calculated_net = $result['net_salary'];

                // Calculate the difference
                $difference = $net_salary_target - $calculated_net;
                if (abs($difference) <= $tolerance) {
                    break;
                }

                // Adjust the gross salary
                $adjustment_factor = 1 - 0.25; // Adjust this value as needed
                $gross_salary += $difference / $adjustment_factor;
            }

            $final_results = $this->calculate_net_salary($gross_salary);
        }

        if ($gross_salary > 0) {
            $salary_per_year    = $gross_salary * 12;
            $salary_per_day     = $salary_per_year / 365;
            $salary_per_hour    = $salary_per_day / 8;
        }

        return [
            'gross_salary_monthly'      => $gross_salary ?? 0,
            'net_salary_calculated'     => $final_results['net_salary'] ?? 0,
            'monthly_tax'               => $final_results['tax_monthly'] ?? 0,
            'monthly_insurance'         => $final_results['insurance_monthly'] ?? 0,
            'company_insurance'         => $final_results['company_insurance'] ?? 0,
            'monthly_deductions'        => $final_results['deductions_monthly'] ?? 0,
            'martyrs_families_fund'     => $final_results['martyrs_families_fund'] ?? 0,
            'insurance_wage'            => $final_results['insurance_wage'] ?? 0,
            'insurance_wage_rounded'    => $final_results['insurance_wage_rounded'] ?? 0,
            'salary_per_year'           => $salary_per_year,
            'salary_per_day'            => $salary_per_day,
            'salary_per_hour'           => $salary_per_hour
        ];
    }
}
