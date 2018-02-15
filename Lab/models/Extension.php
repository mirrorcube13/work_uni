<?php

namespace PTS\Models;

class Extension extends Model
{
    use Prototype\Model;

    protected $table = 'extensions';

    protected $primary_key = 'ext_id';

    public function __toString()
    {
        return '.' . $this->ext_name;
    }

    public static function isAvailable(string $ext)
    {
        $extensions = self::load()->get();
        $extensions = implode(',', $extensions);
        if (strpos($extensions, $ext) === false) {
            return false;
        } else {
            return true;
        }
    }
}
