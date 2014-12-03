<?php
/*
    PufferPanel - A Minecraft Server Management Panel
    Copyright (c) 2013 Dane Everitt

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see http://www.gnu.org/licenses/.
 */
namespace PufferPanel\Core;
use \ORM as ORM;
use \PDO as PDO;

if(file_exists('../install.lock'))
	exit('Installer is Locked.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="../../../assets/css/bootstrap.css">
	<title>PufferPanel Installer</title>
</head>
<body>
	<div class="container">
		<div class="alert alert-danger">
			<strong>WARNING:</strong> Do not run this version on a live environment! There are known security holes that we are working on getting patched. This is extremely beta software and this version is to get the features in place while we work on security enhancements.
		</div>
		<div class="navbar navbar-default">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">Install PufferPanel - General Settings</a>
			</div>
		</div>
		<div class="col-12">
			<div class="row">
				<div class="col-2"></div>
				<div class="col-8">
					<p>This information can be changed later on. Please provide accurate information for URLs, using the wrong link can break the system.</p>
					<?php

					    if(isset($_POST['do_settings'])){

					        include('../../../../src/core/configuration.php');
							$mysql = new PDO('mysql:host='.$_INFO['sql_h'].';dbname='.$_INFO['sql_db'], $_INFO['sql_u'], $_INFO['sql_p'], array(
								PDO::ATTR_PERSISTENT => true,
								PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
							));

							$mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

							foreach($_POST as $id => $val)
								if(in_array($id, array('master_url', 'main_website', 'assets_url')))
									if(!preg_match('/^((https?:){0,1})(\/\/){1}([-\d\w\/.]*)$/', $val))
										Components\Page::redirect('../urls.php?error='.$id);
									else
										$_POST[$id] = preg_replace('/^(http:|https:)(.*)?$/', '$2', $val);

					        $prepare = $mysql->prepare("INSERT INTO `acp_settings` (`setting_ref`, `setting_val`) VALUES
					            ('company_name', :cname),
					            ('master_url', :murl),
								('assets_url', :aurl),
								('main_website', :mwebsite),
					            ('postmark_api_key', NULL),
					            ('mandrill_api_key', NULL),
					            ('mailgun_api_key', NULL),
					            ('sendgrid_api_key', NULL),
					            ('sendmail_email', :smail),
					            ('sendmail_method','php'),
					            ('captcha_pub','6LdSzuYSAAAAAHkmq8LlvmhM-ybTfV8PaTgyBDII'),
					            ('captcha_priv','6LdSzuYSAAAAAISSAYIJrFGGGJHi5a_V3hGRvIAz'),
					            ('default_language', 'en'),
					            ('force_online', 0),
								('https', 0),
								('use_api', 0),
								('allow_subusers', 0)");

					        $prepare->execute(array(
					            ':cname' => $_POST['company_name'],
					            ':murl' => $_POST['master_url'],
					            ':smail' => $_POST['sendmail_email'],
					            ':mwebsite' => $_POST['main_website'],
					            ':aurl' => $_POST['assets_url'],
					        ));

					        exit('<meta http-equiv="refresh" content="0;url=hash.php"/>');

					    }

					?>
					<form action="settings.php" method="post">
						<fieldset>
							<div class="form-group">
								<label for="company_name" class="control-label">Company Name</label>
								<div>
									<input type="text" class="form-control" name="company_name" autocomplete="off" />
								</div>
							</div>
							<div class="form-group">
								<label for="main_website" class="control-label">Main Website URL</label>
								<div>
									<input type="text" class="form-control" name="main_website" value="http://<?php echo $_SERVER['HTTP_HOST']; ?>/" autocomplete="off" />
								</div>
							</div>
							<div class="form-group">
								<label for="master_url" class="control-label">PufferPanel Master URL</label>
								<div>
									<input type="text" class="form-control" name="master_url" value="<?php echo str_replace("install/install/do/settings.php", "", 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']); ?>" autocomplete="off" />
								</div>
							</div>
							<div class="form-group">
								<label for="assets_url" class="control-label">PufferPanel Assets URL</label>
								<div>
									<input type="text" class="form-control" name="assets_url" value="<?php echo str_replace("install/install/do/settings.php", "", 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']).'assets/'; ?>" autocomplete="off" />
								</div>
							</div>
							<div class="form-group">
								<label for="sendmail_email" class="control-label">Sendmail Email</label>
								<div>
									<input type="text" class="form-control" name="sendmail_email" autocomplete="off" />
								</div>
							</div>
							<div class="form-group">
								<div>
									<input type="submit" class="btn btn-primary" name="do_settings" value="Continue &rarr;" />
								</div>
							</div>
					    </fieldset>
					</form>
				</div>
				<div class="col-2"></div>
			</div>
		</div>
		<div class="footer">
            <div class="col-8 nopad"><p>PufferPanel is licensed under a <a href="https://github.com/PufferPanel/PufferPanel/blob/master/LICENSE">GPL-v3 License</a>.<br />Running <?php echo trim(file_get_contents('../../../../src/versions/current')).' ('.substr(trim(file_get_contents('../../../../.git/HEAD')), 0, 8).')'; ?> distributed by <a href="http://pufferpanel.com">PufferPanel Development</a>.</p></div>
		</div>
	</div>
</body>
</html>
