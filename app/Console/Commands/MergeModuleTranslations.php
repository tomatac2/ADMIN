<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MergeModuleTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:merge-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge all JSON translation files (en.json, ar.json) from all modules into /lang folder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modulesPath = base_path('Modules');
        $languages = ['en', 'ar'];
        $merged = [];

        foreach ($languages as $lang) {
            $this->info("🔍 Scanning {$lang} static.php files...");
            $merged[$lang] = [];

            // 1️⃣ Traverse all modules
            foreach (File::directories($modulesPath) as $module) {
                $file = "{$module}/lang/{$lang}/static.php";
                if (File::exists($file)) {
                    $this->line(" ➕ Found: " . basename($module) . "/lang/{$lang}/static.php");
                    $arr = include $file;
                    if (is_array($arr)) {
                        $merged[$lang] = array_merge($merged[$lang], $arr);
                    }
                }
            }

            // 2️⃣ Write merged result to lang/{lang}.json
            $targetFile = base_path("lang/{$lang}.json");
            File::ensureDirectoryExists(base_path('lang'));

            File::put(
                $targetFile,
                json_encode($merged[$lang], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            );

            $this->info("✅ Merged JSON saved to: lang/{$lang}.json");
        }

        $this->info('🎉 All translations merged successfully into JSON!');
    }
}
