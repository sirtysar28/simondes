<?php

namespace App\Http\Controllers;

use App\Support\ActivityLogger;
use App\Support\ArtisanWhitelist;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class TerminalController extends Controller
{
    public function index(): View
    {
        return view('terminal.index', ['commands' => ArtisanWhitelist::commands()]);
    }

    /**
     * Menjalankan artisan dari daftar putih — tanpa shell, tanpa input bebas,
     * tanpa eval/system/exec sehingga aman dari injeksi perintah.
     */
    public function run(Request $request)
    {
        $request->validate(['command' => ['required', 'string', 'max:50'], 'confirm' => ['nullable']]);

        $item = ArtisanWhitelist::find($request->input('command'));

        if (! $item) {
            return back()->withErrors(['command' => 'Perintah tidak ada di daftar putih.']);
        }

        if (! empty($item['destructive']) && ! $request->boolean('confirm')) {
            return back()->withErrors(['command' => 'Centang kotak konfirmasi untuk perintah destruktif.']);
        }

        $php = (new PhpExecutableFinder)->find() ?: 'php';
        $artisan = base_path('artisan');

        $process = new Process(array_merge([$php, $artisan], $item['command']), base_path(), null, null, 180);
        $process->run();

        ActivityLogger::log('terminal', 'Terminal', 'Jalankan: '.implode(' ', $item['command']).' — exit '.$process->getExitCode());

        return back()->with([
            'terminal_output' => $process->getOutput().($process->getExitCode() !== 0 ? "\n[ERROR] ".$process->getErrorOutput() : ''),
            'terminal_ran' => $item['label'],
            'terminal_exit' => $process->getExitCode(),
        ]);
    }
}
