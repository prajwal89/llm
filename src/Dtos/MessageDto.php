<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Dtos;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MessageDto
{
    public function __construct(
        public string $role,
        public string $text,
    ) {
        // $rules = [
        //     'role' => 'required|string|max:255',
        //     'text' => 'required|string|max:500',
        // ];

        // $data = [
        //     'role' => $this->role,
        //     'text' => $this->text,
        // ];

        // $validator = Validator::make($data, $rules);

        // if ($validator->fails()) {
        //     throw new ValidationException($validator);
        // }
    }

    public static function fromOpenAi(array $choice): self
    {
        return new self(
            role: $choice['message']['role'],
            text: $choice['message']['content'],
        );
    }

    public function toOpenAi(): array
    {
        return [
            'role' => $this->role,
            'content' => $this->text,
        ];
    }

    public static function fromAnthropic(array $choice): self
    {
        return new self(
            role: 'assistant',
            text: $choice['text'],
        );
    }

    public function toAnthropic(): array
    {
        return [
            'role' => $this->role,
            'content' => $this->text,
        ];
    }

    public static function fromMeta(array $array): self
    {
        return new self(
            role: $array['role'],
            text: $array['content'],
        );
    }

    public static function fromGoogle(array $array): self
    {
        return new self(
            role: $array['content']['role'],
            text: $array['content']['parts'][0]['text'],
        );
    }

    public static function fromArray(array $array): self
    {
        return new self(
            role: $array['role'],
            text: $array['text'],
        );
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'text' => $this->text,
        ];
    }
}
