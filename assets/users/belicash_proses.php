<?php

include '../../koneksi.php' ;
extract($_POST);



// VARIABEL TETAP 
$id_admin = "0" ;
$status_verifikasi = "0" ;


$query = mysqli_query($konekdb, 'INSERT into tb_cash 
		 (id_beli,id_pembeli,id_mobil,nama_mobil,harga_mobil,id_admin,status_verifikasi) VALUES 
		 ("'.$id_beli.'","'.$id_pembeli.'","'.$id_mobil.'","'.$nama_mobil.'","'.$harga_mobil.'","'.$id_admin.'","'.$status_verifikasi.'") ');


if($query){
	echo "<script> alert('Pembelian berhasil !') ;
	document.location.href='transaksi.php'</script>";

}else{
	echo "<script> alert('Pembelian gagal !') ;
	document.location.href='index.php'</script>";

}
?>