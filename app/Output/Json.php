<?php
namespace App\Output;

use App\Output;
use Illuminate\Support\Facades\Storage;

class Json extends Output {
    public $extension = 'json';
    public $description = 'JavaScript Object Notation -- a fairly standard web-friendly format for passing or storing data.';
    public function convert($data, $filename){
        file_put_contents($filename, json_encode($data)); 
    }
}