<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertLangFilesToJs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:js';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert all language files named lang/{language}/"ajax" into javascript and place the converted files in resources/assets/js/lang ';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $origDir = "resources/lang/";

        $dir = opendir($origDir);
        if ( ! $dir) {
            die(sprintf("%s could not be read.", $origDir));
        }

        $this->info('The following files were converted:');
        while (($subdir = readdir($dir)) !== false) {
            $langDir = $origDir . $subdir;
            if (is_dir($langDir)) {
                $file = sprintf('%s/ajax.php',$langDir);
                if(is_file($file)){
                    $fh = fopen(sprintf('resources/assets/js/lang/%s.json',$subdir),'w');
                    fwrite($fh,json_encode(include($file)));
                    fclose($fh);
                    $this->info('    - '.$subdir);
                }

            }
        }

        closedir($dir);
    }
}
