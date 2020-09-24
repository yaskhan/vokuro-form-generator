<?php

require 'snippets.php';

class Builder {
    private $data;
    private $formsnippets;
    private $filebody = '';
    private $classbody = '';
    
    public function __construct($json) {
        $this->data = json_decode($json);
        $this->formsnippets = new FormSnippet();
    }
    
    public function build() {
        foreach ($this->data as $data) {
            switch ($data->type) {
                case "text":
                    if($data->subtype == 'text') $this->buildText($data);
                    else if($data->subtype == 'email') $this->buildEmail($data);
                    else if($data->subtype == 'password') $this->buildPassword($data);
                    break;
                    
                case "select":
                    $this->buildSelect($data);
                    break;

                case "checkbox-group":
                    $this->buildCheck($data);
                    break;
                
                case "date":
                    $this->buildDate($data);
                    break;
                        
                case "file":
                    $this->buildFile($data);
                    break;
                        
                case "number":
                    $this->buildNumeric($data);
                    break;

                case "radio-group":
                    $this->buildRadio($data);
                    break;

                case "textarea":
                    $this->buildTextarea($data);
                    break;
                
                default:
                    break;
            };
        }
        echo $this->classbody;
    }
    
    private function buildText($data) {
        $attr = []; $val = [];
        if($data->className) $attr['class'] = $data->className;
        if($data->required) $attr['required'] = $data->required;
        if($data->placeholder) $attr['placeholder'] = $data->placeholder;
        if($data->value) $attr['value'] = $data->value;
        $attr = $this->varexport($attr);
        
        if($data->required)  $val[]['required'] = $data->required;
        if($data->maxlength) $val[]['maxlen'] = $data->maxlength;

        $this->classbody .= $this->formsnippets->genText($data->name, $attr, $val);
    }
    
    private function buildEmail($data) {
        $attr = [];
        if($data->className) $attr['class'] = $data->className;
        if($data->required) $attr['required'] = $data->required;
        if($data->placeholder) $attr['placeholder'] = $data->placeholder;
        $attr = $this->varexport($attr);

        $this->classbody .= $this->formsnippets->genEmail($data->name, $attr);
    }
    
    private function buildPassword($data) {
        $attr = [];
        if($data->className) $attr['class'] = $data->className;
        if($data->required) $attr['required'] = $data->required;
        if($data->placeholder) $attr['placeholder'] = $data->placeholder;
        $attr = $this->varexport($attr);

        $this->classbody .= $this->formsnippets->genPassword($data->name, $attr);
    }
    
    private function buildSelect($data) {
        $attr = []; $val = [];
        if($data->className) $attr['class'] = $data->className;
        if($data->required) $attr['required'] = $data->required;
        if($data->multiple) $attr['multiple'] = $data->multiple;
        if($data->placeholder) $attr['placeholder'] = $data->placeholder;
        $attr = $this->varexport($attr);
        
        if($data->required)  $val[]['required'] = $data->required;

        $this->classbody .= $this->formsnippets->genSelect($data->name, $attr, $val);
    }
    
    private function buildCheck($data) {
        $attr = [];
        if($data->className) $attr['class'] = $data->className;
        $attr = $this->varexport($attr);

        $this->classbody .= $this->formsnippets->genCheck($data->name, $attr);
    }
    
    private function buildDate($data) {
        $attr = []; $val = [];
        if($data->className) $attr['class'] = $data->className;
        if($data->required) $attr['required'] = $data->required;
        if($data->value) $attr['value'] = $data->value;
        if($data->placeholder) $attr['placeholder'] = $data->placeholder;
        $attr = $this->varexport($attr);
        
        if($data->required)  $val[]['required'] = $data->required;

        $this->classbody .= $this->formsnippets->genDate($data->name, $attr, $val);
    }
    
    private function buildFile($data) {
        $attr = []; $val = [];
        if($data->className) $attr['class'] = $data->className;
        if($data->required) $attr['required'] = $data->required;
        if($data->multiple) $attr['multiple'] = $data->multiple;
        if($data->placeholder) $attr['placeholder'] = $data->placeholder;
        $attr = $this->varexport($attr);
        
        $this->classbody .= $this->formsnippets->genFile($data->name, $attr);
    }
    
    private function buildNumeric($data) {
        $attr = []; $val = [];
        if($data->className) $attr['class'] = $data->className;
        if($data->required) $attr['required'] = $data->required;
        if($data->value) $attr['value'] = $data->value;
        if($data->placeholder) $attr['placeholder'] = $data->placeholder;
        
        if($data->min) $attr['min'] = $data->min;
        if($data->max) $attr['max'] = $data->max;
        if($data->step) $attr['step'] = $data->step;
        $attr = $this->varexport($attr);
        
        if($data->required)  $val[]['required'] = $data->required;
        if($data->min)  $val[]['minlen'] = $data->min;
        if($data->max)  $val[]['maxlen'] = $data->min;
        $val[]['numeric'] = true;

        $this->classbody .= $this->formsnippets->genNumeric($data->name, $attr, $val);
    }    
    
    private function buildRadio($data) {
        $attr = [];
        if($data->className) $attr['class'] = $data->className;
        $attr = $this->varexport($attr);

        $this->classbody .= $this->formsnippets->genRadio($data->name, $attr);
    }
    
    private function buildTextarea($data) {
        $attr = []; $val = [];
        if($data->className) $attr['class'] = $data->className;
        if($data->required) $attr['required'] = $data->required;
        if($data->value) $attr['value'] = $data->value;
        if($data->placeholder) $attr['placeholder'] = $data->placeholder;
        if($data->maxlength) $attr['maxlength'] = $data->maxlength;
        if($data->rows) $attr['rows'] = $data->rows;

        $attr = $this->varexport($attr);
        
        if($data->required)  $val[]['required'] = $data->required;

        $this->classbody .= $this->formsnippets->genTextarea($data->name, $attr, $val);
    }
    
    private function varexport($expression, $return=true) {
        $export = var_export($expression, TRUE);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
        ];
        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
        if ((bool)$return) return $export; else echo $export;
    }
}

