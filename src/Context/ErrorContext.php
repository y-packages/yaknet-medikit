<?php

namespace YakNet\Medikit\Context;

/**
 * Captures the context of an error, including the code snippet where it occurred.
 */
class ErrorContext
{
    public function __construct(
        public readonly string $message,
        public readonly string $file,
        public readonly int $line,
        public readonly string $snippet,
        public readonly array $stackTrace
    ) {
    }

    /**
     * Creates an ErrorContext by reading the file where the error occurred.
     */
    public static function fromThrowable(\Throwable $e, int $snippetRange = 10): self
    {
        $file = $e->getFile();
        $line = $e->getLine();
        $snippet = self::extractSnippet($file, $line, $snippetRange);

        return new self(
            $e->getMessage(),
            $file,
            $line,
            $snippet,
            $e->getTrace()
        );
    }

    private static function extractSnippet(string $file, int $targetLine, int $range): string
    {
        if (!file_exists($file)) {
            return "File not found: $file";
        }

        $lines = file($file);
        $start = max(0, $targetLine - $range - 1);
        $end = min(count($lines), $targetLine + $range);

        $snippet = "";
        for ($i = $start; $i < $end; $i++) {
            $lineNumber = $i + 1;
            $prefix = ($lineNumber === $targetLine) ? ">>> " : "    ";
            $snippet .= sprintf("%d: %s%s", $lineNumber, $prefix, $lines[$i]);
        }

        return $snippet;
    }
}
