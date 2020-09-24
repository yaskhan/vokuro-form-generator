<?php

class FormSnippet {
	public function genClassBody($formname, $fields) {
        $class = <<<'EOD'
<?php
declare(strict_types=1);

namespace Vokuro\Forms;

use Phalcon\Forms\Element\{
	Check    ,
	Date     ,
	Email    ,
	File     ,
	Hidden   ,
	Numeric  ,
	Password ,
	Radio    ,
	Select   ,
	Submit   ,
	Text     ,
	Textarea
};
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\{
	Alnum       ,
	Alpha       ,
	Between     ,
	Callback    ,
	Confirmation,
	Creditcard  ,
	Date  as DateValidator,
	Digit       ,
	Email as EmailValidator,
	Exception   ,
	Exclusionin ,
	File  as FileValidator,
	Identical   ,
	Inclusionin ,
	Numericality,
	PresenceOf  ,
	Regex       ,
	StringLength,
	Uniqueness  ,
	Url
};


class %sForm extends Form
{
    public function initialize()
    {

%s

        $csrf = new Hidden('csrf');
        $csrf->addValidator(new Identical([
            'value'   => $this->security->getRequestToken(),
            'message' => 'CSRF validation failed',
        ]));
        $csrf->clear();
        $this->add($csrf);
		
        $this->add(new Submit('Save', [
            'class' => 'btn btn-primary',
        ]));
    }
}
EOD;
		return sprintf($class, $formname, $fields);
	}
        
        public function genValidators($fname, $val) {
            $ret = '';
            $body = '$'.$fname.'->addValidator(%s);'.PHP_EOL;
            $required = "new PresenceOf(['message' => 'required',])";
            $textlmax = "new StringLength(['max' => %s, 'messageMaximum' => 'Maximum %s characters',])";
            $textlmin = "new StringLength(['min' => %s, 'messageMinimum' => 'Minimum %s characters',])";
            $num = 'new Numericality(["message" => ":field is not numeric",])';
            if(isset($val)) {
                foreach ($val as $v) {
                    if($v['required'])
                        $ret .= sprintf($body, $reuired);
                    if($v['maxlen'])
                        $ret .= sprintf($body, sprintf($textlmax, $v['maxlen']));
                    if($v['minlen'])
                        $ret .= sprintf($body, sprintf($textlmin, $v['minlen']));
                    if($v['numeric'])
                        $ret .= sprintf($body, $num);
                }
                return $ret;
            }
        }


        public function genText($fname, $attr, $val) {
			$field = <<<'EOD'
		$%1$s = new Text('%1$s', %2$s);
		%3$s
		// $%1$s->setLabel('%1$s');
		// $%1$s->setFilters(['string', 'trim',]);
		$this->add($%1$s);
EOD;
		return sprintf($field, $fname, $attr, $this->genValidators($fname, $val));
	}
	
	public function genEmail($fname, $attr) {
			$field = <<<'EOD'
		$%1$s = new Email('%1$s', %2$s);
		$%1$s->addValidators([new EmailValidator(['message' => 'The e-mail is not valid',]),]);
		// $%1$s->setLabel('%1$s');
		$%1$s->setFilters(['string', 'trim',]);
		$this->add($%1$s);
EOD;
		return sprintf($field, $fname, $attr);
	}
	
	public function genPassword($fname, $attr) {
			$field = <<<'EOD'
		$%1$s = new Password('%1$s', %2$s);
		$%1$s->addValidators([
                    new PresenceOf([
                        'message' => 'Password is required',
                    ]),
                    new StringLength([
                        'min'            => 8,
                        'messageMinimum' => 'Password is too short. Minimum 8 characters',
                    ]),
		]);
		// $%1$s->setLabel('%1$s');
		$%1$s->setFilters(['string', 'trim',]);
		$this->add($%1$s);
EOD;
		return sprintf($field, $fname, $attr);
	}
	
	public function genSelect($fname, $attr) {
			$field = <<<'EOD'
		$%1$s = new Select('%1$s', %2$s);
		// $%1$s->seOptions([]);
		// $%1$s->addValidators([new PresenceOf(['message' => 'required',]),]);
		// $%1$s->setLabel('%1$s');
		$this->add($%1$s);
EOD;
		return sprintf($field, $fname, $attr);
	}
	
	public function genCheck($fname, $attr) {
			$field = <<<'EOD'
		$%1$s = new Check('%1$s', %2$s);
		// $%1$s->setLabel('%1$s');
		$this->add($%1$s);
EOD;
		return sprintf($field, $fname, $attr);
	}
	
	public function genDate($fname, $attr) {
			$field = <<<'EOD'
		$%1$s = new Date('%1$s', %2$s);
		$%1$s->setLabel('%1$s');
		$this->add($%1$s);
EOD;
		return sprintf($field, $fname, $attr);
	}
	
	public function genFile($fname, $attr) {
			$field = <<<'EOD'
		$%1$s = new File('%1$s', %2$s);
		// $%1$s->addValidators([new PresenceOf(['message' => 'required',]),
		//	new FileValidator(
		//		[
		//			"maxSize"              => "2M",
		//			"messageSize"          => ":field exceeds the max file size (:size)",
		//			"allowedTypes"         => [
		//				"image/jpeg",
		//				"image/png",
		//			],
		//			"messageType"          => "Allowed file types are :types",
		//			"maxResolution"        => "800x600",
		//			"messageMaxResolution" => "Max resolution of :field is :resolution",
		//		]
		//	)]);
		$%1$s->setLabel('%1$s');
		$this->add($%1$s);
EOD;
		return sprintf($field, $fname, $attr);
	}
	
	public function genNumeric($fname, $attr) {
			$field = <<<'EOD'
		$%1$s = new Numeric('%1$s', %2$s);
		 $%1$s->addValidators([
				// new PresenceOf(['message' => 'required',]),
				new Numericality([
					"message" => ":field is not numeric",
				])
		]);
		$%1$s->setLabel('%1$s');
		$this->add($%1$s);
EOD;
		return sprintf($field, $fname, $attr);
	}
        
	public function genRadio($fname, $attr) {
			$field = <<<'EOD'
		$%1$s = new Radio('%1$s', %2$s);
		$%1$s->setLabel('%1$s');
		$this->add($%1$s);
EOD;
		return sprintf($field, $fname, $attr);
	}
        
	public function genTextarea($fname, $attr) {
			$field = <<<'EOD'
		$%1$s = new Textarea('%1$s', %2$s);
		$%1$s->setLabel('%1$s');
		$this->add($%1$s);
EOD;
		return sprintf($field, $fname, $attr);
	}
}


