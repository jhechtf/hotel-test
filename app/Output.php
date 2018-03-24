<?php
/**
 * Class Ouput
 *  Defines the way that the output classes should behave.
 */

namespace App;

abstract class Output
{
    
    public $extension;
    public $description;
    abstract public function convert($data, $filename);
}
