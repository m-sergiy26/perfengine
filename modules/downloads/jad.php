<?php
/*$file_id = App::filter('int', $_GET['id']);
$att_id = App::filter('int', $_GET['attachment_id']);

if(isset($_GET['id']) && $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $file_id ."'")->rowCount() !=0 && $db->query("SELECT ext FROM `downloads_files` WHERE `id` = '". $file_id ."'")->fetchColumn() == 'jar')
{
		$afile = $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $file_id ."'")->fetch();
		$root_dir = $db->query("SELECT server_path FROM `downloads` WHERE `id` = '". abs(intval($afile['ref_id'])) ."'")->fetchColumn();
		$jar_name = str_replace('.jar', '', $afile['server_name']);
		if(!file_exists(ROOT.'/cache/downloads_jad/'.$jar_name.'.jad'))
			{
				import_lib('pclzip.lib');
				$jar = new PclZip(ROOT.'/files/downloads/'.$root_dir.'/'.$afile['server_dir'].'/'.$afile['server_name']);
				$manifest = $jar->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);
				$extract = $manifest[0]['content'];
				$created = $extract."\n".'MIDlet-Jar-Size: '. $afile['size'] ."\n".'MIDlet-Jar-URL: '. URL .'/files/downloads/'. $root_dir .'/'. $afile['server_dir'] .'/'. $afile['server_name'];
				file_put_contents(ROOT.'/cache/downloads_jad/'.$jar_name.'.jad', $created);
				header('location: /cache/downloads_jad/'.$jar_name.'.jad');
				exit;
			}
		else
			{
				header('location: /cache/downloads_jad/'.$jar_name.'.jad');
				exit;
			}
	}
elseif(isset($_GET['attachment_id']) && $db->query("SELECT * FROM `downloads_archive` WHERE `id` = '". $att_id ."'")->rowCount() !=0 && $db->query("SELECT ext FROM `downloads_archive` WHERE `id` = '". $att_id ."'")->fetchColumn() == 'jar')
	{
		$afile = $db->query("SELECT * FROM `downloads_archive` WHERE `id` = '". $att_id ."'")->fetch();
		$ffile = $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $afile['file_id'] ."'")->fetch();
		$root_dir = $db->query("SELECT server_path FROM `downloads` WHERE `id` = '". abs(intval($ffile['ref_id'])) ."'")->fetchColumn();
		$jar_name = str_replace('.jar', '', $afile['server_name']);
		if(!file_exists(ROOT.'/cache/downloads_jad/'.$jar_name.'.jad'))
			{
				import_lib('pclzip.lib');
				$jar = new PclZip(ROOT.'/files/downloads/'.$root_dir.'/'.$ffile['server_dir'].'/'.$afile['server_name']);
				$manifest = $jar->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);
				$extract = $manifest[0]['content'];
				$created = $extract."\n".'MIDlet-Jar-Size: '. $afile['size'] ."\n".'MIDlet-Jar-URL: '. URL .'/files/downloads/'. $root_dir .'/'. $ffile['server_dir'] .'/'. $afile['server_name'];
				file_put_contents(ROOT.'/cache/downloads_jad/'.$jar_name.'.jad', $created);
				header('location: /cache/downloads_jad/'.$jar_name.'.jad');
				exit;
			}
		else
			{
				header('location: /cache/downloads_jad/'.$jar_name.'.jad');
				exit;
			}
	}
else
	{
		header('location: /downloads/');
		exit;
	}*/