<?php

namespace YakNet\Medikit;

/**
 * Represents the diagnosis and fix suggested by the AI.
 */
class Diagnosis
{
    public function __construct(
        public readonly string $explanation,
        public readonly string $suggestedFix,
        public readonly float $confidence = 0.0,
        /** @var array<string, mixed> */
        public readonly array $meta = []
    ) {
    }
}
