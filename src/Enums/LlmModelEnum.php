<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Enums;

enum LlmModelEnum: string
{
    case GPT_3_5_TURBO = 'gpt-3.5-turbo';

    case GPT_4 = 'gpt-4';
    case GPT_4o = 'gpt-4o';
    case GPT_4_TURBO = 'gpt-4-turbo';

    // case CLAUDE_SONNET_3_5 = 'claude-3-5-sonnet-20240620';
    case CLAUDE_HAIKU_3_5 = 'claude-3-5-haiku-20241022';
    case CLAUDE_SONNET_3_5 = 'claude-3-5-sonnet-20241022';
    case CLAUDE_SONNET_3_7 = 'claude-3-7-sonnet-20250219';

    case LLAMA_3_1 = 'llama3.1';

    case GEMINI_1_5_PRO = 'gemini-1.5-pro';
    case GEMINI_1_5_FLASH = 'gemini-1.5-flash-latest';
    case GEMINI_2_0_FLASH_THINKING_EXP = 'gemini-2.0-flash-thinking-exp-01-21';
    case GEMINI_2_0_PRO_EXP = 'gemini-2.0-pro-exp-02-05';
    case GEMINI_2_5_PRO_EXP = 'gemini-2.5-pro-exp-03-25';

    case DEEPSEEK_CHAT = 'deepseek-chat';
    case DEEPSEEK_REASONER = 'deepseek-reasoner';

    public function llmFamilyName(): LlmProvider
    {
        return match ($this) {
            self::GPT_3_5_TURBO,
            self::GPT_4,
            self::GPT_4_TURBO,
            self::GPT_4o, => LlmProvider::OPEN_AI,

            self::CLAUDE_SONNET_3_5,
            self::CLAUDE_SONNET_3_7,
            self::CLAUDE_HAIKU_3_5 => LlmProvider::ANTHROPIC,

            self::LLAMA_3_1 => LlmProvider::META,

            self::GEMINI_1_5_PRO,
            self::GEMINI_2_0_FLASH_THINKING_EXP,
            self::GEMINI_2_0_PRO_EXP,
            self::GEMINI_2_5_PRO_EXP,
            self::GEMINI_1_5_FLASH => LlmProvider::GOOGLE,

            self::DEEPSEEK_CHAT,
            self::DEEPSEEK_REASONER => LlmProvider::DEEPSEEK
        };
    }

    /**
     * in dollar
     */
    public function costPerMillionInputTokens(): float
    {
        return match ($this) {
            self::GPT_3_5_TURBO => 3,
            self::GPT_4 => 30,
            self::GPT_4_TURBO => 10,
            self::GPT_4o, => 2.50,

            self::CLAUDE_HAIKU_3_5 => 1,
            self::CLAUDE_SONNET_3_5 => 3,
            self::CLAUDE_SONNET_3_7 => 3,

            // self::LLAMA_3_1 => 12,

            self::GEMINI_1_5_PRO => 1.25,
            self::GEMINI_1_5_FLASH => 0.075,

            self::DEEPSEEK_CHAT => 0.14,
            self::DEEPSEEK_REASONER => 0.55,

            default => 1,
        };
    }

    /**
     * in dollar
     */
    public function costPerInputToken(): float
    {
        return $this->costPerMillionInputTokens() / 1000_000;
    }

    /**
     * in dollar
     */
    public function costPerMillionOutputTokens(): float
    {
        return match ($this) {
            self::GPT_3_5_TURBO => 6,
            self::GPT_4 => 60,
            self::GPT_4_TURBO => 30,
            self::GPT_4o, => 10,

            self::CLAUDE_HAIKU_3_5 => 5,
            self::CLAUDE_SONNET_3_5 => 15,
            self::CLAUDE_SONNET_3_7 => 15,

            // self::LLAMA_3_1 => 12,

            self::GEMINI_1_5_PRO => 5,
            self::GEMINI_1_5_FLASH => 0.30,

            self::DEEPSEEK_CHAT => 0.28,
            self::DEEPSEEK_REASONER => 2.19,

            default => 1,
        };
    }

    /**
     * in dollar
     */
    public function costPerOutputToken(): float
    {
        return $this->costPerMillionOutputTokens() / 1000_000;
    }
}
