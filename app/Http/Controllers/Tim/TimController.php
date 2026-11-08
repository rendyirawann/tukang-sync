<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Http\Controllers\Controller;
use App\Models\Team;

/** Dasar controller panel tim. */
abstract class TimController extends Controller
{
    protected function tim(): Team
    {
        $tim = request()->route('tim');

        return $tim instanceof Team ? $tim : Team::where('slug', Team::normalkanSlug((string) $tim))->firstOrFail();
    }

    /** 403 bila izin tidak dimiliki. */
    protected function izin(bool $boleh): void
    {
        abort_unless($boleh, 403, 'Anda tidak punya hak akses untuk tindakan ini.');
    }

    /** 404 bila anak tidak milik induk yang ada di URL. */
    protected function milik(bool $cocok): void
    {
        abort_unless($cocok, 404);
    }
}
