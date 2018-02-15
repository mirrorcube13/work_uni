<?php

namespace PTS\Controllers;

use PTS\Core\View;
use PTS\Models\Dictionary;

class ErrorController
{
    public function errorPage($error_code)
    {
        $viewName = "error/{$error_code}";
        if (View::isView($viewName)){
            $view = new View($viewName);
            $view_text = Dictionary::getDictionaryValues($view->getRequiredConfigVars());
            $view->setConfigVars($view_text);
            return $view->processView();
        }
        return null;
    }
}