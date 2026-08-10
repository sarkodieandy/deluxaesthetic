<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandStatus;

class SecureAcademyFiles extends Command
{
    /**
     * @var array<string, list<string>>
     */
    private const FILE_COLUMNS = [
        'course_materials' => ['file_path'],
        'assignments' => ['attachment_path'],
        'assignment_submissions' => ['file_path'],
        'certificates' => ['pdf_path', 'qr_path'],
    ];

    protected $signature = 'academy:secure-files
        {--dry-run : Report files that need moving without changing storage}';

    protected $description = 'Move Academy documents out of public storage into the protected Academy disk';

    public function handle(): int
    {
        $private = Storage::disk('academy_private');
        $public = Storage::disk('public');
        $dryRun = (bool) $this->option('dry-run');
        $moved = 0;
        $secured = 0;
        $missing = 0;
        $failed = 0;

        foreach (self::FILE_COLUMNS as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::table($table)
                    ->select(['id', $column])
                    ->whereNotNull($column)
                    ->where($column, '!=', '')
                    ->orderBy('id')
                    ->chunkById(100, function ($records) use (
                        $column,
                        $dryRun,
                        $private,
                        $public,
                        &$moved,
                        &$secured,
                        &$missing,
                        &$failed,
                    ): void {
                        foreach ($records as $record) {
                            $path = $this->safeRelativePath((string) $record->{$column});

                            if ($path === null) {
                                $this->warn("Skipped unsafe Academy file path on record {$record->id}.");
                                $failed++;

                                continue;
                            }

                            $privateExists = $private->exists($path);
                            $publicExists = $public->exists($path);

                            if (! $privateExists && ! $publicExists) {
                                $missing++;

                                continue;
                            }

                            if ($dryRun) {
                                if ($publicExists) {
                                    $moved++;
                                } else {
                                    $secured++;
                                }

                                continue;
                            }

                            if (! $privateExists) {
                                $stream = $public->readStream($path);

                                if (! is_resource($stream)) {
                                    $this->error("Could not read public Academy file: {$path}");
                                    $failed++;

                                    continue;
                                }

                                try {
                                    $written = $private->put($path, $stream);
                                } finally {
                                    fclose($stream);
                                }

                                if (! $written || ! $private->exists($path)) {
                                    $this->error("Could not copy Academy file to protected storage: {$path}");
                                    $failed++;

                                    continue;
                                }

                                $moved++;
                            } else {
                                $secured++;
                            }

                            if ($publicExists && (! $public->delete($path) || $public->exists($path))) {
                                $this->error("Protected copy exists but the public Academy file could not be removed: {$path}");
                                $failed++;
                            }
                        }
                    });
            }
        }

        $action = $dryRun ? 'would be moved' : 'moved';
        $this->info("Academy files {$action}: {$moved}; already protected: {$secured}; missing: {$missing}; failures: {$failed}.");

        return $failed === 0 ? CommandStatus::SUCCESS : CommandStatus::FAILURE;
    }

    private function safeRelativePath(string $path): ?string
    {
        $path = str_replace('\\', '/', trim($path));

        if ($path === '' || str_starts_with($path, '/') || preg_match('/(^|\/)\.\.(\/|$)/', $path)) {
            return null;
        }

        return $path;
    }
}
