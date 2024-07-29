<?php

namespace Database\Core\Entity;

use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Interfaces\Operator;
use Database\Core\Models\WhereRelease;
use Database\Core\Models\Operators\LogisticOperatorAnd;
use Database\Core\Models\Operators\LogisticOperatorOr;

class Where {

    /**@var array<Operator> $conditions **/
    private array $conditions = [];

    public function __construct(?Operator $operator = null) {
        if (!is_null($operator)) {
            $this->conditions[] = $operator;
        }
    }

    public function add(OperatorLogisticType $logisticOperatorType, Operator $operator): static
    {
        if (count($this->conditions) > 0) {
            $this->conditions[] = match ($logisticOperatorType) {
                OperatorLogisticType::And => new LogisticOperatorAnd(),
                OperatorLogisticType::Or => new LogisticOperatorOr()
            };
        }

        $this->conditions[] = $operator;
        return $this;
    }

    public function release() : WhereRelease {
        $query = '';
        $params = [];

        foreach ($this->conditions as $condition) {
            $data = $condition->release();
            $query .= " " . $data->query;
            $params = array_merge($params, $data->params);
        }

        if (!empty($query)) {
            $query = " WHERE$query";
        }

        return new WhereRelease(
            query: $query,
            params: $params
        );
    }
}