<?php
function qcf_upload_dir( $dir ) {
	return array(
		       'path'   => $dir['basedir'] . '/qcf',
		       'url'    => $dir['baseurl'] . '/qcf',
		       'subdir' => '/qcf',
	       ) + $dir;
}

