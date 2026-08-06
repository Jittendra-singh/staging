<?php

namespace App\Support;

final class TicketFeeCalculator
{
    private static function cents(float|int|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private static function dollars(int $cents): float
    {
        return round($cents / 100, 2);
    }

    private static function percentageFee(int $baseCents, float|int|string $percentage): int
    {
        return (int) round($baseCents * ((float) $percentage) / 100);
    }

    public static function ticket(
        float|int|string $basePrice,
        float|int|string $platformPercentage,
        float|int|string $processingPercentage,
        bool $buyerPaysFees
    ): array {
        $baseCents       = self::cents($basePrice);
        $platformCents   = self::percentageFee($baseCents, $platformPercentage);
        $processingCents = self::percentageFee($baseCents, $processingPercentage);

        $attendeeCents = $buyerPaysFees
            ? $baseCents + $platformCents + $processingCents
            : $baseCents;

        $organizerCents = $buyerPaysFees
            ? $baseCents
            : $baseCents - $platformCents - $processingCents;

        return [
            'base_price'            => self::dollars($baseCents),
            'platform_fee'          => self::dollars($platformCents),
            'processing_fee'        => self::dollars($processingCents),
            'attendee_total'        => self::dollars($attendeeCents),
            'organizer_payout'      => self::dollars($organizerCents),
            'application_fee'       => self::dollars($platformCents + $processingCents),

            // Useful for Stripe API calls.
            'base_cents'            => $baseCents,
            'platform_fee_cents'    => $platformCents,
            'processing_fee_cents'  => $processingCents,
            'attendee_total_cents'  => $attendeeCents,
            'organizer_payout_cents'=> $organizerCents,
            'application_fee_cents' => $platformCents + $processingCents,
        ];
    }

    public static function donation(
        float|int|string $donationAmount,
        float|int|string $processingPercentage,
        bool $buyerPaysFees
    ): array {
        $donationCents   = self::cents($donationAmount);
        $processingCents = self::percentageFee($donationCents, $processingPercentage);

        $attendeeCents = $buyerPaysFees
            ? $donationCents + $processingCents
            : $donationCents;

        $organizerCents = $buyerPaysFees
            ? $donationCents
            : $donationCents - $processingCents;

        return [
            'donation_amount'       => self::dollars($donationCents),
            'platform_fee'          => 0.00,
            'processing_fee'        => self::dollars($processingCents),
            'attendee_total'        => self::dollars($attendeeCents),
            'organizer_payout'      => self::dollars($organizerCents),
            'application_fee'       => self::dollars($processingCents),

            'donation_cents'        => $donationCents,
            'processing_fee_cents'  => $processingCents,
            'attendee_total_cents'  => $attendeeCents,
            'organizer_payout_cents'=> $organizerCents,
            'application_fee_cents' => $processingCents,
        ];
    }
}