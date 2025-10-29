<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


class TransactionController extends Controller
{
    /**
     * Execute a given callback within a database transaction.
     *
     * If the callback returns successfully, the transaction will be committed.
     * If the callback throws an exception, the transaction will be rolled back.
     *
     * @param callable $callback
     * @return void
     */
    public function withTransaction($callback)
    {
        try {
            DB::beginTransaction();

            $result = $callback();

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
