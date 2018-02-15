<?php

namespace PTS\Core;

class View
{
    public const CV_TABLE = 'dictionary';
    protected const VIEWS_FOLDER = 'views';
    protected const TEMPLATE_EXT = '.tpl';
    protected $dirPath;

    protected const TEMPLATE_NOT_FOUND  = 'Template {name} not found';
    protected const CV_VARS_NOT_FOUND   = 'Required config vars {vars} not found';
    protected const DV_VARS_NOT_FOUND   = 'Required dynamic vars {vars} not set in parameters';
    protected const ARRAY_NOT_SET       = 'Array {name} not set in parameters';

    protected const INCLUDE_PATTERN = '@{FILE=\"(?P<fileName>[/\w\\.]+)\"}@i';
    protected const EXTENDS_PATTERN = '@{EXTENDS=\"([/\w\\.]+)\"}@i';
    protected const SECTION_PATTERN = '@{SECTION=\"(?P<sectionName>[\w.]+)\"}(?P<text>[\w\W]*){ENDSECTION}@iU';
    protected const SLOT_PATTERN    = '@{SLOT=\"(?P<slotName>[\w.]+)\"}@i';
    protected const IF_PATTERN      = '@{IF"[\w]+"==\"[\w]+\"}@i';
    protected const ENDIF_PATTERN   = '@{ENDIF}@i';
    protected const CONFIG_VARS_PATTERN = '@{CV=\"(?P<cv_name>\w+)\"}@';
    protected const CYCLE_PATTERN   = '@{CYCLE=\"(?P<arrayName>\w+)\";\s*FILE=\"(?P<fileName>[/\w\\.]+)\"}@';
    protected const CYCLE_VARS_PATTERN = '@{(CYV)=\"(?P<cyv_name>\w+)\"}@';
    protected const DYNAMIC_VARS_PATTERN = '@{[dD][vV]=\"(?P<dv_name>\w+)\"}@';

    protected $params;
    protected $viewText;
    protected $db;

    protected $sections = [];
    protected $configVars = [];
    protected $dynamicVars = [];


    public function __construct(string $viewName, $params = [])
    {
        $this->dirPath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . self::VIEWS_FOLDER . DIRECTORY_SEPARATOR;
        $fullPath = $this->dirPath . $viewName . self::TEMPLATE_EXT;

        if (file_exists($fullPath)){
            $this->viewText = file_get_contents($fullPath);
            if (!empty($params)) $this->params = $params;
        }
        else throw new \Exception(str_replace('{name}', $fullPath, self::TEMPLATE_NOT_FOUND));

        $this->processExtends();

        return $this;
    }

    /**
     * Checks if template extends another template.
     * Each template can extend only ONE template!
     * Data placed between {SECTION="section_name"} and {ENDSECTION} will only be used.
     * Data from "section_name" will be places in parent's {SLOT="section_name"}.
     */
    protected function processExtends(){
        while (preg_match(self::EXTENDS_PATTERN, $this->viewText, $extends)) {

            //Collect sections to array
            if (preg_match_all(self::SECTION_PATTERN, $this->viewText, $sections)){
                $nOfSections = count($sections['sectionName']);

                for ($i = 0 ; $i < $nOfSections; $i++){
                    $this->sections [$sections['sectionName'][$i]] = $sections['text'][$i];
                }
            }

            //loading parent view
            $this->viewText = $this->getTemplateFile($extends[1]);

            //putting sections in its slots
            while (preg_match(self::SLOT_PATTERN,$this->viewText,$match)){
                $slotName = $match['slotName'];

                if (isset($this->sections[$slotName])){
                    $this->viewText = str_replace($match[0], $this->sections[$slotName], $this->viewText);
                    unset($this->sections[$slotName]);
                }else{
                    $this->viewText = str_replace($match[0], '', $this->viewText);
                }
            }
        }
        while (preg_match(self::SLOT_PATTERN,$this->viewText,$match)){
            $this->viewText = str_replace($match[0], '', $this->viewText);
        }
    }

    /**
     * Loads template from file using template name without extension
     * @param string $templateName
     * @return string
     * @throws \Exception
     */
    protected function getTemplateFile(string $templateName) : string {
        $path = $this->dirPath . $templateName . self::TEMPLATE_EXT;
        if (is_file($path)) return file_get_contents($path);
        else throw new \Exception( str_replace('{name}', $path, self::TEMPLATE_NOT_FOUND));
    }

    public function processView(){

        $this->processExtends();

        while ( preg_match(self::INCLUDE_PATTERN, $this->viewText)      ||
                preg_match(self::CONFIG_VARS_PATTERN, $this->viewText)  ||
                preg_match(self::DYNAMIC_VARS_PATTERN, $this->viewText) ||
                preg_match(self::CYCLE_PATTERN, $this->viewText)){

            $this->processIncludes();

            $this->processCycles();

            $this->processConfigVars();

            $this->processDynamicVars();
        }

        return $this;
    }


    protected function putVarsInPlaceHolders(array $vars){
        if (!empty($vars)){

            //putting vars in its placeholders
            foreach ($vars as $placeHolder => $value){
                $this->viewText = str_replace($placeHolder, $value, $this->viewText);
            }
        }
    }

    protected function processDynamicVars(){
        $this->dynamicVars = $this->loadDynamicVars();
        $this->putVarsInPlaceHolders($this->dynamicVars);
    }

    protected function loadDynamicVars() : array {
        if (0 !== preg_match_all(self::DYNAMIC_VARS_PATTERN, $this->viewText, $matches)){

            $matches['dv_name'] = array_unique($matches['dv_name']);
            $matches[0] = array_unique($matches[0]);

            //making array [ 'dv_VAR_placeholder' => 'dv_var_name']
            $dv_vars = array_combine($matches[0], $matches['dv_name']);

            //finding dynamic vars in params array and putting them in dynamicVars array
            $dynamicVars = [];
            foreach ($dv_vars as $placeHolder => $dvName){
                if (isset($this->params[$dvName])){
                    $dynamicVars[$placeHolder] = $this->params[$dvName];
                    unset($this->params[$dvName]);
                    unset($dv_vars[$placeHolder]);
                }
            }

            //if we didn't find all required values for dynamic vars
            if ( !empty($dv_vars)){

                $dv_vars = '[ ' . implode(', ', array_keys($dv_vars)) . ' ]';

                throw new \Exception( str_replace('{vars}', $dv_vars, self::DV_VARS_NOT_FOUND) );

            }

            return $dynamicVars;
        }
        else return [];
    }

    /**
     * Process cycle statements like
     * {CYCLE="required_array_name"; FILE="path_to_iteration_template"}
     *
     * Iteration template may have cycle variables like
     * {CYV="cycle_variable_name"}
     *
     * required array must be like
     * [
     *  ['cycle_variable_name1' => 'cycle_variable_value1', 'cycle_variable_name2' => 'cycle_variable_value2', ...],
     *  ['cycle_variable_name1' => 'cycle_variable_value3', 'cycle_variable_name2' => 'cycle_variable_value4', ...],
     *  ...
     * ]
     *
     * if array is broken or not supplied
     * @throws \Exception
     */
    protected function processCycles(){
        if (0 !== ( $nOfMatches = preg_match_all(self::CYCLE_PATTERN, $this->viewText, $matches) ) ){

            for ($i = 0; $i < $nOfMatches; $i++){

                //Name of array required for cycle
                $arrayName = $matches['arrayName'][$i];
                if ( !empty($this->params[$arrayName]) && is_array($this->params[$arrayName]) ){

                    $cycleResult = '';

                    //Load placeholder for cycle's iteration and parse it's vars
                    $placeHolder = $this->getTemplateFile($matches['fileName'][$i]);
                    preg_match_all(self::CYCLE_VARS_PATTERN, $placeHolder, $cyvMatches);

                    //Get array('cycle_var_name' => cycle_var_placeholder)
                    $cycleVars = array_combine($cyvMatches['cyv_name'], $cyvMatches[0]);

                    //get line of values of cycle variables
                    foreach ($this->params[$arrayName] as $line){

                        $cycleString = $placeHolder;

                        //replace each cycle's variable placeholder in iteration placeholder
                        foreach ($cycleVars as $cycleVar => $cyvPlaceholder){
                            if (!isset($line[$cycleVar])) throw new \Exception(str_replace('{name}', $matches['arrayName'][$i], self::ARRAY_NOT_SET) );
                            $cycleString = str_replace($cyvPlaceholder, $line[$cycleVar], $cycleString);
                        }
                        $cycleResult .= $cycleString;

                    }

                    $this->viewText = str_replace($matches[0][$i], $cycleResult, $this->viewText);
                }
                else {
                    //throw new Exception(str_replace('{name}', $matches['arrayName'][$i], self::ARRAY_NOT_SET) );
                    $this->viewText = str_replace($matches[0][$i], '', $this->viewText);
                }

            }
        }
    }

    /**
     * Returns array of required config vars names for a view
     * @return array
     * or empty array if config vars is not required
     */
    public function getRequiredConfigVars() : array {
        if (0 !== preg_match_all(self::CONFIG_VARS_PATTERN, $this->viewText, $matches)){

            $matches['cv_name'] = array_unique($matches['cv_name']);
            $matches[0] = array_unique($matches[0]);
            // Making array [ 'CV_VAR_placeholder' => 'cv_var_name'].
            $vars_from_tpl = array_combine($matches[0], $matches['cv_name']);

            foreach ($vars_from_tpl as $placeholder => $value) {
                if (!isset($this->configVars[$placeholder]) || $this->configVars[$placeholder] !== $value) {
                    $this->configVars[$placeholder] = $value;
                }
                else{
                    unset($vars_from_tpl[$placeholder]);
                }
            }

            return $vars_from_tpl;

        }
        else return [];
    }

    /**
     * Sets config vars. Requires array ['config_var_name' => 'config_var_value']
     *
     * @param array $configVars
     * @return void
     * @throws \Exception
     */
    public function setConfigVars(array $configVars) : void {

        $required_vars = array_filter(array_values($this->configVars), function ($var) use ($configVars){
            if (!isset($configVars[$var])) return true;
            else return false;
        });

        if (!empty($required_vars)) {
            $required_vars = '[ ' . implode(', ', $required_vars ) . ' ]';

            throw new \Exception( str_replace('{vars}', $required_vars, self::CV_VARS_NOT_FOUND) );
        }

        foreach ($this->configVars as $placeHolder => $cv_name){
            if (isset($configVars[$cv_name])) {
                $this->configVars[$placeHolder] = $configVars[$cv_name];
            }
        }

    }

    protected function processConfigVars()
    {
        if ($this->configVars) {
            $this->putVarsInPlaceHolders($this->configVars);
        } else {
            throw new \Exception( str_replace('{vars}', '[' . implode('] [', $vars) . ']',
                self::CV_VARS_NOT_FOUND));
        }
    }

    protected function processIncludes()
    {
        while (preg_match(self::INCLUDE_PATTERN, $this->viewText, $matches)) {
            $this->viewText = str_replace(
                $matches[0],
                $this->getTemplateFile($matches['fileName']),
                $this->viewText);
        }

    }

    public function show(){
        return $this->viewText;
    }

    public static function isView($viewName) : bool {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . self::VIEWS_FOLDER . DIRECTORY_SEPARATOR . $viewName . self::TEMPLATE_EXT;
        if (file_exists($filePath) && is_file($filePath)) return true;
        else return false;
    }
}