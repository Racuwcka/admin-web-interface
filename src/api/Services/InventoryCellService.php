<?php

namespace api\Services;

use api\Core\Models\Cell\CellQty;
use api\Core\Models\InventoryCell\InventoryCellEvent;
use api\Core\Models\InventoryCell\InventoryCellEventData;
use api\Core\Models\InventoryCell\InventoryCellFloor;
use api\Core\Models\InventoryCell\InventoryCellSearch;
use api\Core\Repositories\Cell\CellRepository;
use api\Core\Repositories\InventoryCell\InventoryCellRepository;

class InventoryCellService
{
    public static int $eventId = 15;

    public static function getEventData(): ?InventoryCellEventData
    {
        try {
            $users = [];
            $dates = [];

            $report = new InventoryCellEventData();
            $eventItems = InventoryCellRepository::getListActionResult(eventId: self::$eventId);

            foreach ($eventItems as $eventItem) {
                $report->qty_product += $eventItem->resultQty;
                $report->qty_extra += $eventItem->extraQty;
                $report->qty_missing += $eventItem->missingQty;

                $report->qty_cells += 1;

                if (!in_array($eventItem->userId, $users)) {
                    $users[] = $eventItem->userId;
                }

                $date = gmdate("Y-m-d", round($eventItem->date));
                if (!in_array($date, $dates)) {
                    $dates[] = $date;
                }
            }

            $report->qty_users = count($users);
            $report->qty_days = count($dates);
            $report->qty_percent_discrepancy = $report->qty_product > 0 ? round((($report->qty_extra + $report->qty_missing) * 100) / $report->qty_product) : 0;

            return $report;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * @param $letter
     * @param $floor
     * @return array<InventoryCellFloor>
     */
    public static function getFloor($letter, $floor): array
    {
        try {
            $shelfsAll = CellRepository::getListFloorCount(letter: $letter, floor: $floor);
            $shelfsData = InventoryCellRepository::getListFloorActionCount(eventId: self::$eventId, letter: $letter, floor: $floor);

            $shelfs = [];
            $shelfsDataCount = [];

            foreach ($shelfsData as $item) {
                $shelfsDataCount[$item->shelf] = $item->qty;
            }

            foreach ($shelfsAll as $shelf) {
                $id = $shelf->shelf;
                $completed = $shelfsDataCount[$id] ?? 0;
                $total = $shelf->qty;
                $percent = round(($completed * 100) / $total);

                $shelfs[] = new InventoryCellFloor(
                    id: $id,
                    completed: $completed,
                    total: $total,
                    percent: $percent
                );
            }

            return $shelfs;

        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * @param $letter
     * @param $floor
     * @param $shelf
     * @return array<InventoryCellEvent>
     */
    public static function getShelf($letter, $floor, $shelf): array
    {
        try {
            $cells = CellRepository::getListShelf(letter: $letter, floor: $floor, shelf: $shelf);

            $minTier = 1;
            $maxTier = 0;
            $maxPos = 0;

            $existsPos = [];

            foreach ($cells as $cell) {
                $minTier = min($cell->tier, $minTier);
                $maxTier = max($cell->tier, $maxTier);
                $maxPos = max($cell->pos, $maxPos);

                $existsPos[$cell->tier][$cell->pos] = true;
            }

            $events = InventoryCellRepository::getListShelfAction(eventId: self::$eventId, letter: $letter, floor: $floor, shelf: $shelf);

            $matrix = [];
            $matrixTemp = [];

            for ($tier = $minTier; $tier <= $maxTier; $tier++) {
                for ($pos = 1; $pos <= $maxPos; $pos++) {
                    $matrixTemp[$tier][$pos] = [
                        "name" => ($letter == 'A' ? $letter : "M$floor") . "-$shelf-$tier-$pos",
                        "exists" => $existsPos[$tier][$pos] ?? false,
                        "event" => null
                    ];
                }
            }

            foreach ($events as $event) {
                if (isset($matrixTemp[$event->tier][$event->pos])) {
                    $matrixTemp[$event->tier][$event->pos]['event'] = new InventoryCellEvent(
                        type: $event->type,
                        extraQty: $event->extraQty,
                        missingQty: $event->missingQty,
                        resultQty: $event->resultQty,
                        userName: $event->userName,
                        userPhoto: null,
                        date: $event->date
                    );
                }
            }

            foreach ($matrixTemp as $item) {
                $matrix[] = array_values($item);
            }

            return $matrix;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * @param $query
     * @return array<InventoryCellSearch>
     */
    public static function searchInEvent($query): array
    {
        try {
            $data = InventoryCellRepository::search(eventId: self::$eventId, query: $query);

            $list = [];

            /**@var array<InventoryCellSearch> $listTemp */
            $listTemp = [];

            foreach ($data as $item) {
                $key = $item->articul . $item->size;
                if (isset($listTemp[$key])) {
                    $listTemp[$key]->qty += $item->qty;
                    if (isset($listTemp[$key]->cells[$item->cellId])) {
                        $listTemp[$key]->cells[$item->cellId]->qty += $item->qty;
                    }
                    else {
                        $listTemp[$key]->cells[$item->cellId] = new CellQty(
                            id: $item->cellId,
                            name: $item->cellName,
                            qty: $item->qty
                        );
                    }
                }
                else {
                    $listTemp[$key] = new InventoryCellSearch(
                        size: $item->size,
                        qty: $item->qty,
                        cells: [
                            $item->cellId => new CellQty(
                                id: $item->cellId,
                                name: $item->cellName,
                                qty: $item->qty
                            )
                        ]
                    );
                }
            }

            foreach ($listTemp as $item) {
                $item->cells = array_values($item->cells);
                $list[] = $item;
            }

            return $list;

        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }
}