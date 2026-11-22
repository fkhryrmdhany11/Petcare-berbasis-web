<?php

namespace App\Database;

use Illuminate\Database\Connection;

class OracleConnection extends Connection
{
    /**
     * Get the default query grammar instance.
     */
    protected function getDefaultQueryGrammar(): OracleQueryGrammar
    {
        return new OracleQueryGrammar($this);
    }

    /**
     * Get the default post processor instance.
     */
    protected function getDefaultPostProcessor(): OracleProcessor
    {
        return new OracleProcessor();
    }
}
