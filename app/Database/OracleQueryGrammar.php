<?php

namespace App\Database;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\Grammar;

class OracleQueryGrammar extends Grammar
{
    /**
     * Map of table names to their exact Oracle 11g XE PET_CARE identifiers.
     */
    protected array $tableMap = [
        'user' => '"USER"',
        'orders' => '"ORDER"',
        'order' => '"ORDER"',
        'pet' => 'PET',
        'animal' => 'ANIMAL',
        'adoption_request' => 'ADOPTION_REQUEST',
        'product' => 'PRODUCT',
        'order_detail' => 'ORDER_DETAIL',
        'payment' => 'PAYMENT',
        'health_service' => 'HEALTH_SERVICE',
        'health_booking' => 'HEALTH_BOOKING',
    ];

    /**
     * Get the format for database stored dates.
     */
    public function getDateFormat(): string
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * Wrap a table in keyword identifiers.
     */
    public function wrapTable($table, $prefix = null): string
    {
        if (!$this->isExpression($table)) {
            $lower = strtolower((string) $table);
            if (isset($this->tableMap[$lower])) {
                return $this->tableMap[$lower];
            }
            return strtoupper((string) $table);
        }

        return $this->getValue($table);
    }

    /**
     * Wrap a single string in keyword identifiers.
     */
    protected function wrapValue($value): string
    {
        if ($value === '*') {
            return $value;
        }

        $lower = strtolower((string) $value);
        if (isset($this->tableMap[$lower])) {
            return $this->tableMap[$lower];
        }

        // Standard column / alias identifier in Oracle (unquoted uppercase so it is case-insensitive)
        return strtoupper(str_replace('"', '', (string) $value));
    }

    /**
     * Compile a select query into SQL (with Oracle 11g ROWNUM support for limit/offset).
     */
    public function compileSelect(Builder $query): string
    {
        $limit = $query->limit;
        $offset = $query->offset;

        $query->limit = null;
        $query->offset = null;

        $sql = parent::compileSelect($query);

        $query->limit = $limit;
        $query->offset = $offset;

        if ($limit !== null && $offset !== null && $offset > 0) {
            $maxRow = (int) $offset + (int) $limit;
            $minRow = (int) $offset + 1;
            return "SELECT * FROM (SELECT inner_q.*, ROWNUM AS rnum FROM ({$sql}) inner_q WHERE ROWNUM <= {$maxRow}) WHERE rnum >= {$minRow}";
        }

        if ($limit !== null) {
            $maxRow = (int) $limit;
            return "SELECT * FROM ({$sql}) WHERE ROWNUM <= {$maxRow}";
        }

        if ($offset !== null && $offset > 0) {
            $minRow = (int) $offset + 1;
            return "SELECT * FROM (SELECT inner_q.*, ROWNUM AS rnum FROM ({$sql}) inner_q) WHERE rnum >= {$minRow}";
        }

        return $sql;
    }

    /**
     * Compile the "limit" portions of the query (handled in compileSelect for Oracle 11g).
     */
    protected function compileLimit(Builder $query, $limit): string
    {
        return '';
    }

    /**
     * Compile the "offset" portions of the query (handled in compileSelect for Oracle 11g).
     */
    protected function compileOffset(Builder $query, $offset): string
    {
        return '';
    }
}
