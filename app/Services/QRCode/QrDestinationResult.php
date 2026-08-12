<?php

namespace App\Services\QRCode;

final class QrDestinationResult
{
    private function __construct(
        public readonly bool $ok,
        public readonly ?string $url = null,
        public readonly ?string $destinationType = null,
        public readonly ?int $destinationLinkId = null,
        public readonly string $reason = 'unavailable',
    ) {}

    public static function ok(string $url, string $destinationType, ?int $destinationLinkId = null): self
    {
        return new self(true, $url, $destinationType, $destinationLinkId, 'ok');
    }

    public static function unavailable(string $reason): self
    {
        return new self(false, null, null, null, $reason);
    }
}
