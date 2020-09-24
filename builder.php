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
                    break;

                default:
                    break;
            };
        }
    }
    
    private function buildText($data) {
        $attr = []; $val = [];
        if($data->className) $attr['class'] = $data->className;
        if($data->required) $attr['required'] = $data->required;
        $attr = $this->varexport($attr);
        
        if($data->required)  $val[]['required'] = $data->required;
        if($data->maxlength) $val[]['maxlen'] = $data->maxlength;

        $this->classbody .= $this->formsnippets->genText($data->name, $attr, $val);
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

