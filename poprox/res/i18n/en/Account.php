<?php
namespace BitsTheater\res\en;
use BitsTheater\res\en\CoreAccount as BaseResources;
{//begin namespace

class Account extends BaseResources {
	public $label_phone = 'Phone';
	
	public $text_reg_cht_code = 'CHT-OPT-IN';
	public $text_reg_cht_microtasking_regcode = <<<EOD
If you would like to participate in our Counter Human Trafficking microtasking project,
please use "CHT-OPT-IN" as your registration code.
EOD;
	public $text_reg_cht_microtasking_phone = <<<EOD
If you would also like to particpate via SMS text, please provide your phone number.
EOD;
	public $text_account_cht_microtasking = <<<EOD
If you would like to participate in our Counter Human Trafficking microtasking project via SMS text,
please provide your phone number in the space provided below.
EOD;
	
}//end class

}//end namespace
