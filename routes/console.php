<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('makeMove', function () {
    $this->comment("Starting command move file to DB");

    $filename = "random.csv";
    $filename = $this->ask("Enter path to file: e.g. ($filename) or press 'Enter'",$filename);

    if (\Storage::exists($filename)) {

        $separator = $this->choice(
            'Choice a separator?',
            [',', ';',":"],
            ","
        );

        $lines = explode(PHP_EOL, \Storage::get($filename));

        $contents = [];
        foreach ($lines as $key => $line) {
            $contents[] = str_getcsv($line,$separator);
        }


        dd($contents);


        dd($contents);

    } else {
        $this->error("File '$filename' not fount in /storage/app");
    }


});
