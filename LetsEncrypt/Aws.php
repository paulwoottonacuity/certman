<?php

namespace FreePBX\modules\Certman\LetsEncrypt;

Class Aws Extends Base {

	public $weblink = "https://github.com/Neilpang/acme.sh/wiki/How-to-use-Amazon-Route53-API";

	// public $staging = false;

	public function getRawName() {
		return "aws";
	}

	public function getOptions() {
		return [
			"aws-key" => [ 
				"text" => _("AWS Key"), 
				"default" => "",
				"placeholder" => _("AWS Key - Like ABCDABCDEFGHIJKLMNOP"),
				"changeable" => true,
			],
			"aws-secret" => [ 
				"text" => _("AWS Secret"), 
				"default" => "",
				"placeholder" => _("AWS Secret - About 30 random chars"),
				"changeable" => true,
			],
		];
	}

	public function issueCert($cert, $force = false) {
		$vals = $this->loadOptions();
		$hooks = "--pre-hook '".__DIR__."/cert-pre-hook \\\$domain' --post-hook '".__DIR__."/cert-post-hook \\\$domain' ";
		if ($force) {
			$cmd = "--issue --force -d $cert $hooks -w ".$vals['webroot'];
		} else {
			$cmd = "--issue -d $cert $hooks -w ".$vals['webroot'];
		}
		return $this->run($cmd);
	}

	public function renewCert($cert, $force = false) {
		$vals = $this->loadOptions();
		if ($force) {
			$cmd = "--renew --force -d $cert -w ".$vals['webroot'];
		} else {
			$cmd = "--renew -d $cert -w ".$vals['webroot'];
		}
		return $this->run($cmd);
	}
}

