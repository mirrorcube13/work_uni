<?php

namespace PTS\Controllers;

use PTS\Core\Request;
use PTS\Core\View;
use PTS\Models\Dictionary;
use PTS\Models\Image;

class PhotoController
{
    public function showPhoto($id = 0)
    {
        if ($id){
            try{
                $image = Image::find($id);
            } catch (\Exception $e) {
                return Request::error(404);
            }

            $image->f_views = $image->f_views + 1;
            $image->saveToDb();

            $view = new View('photo', $image);
            $view->setConfigVars(Dictionary::getDictionaryValues($view->getRequiredConfigVars()));
            return $view->processView();
        }
        return Request::error(404);
    }

    public function editPhoto($id = 0)
    {
        if ($id) {
            try{
                $image = Image::find($id);
            } catch (\Exception $e) {
                return Request::error(404);
            }

            $view = new View('photo-form', $image);
            $view->setConfigVars(Dictionary::getDictionaryValues($view->getRequiredConfigVars()));
            return $view->processView();
        }
        return Request::error(404);
    }

    public function savePhoto($id = 0)
    {
        if ($id) {
            try{
                $image = Image::find($id);
            } catch (\Exception $e) {
                return Request::error(404);
            }

            $params = Request::getAll();
            if ($params['img-title']) {
                $image->f_title = $params['img-title'];
            }
            if ($params['img-alt']) {
                $image->f_alt = $params['img-alt'];
            }
            $image->saveToDb();

            return Request::back('/photo/' . $image->f_id);
        }
        return Request::error(404);
    }

    public function deletePhoto($id = 0)
    {
        if ($id) {
            try{
                $image = Image::find($id);
            } catch (\Exception $e) {
                return Request::error(404);
            }

            $image->delete();
            return Request::back('/');
        }
        return Request::error(404);
    }
}