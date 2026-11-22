<?php

namespace App\Database;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Processors\Processor;

class OracleProcessor extends Processor
{
    /**
     * Map of primary keys or table names to their Oracle sequence names in PET_CARE.
     */
    protected array $sequenceMap = [
        'user_id' => 'SEQ_USER_ID',
        'pet_id' => 'SEQ_PET_ID',
        'animal_id' => 'SEQ_ANIMAL_ID',
        'adoption_request_id' => 'SEQ_ADOPTION_REQUEST_ID',
        'product_id' => 'SEQ_PRODUCT_ID',
        'order_id' => 'SEQ_ORDER_ID',
        'order_detail_id' => 'SEQ_ORDER_DETAIL_ID',
        'payment_id' => 'SEQ_PAYMENT_ID',
        'service_id' => 'SEQ_SERVICE_ID',
        'booking_id' => 'SEQ_BOOKING_ID',
        'id' => 'SEQ_USER_ID',
    ];

    /**
     * Process an "insert get ID" query on Oracle 11g XE.
     */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
    {
        $connection = $query->getConnection();
        $connection->insert($sql, $values);

        $pk = strtolower((string) ($sequence ?: 'id'));
        $seqName = $this->sequenceMap[$pk] ?? null;

        if ($seqName) {
            $row = $connection->selectOne("SELECT {$seqName}.CURRVAL AS id FROM dual");
            if ($row) {
                $id = is_object($row) ? ($row->id ?? $row->ID ?? null) : ($row['id'] ?? $row['ID'] ?? null);
                return is_numeric($id) ? (int) $id : $id;
            }
        }

        // Fallback: query MAX(pk) from the target table
        $table = $query->getGrammar()->wrapTable($query->from);
        $col = strtoupper($pk);
        $row = $connection->selectOne("SELECT MAX({$col}) AS id FROM {$table}");
        if ($row) {
            $id = is_object($row) ? ($row->id ?? $row->ID ?? null) : ($row['id'] ?? $row['ID'] ?? null);
            return is_numeric($id) ? (int) $id : $id;
        }

        return 0;
    }

    /**
     * Process the results of a select query (strip ROWNUM helper column if present).
     */
    public function processSelect(Builder $query, $results)
    {
        foreach ($results as $row) {
            if (is_object($row) && property_exists($row, 'rnum')) {
                unset($row->rnum);
            } elseif (is_array($row) && array_key_exists('rnum', $row)) {
                unset($row['rnum']);
            }
        }

        return $results;
    }
}
