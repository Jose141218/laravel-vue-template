<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait HasDateFormats
{
    /**
     * Formatea una fecha en representaciones raw, formatted, human y diff.
     *
     * @return array{raw: string, formatted: string, human: string, diff: string}|null
     */
    protected function formatDate(Carbon|string|null $date, bool $withTime = true): ?array
    {
        if (! $date) {
            return null;
        }

        if (! $date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        $date = $date->locale('es');

        return [
            'raw' => $withTime ? $date->toDateTimeString() : $date->toDateString(),
            'formatted' => $date->format($withTime ? 'd/m/Y H:i' : 'd/m/Y'),
            'human' => $date->translatedFormat($withTime ? 'd \d\e F \d\e Y, g:i a' : 'd \d\e F \d\e Y'),
            'diff' => $date->diffForHumans(),
        ];
    }
}
