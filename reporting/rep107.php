<?php
        // This is Demo Output File
        $page_security = 'SA_SALESTRANSVIEW';
        ob_start();

        $path_to_root="..";

        include_once($path_to_root . "/includes/session.inc");
        include_once($path_to_root . "/includes/date_functions.inc");
        include_once($path_to_root . "/includes/data_checks.inc");
        include_once($path_to_root . "/sales/includes/sales_db.inc");
        include_once($path_to_root . "/gl/includes/db/gl_db_currencies.inc");
        include_once($path_to_root . "/inventory/includes/db/items_db.inc");
        include_once($path_to_root."/modules/TCPDF/reporting/includes/db/rep107_db.php");
        require_once($path_to_root . "/modules/TCPDF/tcpdf.php");

// ---------------------------------------------------------
        $from = $_POST['PARAM_0'];
        $to = $_POST['PARAM_1'];
        $invoice_currency = $_POST['PARAM_2'];
        $email = $_POST['PARAM_3'];
        $pay_service = $_POST['PARAM_4'];
        $comments = $_POST['PARAM_5'];
        $customer = $_POST['PARAM_6'];
        $i = $_POST['PARAM_7'];

        if (!$from || !$to) return;

        $dec = user_price_dec();

        $fno = explode("-", $from);
        $tno = explode("-", $to);
        $from = min($fno[0], $tno[0]);
        $to = max($fno[0], $tno[0]);
        $range = get_invoice_range($from, $to);
        
        // Demo Invoice Starts
        // In this example HTML is used to create PDF file.
        $html = '
        <style>
        table, tr, td {
        padding: 15px;
        }
        </style>
        <table style="background-color: #222222; color: #fff">
        <tbody>
        <tr>
        <td><h1>INVOICE<strong> #21234</strong></h1></td>
        <td align="right">
        123 street, ABC Store<br/>
        Country, State, 00000
        <br/>
        <strong>+00-1234567890</strong> | <strong>abc@xyz</strong>
        </td>

        </tr>
        </tbody>
        </table>
        ';
        $html .= '
        <table>
        <tbody>
        <tr>
        <td>Invoice to<br/>
        <strong>Demo Customer</strong>
        <br/>
        123 street, ABC Store<br/>
        Country, State, 00000
        </td>
        <td align="right">
        <strong>Total Due: $ 23,442.00/strong><br/>
        Tax ID: ABCDEFGHIJ12345<br/>
        Invoice Date: '.date('d-m-Y').'
        </td>
        </tr>
        </tbody>
        </table>
        ';
        $html .= '
        <table>
        <thead>
        <tr style="font-weight:bold;">
        <th>Item name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
        </tr>
        </thead>
        <tbody>';
		$html .= '
		<tr>
		<td style="border-bottom: 1px solid #222">Demo Item</td>
		<td style="border-bottom: 1px solid #222">$23,422.00</td>
		<td style="border-bottom: 1px solid #222">1</td>
		<td style="border-bottom: 1px solid #222">$23,422.00</td>
		</tr>
		';
        $html .='
        <tr align="right">
        <td colspan="4"><strong>Grand total: $23,422.00</strong></td>
        </tr>
        <tr>
        <td colspan="4">
        <h2>Thank you for your business.</h2><br/>
        <strong>Terms and conditions:<br/></strong>
        It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here, making it look like readable English.
        </td>
        </tr>
        </tbody>
        </table>';
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set default monospaced font
        // set margins
        $pdf->SetMargins(-1, 0, -1);
        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Set font
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 0, 0, true, '', true);
        // Demo Invoice Ends

        ob_end_clean();

        $filename = 'Invoice';


        $root_dir =  $_SERVER['DOCUMENT_ROOT'].substr(company_path(),3). '/pdf_files';
        $dir = company_path(). '/pdf_files';

        $fname = $root_dir.'/'.$filename.'.pdf';
        $pdf->Output($fname, 'F');


        if ($d = @opendir($dir)) {
        while (($file = readdir($d)) !== false) {
            if (!is_file($dir.'/'.$file) || $file == 'index.php') continue;
            $ftime = filemtime($dir.'/'.$file);
            if (time()-$ftime > 180){
                unlink($dir.'/'.$file);
            }
        }
        closedir($d);
        }

        global $Ajax;
        $Ajax->popup($dir.'/'.$filename.'.pdf'); 
        exit;
