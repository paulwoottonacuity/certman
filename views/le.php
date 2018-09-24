<?php
if(!empty($message)) {
	$messagehtml = '<div class="alert alert-' . $message['type'] .'">'. $message['message'] . '</div>';
}

$fwapi = \FreePBX::Certman()->getFirewallAPI();
$letext = _("LetsEncrypt HTTP Challenge require that port 80 has unfiltered access from the entire internet.");

// Is firewall enabled and available?
if ($fwapi->isAvailable()) {
	// Are our hosts already set up?
	if (!$fwapi->portIsOpen()) {
		// They're not. Add a warning and a button
		$alert = "<form class='fpbx-submit' name='frm_fixfirewall' id='updatefw' method='post'>";
		$alert .= "<div class='alert alert-warning'><h3>"._("Firewall Warning")."</h3>";
		$alert .= "<p class='col-sm-12'>$letext</p>"; // Adding col-sm-12 fixes the padding in the alert
		$alert .= "</div></form>";
	} else {
		$alert = "<div class='alert alert-success'><h3>"._("Firewall Validated")."</h3>";
		$alert .= "<p>$letext</p>";
		$alert .= "<p>"._("The Firewall module believes that this port is open to the world. However, it's possible that other external firewalls may block access. If you are having problems validating your certificate, this could be the issue, and you may need to use DNS Validation.")."</p>";
		$alert .= "</div>";
	}
} else {
	$alert = "<div class='alert alert-info'><h3>"._("Firewall Warning")."</h3>";
	$alert .= "<p>$letext</p>";
	$alert .= "<p>"._("PBX System Firewall is not in use so this can not be verified. Please manually verify inbound connectivity.")."</p>";
	$alert .= "</div>";
}
?>

<div class="container-fluid">
	<h1><?php echo !empty($cert['cid']) ? _("Edit Let's Encrypt Certificate") : _("New Let's Encrypt Certificate")?></h1>
	<?php echo !empty($messagehtml) ? $messagehtml : "" ?>
	<?php echo $alert; ?>
	<div class='alert alert-info'><?php printf(_("Let's Encrypt Certificates are <strong>automatically</strong> updated by %s when required (Approximately every 2 months). Do not install your own certificate updaters!"), \FreePBX::Config()->get("DASHBOARD_FREEPBX_BRAND")); ?></div>
	<div class = "display full-border">
		<div class="row">
			<div class="col-sm-12">
				<div class="fpbx-container">
					<div class="display full-border" id='certpage'>
						<form class="fpbx-submit" name="frm_certman" action="config.php?display=certman" method="post" enctype="multipart/form-data" data-fpbx-delete="config.php?display=certman&amp;certaction=delete&amp;type=cert&amp;id=<?php echo $cert['cid']?>">
							<input id="certaction" type="hidden" name="certaction" value="<?php echo !empty($cert['cid']) ? 'edit' : 'add'?>">
							<input id="certtype" type="hidden" name="type" value="le">
							<input id="cid" type="hidden" name="cid" value="<?php echo !empty($cert['cid']) ? $cert['cid'] : ''?>">
							<div class="element-container">
								<div class="row">
									<div class="form-group form-horizontal">
										<div class="col-md-3">
											<label class="control-label" for="host"><?php echo _("Certificate Host Name")?></label>
											<i class="fa fa-question-circle fpbx-help-icon" data-for="host"></i>
										</div>
										<div class="col-md-9">
											<?php if (empty($cert['cid'])) { ?>
												<input type="text" class="form-control" id="host" name="host" placeholder="server.example.com" required value="<?php echo $hostname?>">
											<?php } else { ?>
												<?php echo !empty($cert['basename']) ? $cert['basename'] : ""?>
											<?php } ?>
										</div>
									</div>
									<div class="col-md-12">
										<span id="host-help" class="help-block fpbx-help-block" style=""><?php echo _("This must be the hostname you are requesting a certificate for.")?></span>
									</div>
								</div>
							</div>
							<div class="element-container">
								<div class="row">
									<div class="form-group form-horizontal">
										<div class="col-md-3">
											<label class="control-label" for="email"><?php echo _("Owners Email")?></label>
											<i class="fa fa-question-circle fpbx-help-icon" data-for="email"></i>
										</div>
										<div class="col-md-9">
											<input type="text" class="form-control" id="email" name="email" placeholder="you@example.com" required value="<?php echo $cert['additional']['email']; ?>">
										</div>
									</div>
									<div class="col-md-12">
										<span id="email-help" class="help-block fpbx-help-block" style=""><?php echo _("This email address is given to Let's Encrypt. It may be used by them if the certificate is approaching expiration and it has not been renewed.")?></span>
									</div>
								</div>
							</div>
							<?php if(!empty($cert['cid'])) { ?>
								<div class="element-container">
									<div class="row">
										<div class="form-group form-horizontal">
											<div class="col-md-3">
												<label class="control-label" for="expires"><?php echo _("Valid Until")?></label>
											</div>
											<div class="col-md-9"> <?php echo \FreePBX::Certman()->getReadableExpiration($certinfo['validTo_time_t']); ?> </div>
										</div>
									</div>
								</div>
								<div class="element-container">
									<div class="row">
										<div class="form-group form-horizontal">
											<div class="col-md-3">
												<label class="control-label" for="cn"><?php echo _("Common Name")?></label>
											</div>
											<div class="col-md-9">
												<?php echo $certinfo['subject']['CN']?>
											</div>
										</div>
									</div>
								</div>
								<?php if(!empty($certinfo['extensions']['certificatePolicies'])) {?>
									<div class="element-container">
										<div class="row">
											<div class="form-group form-horizontal">
												<div class="col-md-3">
													<label class="control-label" for="cp"><?php echo _("Certificate Policies")?></label>
													<i class="fa fa-question-circle fpbx-help-icon" data-for="cp"></i>
												</div>
												<div class="col-md-9">
													<textarea class="form-control" readonly><?php echo $certinfo['extensions']['certificatePolicies']?></textarea>
												</div>
											</div>
											<div class="col-md-12">
												<span id="cp-help" class="help-block fpbx-help-block" style=""><?php echo _('A certificate policy (CP) is a document which aims to state what are the different actors of a public key infrastructure (PKI), their roles and their duties')?></span>
											</div>
										</div>
									</div>
								<?php } ?>
							<?php } ?>
							<!-- Challenge Method -->
							<div class="element-container">
								<div class="row">
									<div class="form-group form-horizontal">
										<div class="col-md-3">
											<label class="control-label" for="challengetype"><?php echo _("Validate Using")?></label>
										</div>
										<div class="col-md-9">
<?php
$method = "webroot";

// List of DNS API providers:
//
// https://github.com/Neilpang/acme.sh/blob/master/dnsapi/README.md
//
$methods = [ "webroot" => _("HTTP via Port 80"), "aws" => _("AWS Route53 DNS"), "cloudflare" => _("CloudFlare DNS API") ];
?>
<select class="form-control" id="method" name="method"> 
<?php
foreach ($methods as $m => $txt) {
	if ($m === $method) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	print "<option value='$m' $selected>$txt</option>\n";
}
?>
</select>
										</div>
									</div>
								</div>
							</div>
							<!-- END Challenge Method -->
							<div class='element-container'>
<?php
foreach (array_keys($methods) as $m) {
	$classname = 'FreePBX\\modules\\Certman\\LetsEncrypt\\'.ucfirst($m);
	if (!class_exists($classname)) {
		throw new \Exception("Can't create LE handler $classname - This is a bug in Certman.");
	}
	$le = new $classname;
	$opts = $le->getOptions();
	$rawname = $le->getRawName();
	$current = \FreePBX::Certman()->getAll("le-$rawname");
	if ($method !== $m) {
		$hidden = "style='display: none'";
	} else {
		$hidden = "";
	}
	foreach ($opts as $o => $vals) {
		if (!empty($vals['default'])) {
			$default = $vals['default'];
		} else {
			$default = "";
		}
		if (empty($vals['changeable'])) {
			$disabled = "disabled";
			$value = $vals['default'];
		} else {
			$disabled = "";
			if (!empty($current[$o])) {
				$value = $current[$o];
			} else {
				$value = "";
			}
		}
		if (!empty($vals['placeholder'])) {
			$default = $vals['placeholder'];
		}
		$value = htmlentities($value, \ENT_QUOTES, 'UTF-8');
		$default = htmlentities($default, \ENT_QUOTES, 'UTF-8');
		print "<div class='row letsencrypt le-$rawname' $hidden><div class='form-group form-horizontal'><div class='col-md-3'>";
		print "<label class='control-label' for='${m}-${o}'>".$vals['text']."</label></div>";
		print "<div class='col-md-9'>";
		print "<input type='text' class='form-control blankok' id='${m}-${o}' name='${m}-${o}' $disabled placeholder='$default' value='$value'>";
		print "</div>";
		print "</div></div>";
	}
	if (!empty($le->weblink)) {
		print "<div class='row letsencrypt le-$rawname' $hidden><div class='form-group form-horizontal'><div class='col-md-3'> &nbsp; </div>";
		print "<div class='col-md-9'>";
		printf(_("Please visit <a href='%s' target='_new'>this page</a> for information or help with using this Validation Method."), $le->weblink);
		print "</div></div></div>";
	}
}
?>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script>

function showSettings(modname) {
        $(".row.letsencrypt").hide();
        $(".row.letsencrypt.le-"+modname).show();
}

$(function() {
	// Whenever the 'Validate Using' select is changed, display the values for that
	// option.
	$("#method").on('change', function(e) {
		showSettings($(e.target).val());
	});
});


</script>


