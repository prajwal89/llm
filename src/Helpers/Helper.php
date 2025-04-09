<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Helpers;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Helper
{
    public static function extractValidJson(string $text): false|string
    {
        $startPos = strpos($text, '{');
        $endPos = strrpos($text, '}');

        if ($startPos !== false && $endPos !== false && $startPos < $endPos) {
            // ! this can go wrong in some cases
            // Extract the substring between the first '{' and last '}'
            $jsonSubstring = substr($text, $startPos, ($endPos - $startPos) + 1);

            json_decode($jsonSubstring, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $jsonSubstring;
            }
            Log::info('JSON decode error: ' . json_last_error_msg());

            return false;
        }

        return false;
    }

    public static function JsonToJsonl(string $jsonString): string
    {
        // Decode the JSON string
        $data = json_decode($jsonString, true);

        // Validate the JSON structure
        if (!is_array($data)) {
            throw new Exception('Invalid JSON format: expected an array of objects.');
        }

        // Convert each array item to a JSONL line
        $jsonlLines = array_map(function ($item) {
            return json_encode($item);
        }, $data);

        // Join the lines with a newline character
        return implode("\n", $jsonlLines);
    }

    public static function jsonlToArray($jsonlString): Collection
    {
        $lines = explode("\n", $jsonlString);

        // Decode each line into an associative array
        $data = array_map(function ($line) {
            return json_decode($line, true);
        }, array_filter($lines));

        return collect($data);
    }

    /**
     * For identifying unique llm usage
     */
    public static function llmUsageHash(
        ?string $systemPrompt,
        array $messages, // user messages
        string $modelName
    ): string {
        return md5($systemPrompt . json_encode($messages) . $modelName);
    }
}
