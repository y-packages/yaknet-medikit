<?php

namespace YakNet\Medikit;

use YakNet\Medikit\Context\ErrorContext;
use YakNet\Medikit\Healer\GeminiHealer;

/**
 * The core Medikit class that handles runtime healing.
 */
class Medikit
{
    private static ?self $instance = null;
    private ?GeminiHealer $healer = null;

    private function __construct(
        private readonly string $apiKey,
        private readonly bool $autoHealing = false
    ) {
        $this->healer = new GeminiHealer($this->apiKey);
    }

    /**
     * Initializes the Medikit immunity system.
     */
    public static function register(?string $apiKey = null, bool $autoHealing = false): self
    {
        // Check for .env and debug mode
        $debugMode = self::checkDebugMode();
        if (!$debugMode) {
            return new self('', false); // Dummy instance, won't register handler
        }

        $apiKey = $apiKey ?? $_ENV['GEMINI_API_KEY'] ?? (getenv('GEMINI_API_KEY') ?: null) ?? throw new \InvalidArgumentException('Gemini API Key is required for Medikit.');

        if (self::$instance === null) {
            self::$instance = new self($apiKey, $autoHealing);
            set_exception_handler([self::$instance, 'handleException']);
        }
        return self::$instance;
    }

    private static function checkDebugMode(): bool
    {
        // Simple .env check in current or parent directories
        $envPath = getcwd() . '/.env';
        if (file_exists($envPath)) {
            $content = file_get_contents($envPath);
            return str_contains($content, 'MEDIKIT_DEBUG=true');
        }
        return false;
    }

    /**
     * Handles uncaught exceptions by diagnosing them.
     */
    public function handleException(\Throwable $e): void
    {
        // Don't interrupt if we are already in a healing loop
        static $isHealing = false;
        if ($isHealing) return;
        $isHealing = true;

        $context = ErrorContext::fromThrowable($e);
        $diagnosis = $this->healer->diagnose($context);

        $this->renderReport($context, $diagnosis, $e);

        // Optionally, we could exit here or re-throw
        exit(1);
    }

    private function renderReport(ErrorContext $context, Diagnosis $diagnosis, \Throwable $originalError): void
    {
        $isCli = PHP_SAPI === 'cli';
        
        if ($isCli) {
            $this->renderCliReport($context, $diagnosis, $originalError);
        } else {
            $this->renderHtmlReport($context, $diagnosis, $originalError);
        }
    }

    private function renderCliReport(ErrorContext $context, Diagnosis $diagnosis, \Throwable $e): void
    {
        echo "\n\e[41m  CRITICAL ERROR DETECTED  \e[0m\n";
        echo "\e[31mMessage: {$e->getMessage()}\e[0m\n";
        echo "Location: {$e->getFile()}:{$e->getLine()}\n\n";

        echo "\e[44m  MEDIKIT DIAGNOSIS  \e[0m\n";
        echo "\e[34mExplanation:\e[0m {$diagnosis->explanation}\n\n";
        
        if ($diagnosis->suggestedFix) {
            echo "\e[32mSuggested Fix:\e[0m\n";
            echo "----------------------------------------\n";
            echo $diagnosis->suggestedFix . "\n";
            echo "----------------------------------------\n";

            echo "\e[33mWould you like me to attempt a repair? (y/n): \e[0m";
            $answer = strtolower(trim(fgets(STDIN)));
            if ($answer === 'y') {
                echo "\e[32m[MEDIKIT] Repairing process initiated... (Feature in development)\e[0m\n";
                // Future: Implement actual file patching here
            } else {
                echo "\e[31m[MEDIKIT] Repair cancelled by user.\e[0m\n";
            }
        }
        echo "\n";
    }

    private function renderHtmlReport(ErrorContext $context, Diagnosis $diagnosis, \Throwable $e): void
    {
        // For web, we'll output a clean, premium diagnostic page
        if (!headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        require __DIR__ . '/Resources/report_template.php';
    }
}
