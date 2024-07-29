<?php

namespace Requests\Modules\OneC;

use Requests\Core\AbstractClasses\RequestIndex;
use Requests\Modules\Logistics\RequestLogisticsSend;
use Requests\Modules\OneC\Parts\Cell;
use Requests\Modules\OneC\Parts\Common;
use Requests\Modules\OneC\Parts\Document;
use Requests\Modules\OneC\Parts\InventoryCell;
use Requests\Modules\OneC\Parts\MovingWarehouse;
use Requests\Modules\OneC\Parts\Package;
use Requests\Modules\OneC\Parts\Placement;
use Requests\Modules\OneC\Parts\Product;
use Requests\Modules\OneC\Parts\TransitStorage;
use Requests\Modules\OneC\Parts\Warehouse;

/**
 * @property Cell $cell
 * @property MovingWarehouse $movingWarehouse
 * @property Product $product
 * @property TransitStorage $transitStorage
 * @property InventoryCell $inventoryCell
 * @property Package $package
 * @property Warehouse $warehouse
 * @property Placement $placement
 * @property Document $document
 * @property Common $common
 *
 * @property RequestOneCParams $params
 * @property RequestLogisticsSend $send
 */

class RequestOneCIndex extends RequestIndex
{
    protected Cell $cell;
    protected MovingWarehouse $movingWarehouse;
    protected Product $product;
    protected TransitStorage $transitStorage;
    protected InventoryCell $inventoryCell;
    protected Package $package;
    protected Warehouse $warehouse;
    protected Placement $placement;
    protected Document $document;
    protected Common $common;

    public function declareParams() : RequestOneCParams
    {
        return new RequestOneCParams();
    }

    public function declareSend() : RequestOneCSend
    {
        return new RequestOneCSend();
    }
}