<?php

use Illuminate\Support\Facades\Artisan;

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

        $errors = [];
        $success = [];
        foreach ($contents as $key=> $item) {


            if ($key === 0) continue;


            $customer = [
                "name"=>null,
                "surname"=>null,
                "email"=>null,
                "location"=>"null",
                "country_code"=>null,
            ];

            //  Name
            if (isset($item[1])) {
                $customer["name"] = explode(' ',$item[1])[0] ?? null;
                $customer["surname"] = explode(' ',$item[1])[1] ?? null;
            }

            $customer["email"] = $item[2] ?? null;

            //  Age to date

            $error_age = false;
            if (isset($item[3])) {
                if (is_numeric($item[3]) && ($item[3] > 18 && $item[3] < 99)) {
                    $customer["age"] = \Carbon\Carbon::now()->subYears($item[3])->toDateString();
                } else {
                    $error_age = true;
                }
            }

            //  Location and Code
            if(isset($item[4])) {
                $location = $item[4] ?? null;
                $country_codes = collect(json_decode(\Storage::get('country_codes.json')));

                $customer["location"] = $location;
                $customer["country_code"] = $country_codes->where('name',$location)->first()->code ?? null;
            }


            $validator = \Validator::make($customer,
                [
                    'name' => 'string|required|max:255',
                    'surname' => 'string|required|max:255',
                    'email' => 'email:rfc,dns|required|max:255|unique:customers',
                    'age' => 'date|required',
                    'location' => 'string|required|min:1|max:255',
                    'country_code' => 'string|required|max:3',
                ],
                []
            );


            $validator->after(function($validator) use ($error_age)
            {
                if ($error_age) {
                    $validator->errors()->add('age', 'Age must be a numeric from 18 to 99!');
                }
            });

            if ($validator->fails()) {
                $errors[$key] = [
                    "line" => implode($separator, $item),
                    "errors" => implode(",",$validator->messages()->all()),
                ];
            } else {
                $success[] = $customer;
            }

            //  Create if only location error
            if (($validator->errors()->has("location") && !$validator->errors()->has("email") && $validator->errors()->count() > 1)) {
                $customer['location'] = "Unknown";
                $success[] = $customer;
            }
        }

        if ($success) {
            foreach ($success as $customer) {
                \App\Models\Customer::create($customer);
            }
            $this->comment("Success added ". count($success) ." to DB");
        } else {
            $this->comment("Nothing added to DB");
        }


        if ($errors) {
            Excel::store(new \App\Exports\ErrorsExport($errors), 'failed.xlsx', 'local');
            $this->error("Total errors lines ". count($errors) . " Your report saved to /storage/app/failed.xlsx");
        }

    } else {
        $this->error("File '$filename' not fount in /storage/app");
    }


});
