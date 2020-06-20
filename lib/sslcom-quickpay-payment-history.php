<?php 
	wp_register_style('stylesheet_1', "//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css");
    wp_register_style('stylesheet_2', "//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css");
    wp_register_style('stylesheet_3', "//cdn.datatables.net/plug-ins/1.10.12/features/searchHighlight/dataTables.searchHighlight.css");

	wp_register_script('script_1', "//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js");
    wp_register_script('script_2', "//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js");
    wp_register_script('script_3', "//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js");
    wp_register_script('script_4', "//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js");
    wp_register_script('script_5', "//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js");
    wp_register_script('script_6', "//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js");
    wp_register_script('script_7', "//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js");
    wp_register_script('script_8', "//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js");
    wp_register_script('script_9', "//cdn.datatables.net/plug-ins/1.10.12/features/searchHighlight/dataTables.searchHighlight.min.js");
    wp_register_script('script_10', "//bartaz.github.io/sandbox.js/jquery.highlight.js");

    wp_enqueue_script('script_1');
    wp_enqueue_script('script_2');
    wp_enqueue_script('script_3');
    wp_enqueue_script('script_4');
    wp_enqueue_script('script_5');
    wp_enqueue_script('script_6');
    wp_enqueue_script('script_7');
    wp_enqueue_script('script_8');
    wp_enqueue_script('script_9');
    wp_enqueue_script('script_10');

    wp_enqueue_style('stylesheet_1');
    wp_enqueue_style('stylesheet_2');
    wp_enqueue_style('stylesheet_3');

    global $wpdb;
    $table_name = $wpdb->prefix . 'sslcom_quickpay_payment';

    $results = $wpdb->get_results('SELECT * FROM ' . $table_name. ' ORDER BY tran_date DESC', ARRAY_A);
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // alert("hello");

            $('#example').DataTable({
                // aLengthMenu: [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500, "ALL"]],
                searchHighlight: true,
                dom: 'Blfrtip',
                buttons: ['copy', 'excel', 'print']
            });
        });
    </script>
    <div class='myTableWrp'>
        <table id='example' class='display nowrap' cellspacing='0' width='100%'>
            <thead>
                <tr style="background:#00004d;color:white;">
                    <th>SL No.</th>
                    <th>Transection ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Card Type (Currency)</th>
                    <th>Paid Apount</th>
                    <th>Payer Name</th>
                    <th>Payer Email</th>
                    <th>Payer Phone</th>
                    <th><b>Service/Package/Product Name</b></th>
                    <th>Address</th>
                    <th>Extra Field 1</th>
                    <th>Extra Field 2</th>
                    <th>Notes</th>
                    <th>IPN Status</th>
                </tr>
            </thead>
            
            <tbody>
                <?php
                if (!empty($results)) {
                    $i = 0;
                    foreach ($results as $row) {
                        ?>
                        <tr>
                            <td><?php echo ++$i; ?></td>
                            <td style="color: blue;"><strong><?php echo $row['trxid']; ?></strong></td>
                            <td><?php echo $row['tran_date']; ?></td>
                            <?php if($row['tran_status'] == "Processing") { ?>
                            <td style="color: green;"><strong><?php echo $row['tran_status']; ?> (Success)</strong></td>
                            <?php } else {?>
                            <td style="color: red;"><?php echo $row['tran_status']; ?></td>
                            <?php } ?>
                            <td><?php echo $row['card_type']; ?></td>
                            <td><strong><?php echo number_format($row['total_amount']); ?></strong></td>
                            <td><strong><?php echo $row['cus_name']; ?></strong></td>
                            <td><?php echo $row['cus_email']; ?></td>
                            <td><?php echo $row['cus_phone']; ?></td>
                            <td><strong><?php echo $row['product_name']; ?></strong></td>
                            <td><?php echo $row['cus_address']; ?></td>
                            <td><?php echo $row['extra_field1']; ?></td>
                            <td><?php echo $row['extra_field2']; ?></td>
                            <td><?php echo $row['notes']; ?></td>
                            <td><?php echo $row['ipn_status']; ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
