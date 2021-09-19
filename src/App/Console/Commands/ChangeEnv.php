<?php

namespace  Sislamrafi\Webartisan\App\Console\Commands;

use Illuminate\Console\Command;

class ChangeEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:save {var} {value} {--del}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change values on env files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    private static function envUpdate($key, $value, $del)
    {
        $path = base_path('.env');

        //return  ' '.is_string($value).' '.is_string($key);

        if (!$del) {
            $replaceStr = $key . '=' . $value;
        }else{
            $replaceStr = "";
        }

        $count = 0;
        if (file_exists($path)) {
            if($count == 0 && !empty(env($key))){
                $fileStr = str_replace(
                    $key . '=' . env($key), $replaceStr, file_get_contents($path),$count
                );
            }
            if($count == 0 && empty(env($key))){
                $fileStr = str_replace(
                    $key . '=' . 'false', $replaceStr, file_get_contents($path),$count
                );
            }
            if($count == 0 && empty(env($key))){
                $fileStr = str_replace(
                    $key . '=' . '0', $replaceStr, file_get_contents($path),$count
                );
            }
            if($count == 0 && env($key)==1){
                $fileStr = str_replace(
                    $key . '=' . 'true', $replaceStr, file_get_contents($path),$count
                );
            }
            //return $key . '=' . empty(env($key)). ';Count:'. $count;
            if ($count>0) {
                file_put_contents($path, $fileStr);
                return $del? '.ENV variable deleted.' :'.ENV variable changed.';
            }else if(!$del){
                $fileStr.="\r\n".$key . '=' . $value."\r\n";
                file_put_contents($path, $fileStr);
                return '.ENV variable added.';
            }else{
                return '.ENV variable not found';
            }
        }
    }

    public function handle()
    {
        $this->info($this->envUpdate($this->argument('var'),$this->argument('value'),$this->option('del')));
        return 0;
    }
}
