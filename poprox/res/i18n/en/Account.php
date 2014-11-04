<?php
namespace BitsTheater\res\en;
use BitsTheater\res\Resources as BaseResources;
{//begin namespace

class Account extends BaseResources {
	public $menu_account_label = 'My Account';
	public $menu_account_subtext = 'me, myself, &amp; I';
	
	public $label_login = 'Login';
	public $label_logout = 'Logout';
	public $label_name = 'Username';
	public $label_email = 'Email';
	public $label_pwinput = 'Password';
	public $label_pwconfirm = 'Confirm Password';
	public $label_regcode = 'Registration Code';
	public $label_submit = 'Register';
	public $label_save_cookie = 'Remember me';
	public $label_pwinput_old = 'Current Password';
	public $label_pwinput_new = 'New Password';
	public $label_phone = 'Phone';
	
	public $msg_pw_nomatch = 'passwords do not match';
	public $msg_acctexists = '%1$s already exists. Please use a different one.';
	public $msg_update_success = 'Successfully updated the account.';
	
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
	
	public function setup($aDirector) {
		parent::setup($aDirector);
		$this->label_modify = $this->getRes('generic/save_button_text');
	}
	
}//end class

}//end namespace
