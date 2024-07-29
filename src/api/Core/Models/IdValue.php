<?php
namespace api\Core\Models;

class IdValue {
    public function __construct(
        public $id,
        public $value
    ) {}
}
