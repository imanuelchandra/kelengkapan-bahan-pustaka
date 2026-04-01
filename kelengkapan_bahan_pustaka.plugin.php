<?php
/**
 * Plugin Name: Kelengkapan Bahan Pustaka Untuk SLiMS
 * Plugin URI: -
 * Description: Plugins kelengkapan bahan pustaka dan laporan kelengkapan bahan pustaka untuk SLIMS
 * Version: 1.0.0
 * Author: -
 * Author URI: -
 */
use SLiMS\Plugins;

$plugin = Plugins::getInstance();

Plugins::getInstance()->registerAutoload(__DIR__);


$pathKelengkapanBahanPustaka = __DIR__ . '/pages/kelengkapan_bahan_pustaka.php';
$pathRekapitulasiKelengkapanBahanPustaka = __DIR__ . '/pages/rekapitulasi_kelengkapan_bahan_pustaka.php';
$pathStatistikKelengkapanBahanPustaka = __DIR__ . '/pages/statistik_kelengkapan_bahan_pustaka.php';
//$path =  __DIR__ . '/pages/inventarisasi.php';
 // Make default group menu

Plugins::group('Kelengkapan Bahan Pustaka', function() use($pathKelengkapanBahanPustaka, $pathRekapitulasiKelengkapanBahanPustaka) {
            // Scan all file inside module directory as menu
 Plugins::menu('reporting', 'Daftar Kelengkapan Bahan Pustaka', $pathKelengkapanBahanPustaka);
 Plugins::menu('reporting', 'Rekapitulasi Kelengkapan Bahan Pustaka', $pathRekapitulasiKelengkapanBahanPustaka);

});


Plugins::group('Kelengkapan Bahan Pustaka', function() use($pathStatistikKelengkapanBahanPustaka) {
            // Scan all file inside module directory as menu
  Plugins::menu('reporting', 'Statistik Kelengkapan Bahan Pustaka', $pathStatistikKelengkapanBahanPustaka);
});