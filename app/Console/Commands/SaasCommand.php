<?php

namespace Plugins\LaravelSaas\Console\Commands;

use Illuminate\Console\Command;

class SaasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available commands';

    /**
     * @var string
     * 
     * @see figlet -f slant Laravel SaaS
     */
    public static $logo = <<<LOGO
    __                                __   _____             _____
   / /   ____ __________ __   _____  / /  / ___/____ _____ _/ ___/
  / /   / __ `/ ___/ __ `/ | / / _ \/ /   \__ \/ __ `/ __ `/\__ \
 / /___/ /_/ / /  / /_/ /| |/ /  __/ /   ___/ / /_/ / /_/ /___/ /
/_____/\__,_/_/   \__,_/ |___/\___/_/   /____/\__,_/\__,_//____/
LOGO;
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info(static::$logo);

        $this->comment('');
        $this->comment('Available commands:');

        $this->comment('');
        $this->comment('saas');
        $this->listAdminCommands();
    }

    protected function listAdminCommands(): void
    {
        $commands = collect(\Illuminate\Support\Facades\Artisan::all())->mapWithKeys(function ($command, $key) {
            if (
                \Illuminate\Support\Str::startsWith($key, 'saas')
                || \Illuminate\Support\Str::startsWith($key, 'tenants')
            ) {
                return [$key => $command];
            }

            return [];
        })->toArray();

        \ksort($commands);

        $width = $this->getColumnWidth($commands);

        /** @var Command $command */
        foreach ($commands as $command) {
            $this->info(sprintf(" %-{$width}s %s", $command->getName(), $command->getDescription()));
        }
    }

    private function getColumnWidth(array $commands): int
    {
        $widths = [];

        foreach ($commands as $command) {
            $widths[] = static::strlen($command->getName());
            foreach ($command->getAliases() as $alias) {
                $widths[] = static::strlen($alias);
            }
        }

        return $widths ? max($widths) + 2 : 0;
    }

    /**
     * Returns the length of a string, using mb_strwidth if it is available.
     *
     * @param  string  $string  The string to check its length
     * @return int The length of the string
     */
    public static function strlen($string): int
    {
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return strlen($string);
        }

        return mb_strwidth($string, $encoding);
    }
}
