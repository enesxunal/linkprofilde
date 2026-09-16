<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class VersionController extends Controller
{
    /**
     * Return only the locally installed version.
     * Remote update checks are intentionally disabled.
     */
    public function checkVersion(): array
    {
        return [
            'version' => $this->getCurrentVersion(),
            'update_available' => false,
        ];
    }

    /**
     * Legacy automatic updater is permanently disabled.
     * Updates must be deployed through the normal source/deployment pipeline.
     */
    public function updateVersion()
    {
        return back()->with(
            'info',
            'Otomatik güncelleme devre dışıdır. Güncellemeler kaynak kod ve deploy süreci üzerinden yapılmalıdır.'
        );
    }

    public function getCurrentVersion(): string
    {
        $versionFile = base_path('version.txt');

        if (! File::exists($versionFile)) {
            return 'unknown';
        }

        return trim((string) File::get($versionFile));
    }
}
