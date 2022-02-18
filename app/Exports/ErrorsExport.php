<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ErrorsExport implements FromCollection
{
    /**
     * @var array|mixed
     */
    private mixed $data;

    public function __construct($errors = [])
    {
        $this->data = $errors;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->data);
    }
}
