<?php

	$host = "db";
	$user = "root" ;
	$pass = "1234";	
	$db = "alimrugi";
	


	$konekdb = new mysqli($host,$user,$pass,$db);

	if ($konekdb->connect_error) {
		die("Koneksi ke database gagal: " . $konekdb->connect_error);
	}
	



?>