<?php

namespace PTS\Models;

class Image extends Model
{
    use Prototype\Model {
        delete as deleteFromDb;
    }

    const PER_PAGE = 50;

    const IMAGE_DIR =  '/images';

    const THUMB_DIR = '/images/thumbs';

    const IMAGE_MAX_SIZE = 5242880;

    const THUMB_HEIGHT = 200;

    protected $table = 'images';

    protected $primary_key = 'f_id';

    protected $hasDefault = ['f_views', 'f_alt'];

    public static function loadImages($page = 0, $per_page = self::PER_PAGE)
    {
        $records = self::load()->paginate($per_page, $page);
        return $records;
    }

    public function offsetExists($offset)
    {
        if ($offset == 'f_thumb_name') return true;
        return parent::offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        $function = __FUNCTION__ . '_' . $offset;
        if (method_exists($this, $function)) {
            return  $this->$function($offset);
        }
        return parent::offsetGet($offset);
    }

    public function __get($name)
    {
        $function = 'offsetGet_' . $name;
        if (method_exists($this, $function)) {
            return $this->$function($name);
        }
        return parent::__get($name);
    }

    public function offsetGet_f_thumb_name($offset)
    {
        $path = self::THUMB_DIR . '/' . $this->attributes['f_real_name'];
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
            return $path;
        }
        else return $this->f_real_name;
    }

    public function offsetGet_f_real_name($offset)
    {
        return self::IMAGE_DIR . '/' . $this->attributes[$offset];
    }

    public function delete()
    {
        $path = $this->getFullPath();
        if (file_exists($path)) {
            unlink($path);
        }

        $path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->f_thumb_name;
        if (file_exists($path)) {
            unlink($path);
        }

        $this->deleteFromDb();
    }

    public function makeThumb()
    {
        $file = $this->getFullPath();
        $pathToSave = $_SERVER['DOCUMENT_ROOT'] . self::THUMB_DIR;
        $src_size = getimagesize($file);
        $new_size = [
            self::THUMB_HEIGHT,
            (int) ($src_size[1] / $src_size[0] * self::THUMB_HEIGHT)
        ];
        $file_name = basename($file);

        $type = explode('/', $src_size['mime'])[1];
        $function = 'imagecreatefrom' . $type;
        if (function_exists($function)){
            $img = $function($file);
            $new = imagecreatetruecolor(... $new_size);

            imagecopyresampled($new, $img,0,0, 0,0, $new_size[0], $new_size[1], $src_size[0], $src_size[1]);

            $function = 'image' . $type;
            $function($new, $pathToSave . DIRECTORY_SEPARATOR . $file_name, 90);
            imagedestroy($new);
        }
    }

    protected function getFullPath()
    {
        return $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->f_real_name;
    }

}
