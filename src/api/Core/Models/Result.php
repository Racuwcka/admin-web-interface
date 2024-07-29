<?php

namespace api\Core\Models;

use api\Core\Enums\MessageType;
use api\Core\Enums\MessageView;

class Result implements \JsonSerializable
{
    /**
     * @param mixed $data
     * @param bool $status
     * @param array<Message> $message
     */
    public function __construct(
        public bool            $status,
        public Message | array $message,
        public mixed           $data = null
    ) {}

    public static function do(
        bool            $status,
        mixed           $data = null,
        Message | array $message = []): Result
    {
        return new Result(
            status: $status,
            message: is_array($message) ? $message : [$message],
            data: $data
        );
    }

    public static function success(
        mixed $data = null,
        ?string $message = null,
        array $messageValues = [],
        MessageView $messageView = MessageView::toast,
        MessageType $messageType = MessageType::success,
    ): Result
    {
        return new Result(
            status: true,
            message: $message ? [Message::do(
                type: $messageType,
                messageLocaleKey: $message,
                messageLocaleValues: $messageValues,
                view: $messageView
            )] : [],
            data: $data);
    }

    public static function error(
        ?string $message = null,
        array $messageValues = [],
        MessageView $messageView = MessageView::toast,
        MessageType $messageType = MessageType::error,
    ): Result
    {
        return new Result(
            status: false,
            message: $message ? [Message::do(
                type: $messageType,
                messageLocaleKey: $message,
                messageLocaleValues: $messageValues,
                view: $messageView
            )] : []);
    }

    public function isMessages(): bool
    {
        return count($this->message) > 0;
    }

    /** @return array<string> */
    public function getMessagesText(): array
    {
        return array_map(fn($message) => $message->text, $this->message);
    }

    public function jsonSerialize(): array
    {
        return [
            "success" => $this->status,
            "message" => $this->message,
            "data" => $this->data
        ];
    }
}