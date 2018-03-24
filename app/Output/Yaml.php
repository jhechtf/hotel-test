<?php
namespace App\Output;

use App\Output;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Yaml\Yaml as sYaml;

class Yaml extends Output {
    public $extension = 'yaml';
    public $description = 'YAML Ain\'t Markup Language -- A superset of JSON used primarily for configuration files.';
    public function convert($data, $filename){
        file_put_contents($filename, sYaml::dump($data) );
    }
}