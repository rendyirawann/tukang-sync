<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class RabBaseController extends Controller
{
    /** Melempar 403 berformat envelope bila izin tidak dipenuhi. */
    protected function wajib(bool $boleh): void
    {
        if (! $boleh) {
            throw new HttpResponseException($this->tolak());
        }
    }

    protected function tolak(): JsonResponse
    {
        return ApiResponse::error('Anda tidak punya hak akses untuk tindakan ini.', 403, code: 'FORBIDDEN');
    }

    /** ID pengguna saat ini (UUID). */
    protected function userId(Request $request): ?string
    {
        return $request->user()?->id;
    }
}
