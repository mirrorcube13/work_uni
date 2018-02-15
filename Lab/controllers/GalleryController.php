<?php

namespace PTS\Controllers;

use PTS\Core\Request;
use PTS\Core\View;
use PTS\Models\Dictionary;
use PTS\Models\Extension;
use PTS\Models\Image;

class GalleryController
{
    public function index($page = 0)
    {
        $extensions = Extension::load()->get();
        $ext_str = implode(',', $extensions);
        $vars = [
            'extensions' => $ext_str
        ];

        $images = Image::load()->orderBy('f_views','desc')->paginate(16, $page);
        $vars += [
            'images' => $images
        ];

        $view = new View('gallery', $vars);

        $required = $view->getRequiredConfigVars();
        $vars = Dictionary::getDictionaryValues($required);
        $view->setConfigVars($vars);
        $view->processView();
        return $view;
    }

    public function uploadPhoto()
    {
        $photo = $_FILES['photo'];
        if (!$photo['error']) {
            $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
            if (Extension::isAvailable($ext) && $photo['size'] < Image::IMAGE_MAX_SIZE) {
                $image = Image::create();
                $params = Request::getAll();
                if ($params['title']) {
                    $image->f_title = $params['title'];
                } else {
                    $image->f_title = $photo['name'];
                }

                if ($params['img-alt']) {
                    $image->f_alt = $params['img-alt'];
                }

                $new_name = sha1('s' . time()) . '.' . $ext;
                $path = $_SERVER['DOCUMENT_ROOT'] . Image::IMAGE_DIR . DIRECTORY_SEPARATOR . $new_name;

                if(move_uploaded_file($photo['tmp_name'], $path)) {
                    $image->f_real_name = $new_name;
                    $image->f_size = $photo['size'];
                    $image->f_uploaded_at = time();
                    $image->makeThumb();
                    $image->saveToDb();

                    return Request::back('/photo/' . $image->f_id);
                }
            }
        }
        return Request::back();
    }
}
