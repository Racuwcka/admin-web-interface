<?php

namespace api\Core\Models\Input;

class InputRole
{
    /**
     * @param string $moduleId
     * @param bool $check
     * @param array<string, bool> $actions
     */
    public function __construct(
        public string $moduleId,
        public bool $check,
        public array $actions
    ) {}

    /**
     * @param string $json
     * @return array<self>
     */
    public static function fromJsonList(string $json): array {
        try {
            $list = [];

            $data = json_decode($json);
            foreach ($data as $item) {

                $actions = [];
                foreach ($item->actions as $actionId => $check) {
                    $actions[$actionId] = $check;
                }

                $list[] = new self(
                    moduleId: $item->moduleId,
                    check: $item->check,
                    actions: $actions
                );
            }

            return $list;
        }
        catch (\Exception $e) {
            return [];
        }
    }
}