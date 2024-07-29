<?php
namespace api\Core\Models;

use api\Core\Enums\MessageType;
use api\Core\Enums\MessageView;
use Core\Localizations\Localizations;

class Message implements \JsonSerializable {
    public function __construct(
        public MessageView $view,
        public MessageType $type,
        public string $text
    ) {}

    public static function do(
        MessageType $type,
        string $messageLocaleKey,
        array $messageLocaleValues = [],
        MessageView $view = MessageView::toast,
    ): Message
    {
        return new Message(
            view: $view,
            type: $type,
            text: Localizations::get(
                keyword: $messageLocaleKey,
                values: $messageLocaleValues
            )
        );
    }

    public function jsonSerialize(): array
    {
        return [
            "view" => $this->view->value,
            "type" => $this->type->value,
            "text" => $this->text
        ];
    }
}
