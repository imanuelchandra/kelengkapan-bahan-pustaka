<?php

/**
 *
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

/* Item List */

defined('INDEX_AUTH') OR die('Direct access not allowed!');

//require '../../../sysconfig.inc.php';
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-reporting');
// start the session
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
// privileges checking
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_element.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require MDLBS.'reporting/report_dbgrid.inc.php';

function httpQuery($query = [])
{
    return http_build_query(array_unique(array_merge($_GET, $query)));
}

$page_title = 'Laporan Statistik Kelengkapan Bahan Pustaka';
$reportView = false;
$num_recs_show = 20;
if (isset($_GET['reportView'])) {
    $reportView = true;
}

if (!$reportView) {
?>
    <!-- filter -->
    <div class="per_title">
        <h2><?php echo __('Laporan Statistik Kelengkapan Bahan Pustaka'); ?></h2>
    </div>
    <div class="infoBox">
        <?php echo __('Report Filter'); ?>
    </div>
    <div class="sub_section">
        <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="reportView" class="form-inline">
            <input type="hidden" name="id" value="<?= $_GET['id']??'' ?>"/>
            <input type="hidden" name="mod" value="<?= $_GET['mod']??'' ?>"/>
      
                        <label><?php echo __('Tanggal Penjajaran'); ?></label>
                        <div id="range">
                            <input type="text" name="tglMulaiPenjajaran">
                            <span><?= __('to') ?></span>
                            <input type="text" name="tglSelesaiPenjajaran">
                        </div>

            <input type="submit" name="applyFilter" value="<?php echo __('Apply Filter'); ?>" class="btn btn-primary" />
            <input type="hidden" name="reportView" value="true" />
        </form>
    </div>
    <script type="text/javascript">hideRows('filterForm', 1);</script>
    <!-- filter end -->
    <iframe name="reportView" id="reportView" src="<?php echo $_SERVER['PHP_SELF'].'?' .httpQuery(['reportView' => 'true']); ?>" frameborder="0" style="width: 100%; height: 500px;"></iframe>
    <script>
        $(document).ready(function(){
            const elem = document.getElementById('range');
            const dateRangePicker = new DateRangePicker(elem, {
                language: '<?= substr($sysconf['default_lang'], 0,2) ?>',
                format: 'yyyy-mm-dd',
            });
        })
    </script>
<?php
} else {
    ob_start();
	$xls_rc = 0;
	$xls_cc = 0;
    $row_class = 'alterCellPrinted';

     
    if (isset($_GET['tglMulaiPenjajaran']) AND !empty($_GET['tglMulaiPenjajaran']) && isset($_GET['tglSelesaiPenjajaran']) AND !empty($_GET['tglSelesaiPenjajaran'])) {
        $penjajaranDateStart = $dbs->escape_string(trim($_GET['tglMulaiPenjajaran']));
        $penjajaranDateEnd = $dbs->escape_string(trim($_GET['tglSelesaiPenjajaran']));
        $criteria .= ' AND (DATE(last_update) >= \'' . $penjajaranDateStart . '\' AND DATE(last_update) <= \'' . $penjajaranDateEnd . '\')';
    }

    $output = '<table border="1">';
    // // header
    $output .= '<thead><tr>
        <th>'.__('Kelengkapan Bahan Pustaka').'</th>
        <th>'.__('Eksemplar').'</th>
        </tr></thead>';
	$xlsrows = array($xls_rc => array(__('Kelengkapan Bahan Pustaka'),__('Eksemplar')));
	$xls_rc++;
    
     $output .= '<tbody>';
     $itemMaterials_q = $dbs->query("
                                SELECT 
                                CASE WHEN property_stamp = 1 THEN COUNT(property_stamp) END AS eks_propstamp, 
                                CASE WHEN inventory_stamp = 2 THEN COUNT(inventory_stamp) END AS eks_invstamp,
                                CASE WHEN barcode = 3 THEN COUNT(barcode) END AS eks_barcode,
                                CASE WHEN book_pocket = 4 THEN COUNT(book_pocket) END AS eks_bookpocket,
                                CASE WHEN book_card = 5 THEN COUNT(book_card) END AS eks_bookcard,
                                CASE WHEN catalog_card = 6 THEN COUNT(catalog_card) END AS eks_catcard,
                                CASE WHEN book_label = 7 THEN COUNT(book_label) END AS eks_booklabel,
                                CASE WHEN date_due_slip = 8 THEN COUNT(date_due_slip) END AS eks_datedueslip
                                FROM item_materials 
                                WHERE property_stamp = 1 OR inventory_stamp = 2 OR barcode = 3 OR book_pocket = 4 OR book_card = 5 OR catalog_card = 6 OR book_label = 7 OR date_due_slip = 8 
                                GROUP BY property_stamp, inventory_stamp, barcode, book_pocket, book_card, catalog_card, book_label, date_due_slip;
                                ");

      $itemMaterials_d = $itemMaterials_q->fetch_array();

      $output .= '<tr>';
            
      $output .=  '<td>Cap Kepemilikan</td>';
           
      $output .=  '<td>'.$itemMaterials_d['eks_propstamp'].'</td>';

      //$xlsrows[$xls_rc] = array($lokasiRak_d[0],' ',' ',$jumlah_item_d[0]);
	  //$xls_rc++;

      $output .= '</tr>';


      $output .= '<tr>';
            
      $output .=  '<td>Cap Inventaris</td>';
           
      $output .=  '<td>'.$itemMaterials_d['eks_invstamp'].'</td>';

      //$xlsrows[$xls_rc] = array($lokasiRak_d[0],' ',' ',$jumlah_item_d[0]);
	  //$xls_rc++;

      $output .= '</tr>';


      $output .= '<tr>';
            
      $output .=  '<td>Barcode</td>';
           
      $output .=  '<td>'.$itemMaterials_d['eks_barcode'].'</td>';

      //$xlsrows[$xls_rc] = array($lokasiRak_d[0],' ',' ',$jumlah_item_d[0]);
	  //$xls_rc++;

      $output .= '</tr>';


      $output .= '<tr>';
            
      $output .=  '<td>Kantong Buku</td>';
           
      $output .=  '<td>'.$itemMaterials_d['eks_bookpocket'].'</td>';

      //$xlsrows[$xls_rc] = array($lokasiRak_d[0],' ',' ',$jumlah_item_d[0]);
	  //$xls_rc++;

      $output .= '</tr>';


      $output .= '<tr>';
            
      $output .=  '<td>Kartu Buku</td>';
           
      $output .=  '<td>'.$itemMaterials_d['eks_bookcard'].'</td>';

      //$xlsrows[$xls_rc] = array($lokasiRak_d[0],' ',' ',$jumlah_item_d[0]);
	  //$xls_rc++;

      $output .= '</tr>';


      $output .= '<tr>';
            
      $output .=  '<td>Kartu Katalog</td>';
           
      $output .=  '<td>'.$itemMaterials_d['eks_catcard'].'</td>';

      //$xlsrows[$xls_rc] = array($lokasiRak_d[0],' ',' ',$jumlah_item_d[0]);
	  //$xls_rc++;

      $output .= '</tr>';


      $output .= '<tr>';
            
      $output .=  '<td>Label Buku</td>';
           
      $output .=  '<td>'.$itemMaterials_d['eks_booklabel'].'</td>';

      //$xlsrows[$xls_rc] = array($lokasiRak_d[0],' ',' ',$jumlah_item_d[0]);
	  //$xls_rc++;

      $output .= '</tr>';


      $output .= '<tr>';
            
      $output .=  '<td>Slip Pengembalian</td>';
           
      $output .=  '<td>'.$itemMaterials_d['eks_datedueslip'].'</td>';

      //$xlsrows[$xls_rc] = array($lokasiRak_d[0],' ',' ',$jumlah_item_d[0]);
	  //$xls_rc++;

      $output .= '</tr>';
     
    
   $output .= '<tr class="table-warning">';
   $output .=  '<th>Total Kelengkapan :</th>';
    
   $total_q = $dbs->query("
                            SELECT bar, COALESCE(sub.prop, 0)  + COALESCE(sub.inv, 0)  + COALESCE(sub.bar, 0)  + COALESCE(sub.book, 0)  + COALESCE(sub.bcard, 0)  + COALESCE(sub.catcard, 0)  + COALESCE(sub.blabel, 0)  + COALESCE(sub.ddslip, 0)  AS total
                            FROM
                            (
                                SELECT 
                                CASE WHEN property_stamp IS NOT NULL AND property_stamp != '' THEN COUNT(property_stamp) END AS prop,  
                                
                                CASE WHEN inventory_stamp IS NOT NULL AND inventory_stamp != '' THEN COUNT(inventory_stamp) END AS inv,  
                                
                                CASE WHEN barcode IS NOT NULL AND barcode != '' THEN COUNT(barcode) END AS bar,  
                                CASE WHEN book_pocket IS NOT NULL AND book_pocket != '' THEN COUNT(book_pocket) END AS book,  
                                CASE WHEN book_card IS NOT NULL AND book_card != '' THEN COUNT(book_card) END AS bcard, 
                                CASE WHEN catalog_card IS NOT NULL AND catalog_card != '' THEN COUNT(catalog_card) END AS catcard, 
                                CASE WHEN book_label IS NOT NULL AND book_label != '' THEN COUNT(book_label) END AS blabel, 
                                
                                CASE WHEN date_due_slip IS NOT NULL AND date_due_slip != '' THEN COUNT(date_due_slip) END AS ddslip 
                            FROM item_materials) AS sub
                                ");
   $total_d = $total_q->fetch_row();
   $output .=  '<th>'.$total_d[1].'</th>';

//    $xlsrows[$xls_rc] = array(' ',' ','Total ',$total_d[0]);
//    $xls_rc++;

   $output .= '</tr>';


   $output .= '<tr class="table-warning">';
   $output .=  '<th>Total Eksemplar :</th>';
    
   $total_q = $dbs->query("
                            SELECT bar, COALESCE(sub.prop, 0)  + COALESCE(sub.inv, 0)  + COALESCE(sub.bar, 0)  + COALESCE(sub.book, 0)  + COALESCE(sub.bcard, 0)  + COALESCE(sub.catcard, 0)  + COALESCE(sub.blabel, 0)  + COALESCE(sub.ddslip, 0)  AS total
                            FROM
                            (
                                SELECT 
                                CASE WHEN property_stamp IS NOT NULL AND property_stamp != '' THEN COUNT(property_stamp) END AS prop,  
                                
                                CASE WHEN inventory_stamp IS NOT NULL AND inventory_stamp != '' THEN COUNT(inventory_stamp) END AS inv,  
                                
                                CASE WHEN barcode IS NOT NULL AND barcode != '' THEN COUNT(barcode) END AS bar,  
                                CASE WHEN book_pocket IS NOT NULL AND book_pocket != '' THEN COUNT(book_pocket) END AS book,  
                                CASE WHEN book_card IS NOT NULL AND book_card != '' THEN COUNT(book_card) END AS bcard, 
                                CASE WHEN catalog_card IS NOT NULL AND catalog_card != '' THEN COUNT(catalog_card) END AS catcard, 
                                CASE WHEN book_label IS NOT NULL AND book_label != '' THEN COUNT(book_label) END AS blabel, 
                                
                                CASE WHEN date_due_slip IS NOT NULL AND date_due_slip != '' THEN COUNT(date_due_slip) END AS ddslip 
                            FROM item_materials) AS sub
                                ");
   $total_d = $total_q->fetch_row();
   $output .=  '<th>'.$total_d[0].'</th>';

//    $xlsrows[$xls_rc] = array(' ',' ','Total ',$total_d[0]);
//    $xls_rc++;

   $output .= '</tr>';

   $output .= '</tbody>';
   $output .= '</table>';

    // print out
    echo '<div class="mb-2">'.__('Statistik Kelengkapan Bahan Pustaka').' 
    <a href="#" class="s-btn btn btn-default printReport" onclick="window.print()">'.__('Print Current Page').'</a>
    <a href="' . AWB . 'modules/reporting/xlsoutput.php" class="s-btn btn btn-default">'.__('Export to spreadsheet format').'</a></div>'."\n";
    echo $output;

	unset($_SESSION['xlsquery']); 
	$_SESSION['xlsdata'] = $xlsrows;
	$_SESSION['tblout'] = "recap_list";
	// echo '<p><a href="../xlsoutput.php" class="s-btn btn btn-default">'.__('Export to spreadsheet format').'</a></p>';
    $content = ob_get_clean();
    // include the page template
    require SB.'/admin/'.$sysconf['admin_template']['dir'].'/printed_page_tpl.php';
}