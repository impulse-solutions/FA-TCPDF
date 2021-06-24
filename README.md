<?php
    $page_security = 'SA_SALESTRANSVIEW';
    ob_start();
    set_time_limit(0);
    ini_set("memory_limit", "512M");
    date_default_timezone_set("Asia/Kolkata");

    $path_to_root="..";
    //$image_path = 'C:\xampp\htdocs\accountingerp\modules\TCPDF\reporting\';
    include_once($path_to_root . "/includes/session.inc");
    include_once($path_to_root . "/includes/date_functions.inc");
    include_once($path_to_root . "/includes/data_checks.inc");
    include_once($path_to_root . "/sales/includes/sales_db.inc");
    include_once($path_to_root . "/gl/includes/db/gl_db_currencies.inc");
    include_once($path_to_root . "/inventory/includes/db/items_db.inc");
    include_once($path_to_root."/modules/TCPDF/reporting/includes/rep107_constant.inc");
    include_once($path_to_root."/modules/TCPDF/reporting/includes/db/rep107_db.php");
    require_once($path_to_root . "/modules/TCPDF/tcpdf.php");

	global $SysPrefs;
	
    function clean($string) {
       $string = str_replace(' ', ' ', $string); // Replaces all spaces with hyphens.
       return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
    }

    class MYPDF extends TCPDF {
    
	//Page header
	public function Header() {
	   $this->SetTextColor(0,0,0);
	   $this->SetFillColor(0,0,0);
	   global $copy;
	   global $company;
	   global $shipping;
	   global $gst_state;
	   global $customer_trans;
	   global $sales_order;
	   global $supply_state;
	   global $area;
	   global $currency;
	   global $customer_branch;
	   global $contact_branch;
	   
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        $this->setVisibility('screen');
        // set bacground image
        $img_file = K_PATH_IMAGES.'bg-2.jpg';
        $this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        $this->setVisibility('all');
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
        
        $primaryText = $this->SetTextColor(12,64,137);
        $secondaryText = $this->SetTextColor(28,139,184);
        $blackText = $this->SetTextColor(0,0,0);
        
        $pageWidth    = $this->getPageWidth();   // Get total page width, without margins
        $pageMargins  = $this->getMargins();     // Get all margins as array
        $headerMargin = $pageMargins['header']; // Get the header margin
        $px2          = $pageWidth - $headerMargin; // Compute x value for second point of line
        $half = $px2 /2;
        $quater = ($half /2) - 8;
        $this->SetFont('helvetica', 'I', 8);
		$this->MultiCell($quater, 5, get_invoice_type($supply_state), 0, 'L', 0, 0, '', '', true);
		$this->SetFont('helvetica', 'B', 12);
		// Title
        $xPos = $this->GetX();
        $yPos = $this->GetY();
        $this->setVisibility('screen');
        $this->SetXY($xPos, $yPos);
        $this->SetTextColor(12,64,137);
        $this->MultiCell($half, 5, 'TAX INVOICE', 0, 'C', 0, 0, '', '', true);
        $this->setVisibility('print');
        $this->SetXY($xPos, $yPos);
        $this->SetTextColor(0,0,0);
        $this->MultiCell($half, 5, 'TAX INVOICE', 0, 'C', 0, 0, '', '', true);
        $this->setVisibility('all');
		$this->SetFont('helvetica', 'I', 8);
		$this->MultiCell($quater, 5, $copy, 0, 'C', 0, 0, '', '', true);
		$this->Ln(7);
		$this->SetFont('helvetica', '', 8);
        $this->MultiCell(80, 5, 'From', 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell(30, 5, 'Invoice No', 0, 'L', 0, 0, '', '', true);
        $this->Cell(5, 0);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell(55, 5, $customer_trans['reference'], 0, 'L', 0, 0, '', '', true);
		$this->Ln(4);
		$this->SetFont('helvetica', '', 9);
		// Set font
		$this->SetFont('helvetica', 'B', 10);
                $this->MultiCell(80, 5, htmlspecialchars_decode($company['coy_name']), 0, 'L', 0, 0, '', '', true);
		$this->SetFont('helvetica', '', 9);
                $this->MultiCell(30, 5, 'Invoice Date', 0, 'L', 0, 0, '', '', true);
                $this->Cell(5, 0);
                $this->MultiCell(55, 5, sql2date($customer_trans['tran_date']), 0, 'L', 0, 0, '', '', true);
		$this->Ln(4);
		$this->SetFont('helvetica', '', 9);
                $this->MultiCell(80, 5, htmlspecialchars_decode($company['postal_address']), 0, 'L', 0, 0, '', '', true);
                $this->MultiCell(30, 5, 'Reference No', 0, 'L', 0, 0, '', '', true);
                $this->Cell(5, 0);
                $this->MultiCell(55, 5, $sales_order['customer_ref'], 0, 'L', 0, 0, '', '', true);
                $this->Ln(4);
                $this->MultiCell(80, 5, '', 0, 'L', 0, 0, '', '', true);
                $this->MultiCell(30, 5, 'Place of supply', 0, 'L', 0, 0, '', '', true);
                $this->Cell(5, 0);
                $this->MultiCell(60, 5, $gst_state[$supply_state], 0, 'L', 0, 0, '', '', true);
                $this->Ln(4);
                $this->MultiCell(80, 5, '', 0, 'L', 0, 0, '', '', true);
                $this->MultiCell(30, 5, 'Due Date', 0, 'L', 0, 0, '', '', true);
                $this->Cell(5, 0);
                $this->MultiCell(60, 5, sql2date($customer_trans['due_date']), 0, 'L', 0, 0, '', '', true);
        $this->Ln(8);
		$this->SetFont('helvetica', '', 9);
                $this->MultiCell(70, 5, $company['phone'].', '.$company['email'], 0, 'L', 0, 0, '', '', true);
                $this->MultiCell(16, 5, '   GSTIN:', 0, 'L', 0, 0, '', '', true);
                $this->Cell(2, 0);
                $this->SetFont('helvetica', 'B', 9);
                $this->MultiCell(50, 5, $company['gst_no'], 0, 'L', 0, 0, '', '', true);
                $this->SetFont('helvetica', '', 9);
                $this->MultiCell(16, 5, '', 0, 'L', 0, 0, '', '', true);//PAN
                $this->Cell(2, 0);
                $this->SetFont('helvetica', 'B', 9);
                $this->MultiCell(48, 5, '', 0, 'L', 0, 0, '', '', true//gst_no
                );
                $this->SetFont('helvetica', '', 9);
        $this->Ln(5);    

        $p1x   = $this->getX();
        $p1y   = $this->getY();
        $p2x   = $px2 - 5;
        $p2y   = $p1y;  // Use same y for a straight line
        $colorLine = array('width' => 0.5, 'color' => array(111,177,203));
        $blackLine = array('width' => 0.5, 'color' => array(0,0,0));
        $this->setVisibility('screen');
        $this->Line($p1x, $p1y, $p2x, $p2y, $colorLine);
        $this->setVisibility('print');
        $this->Line($p1x, $p1y, $p2x, $p2y, $blackLine);
        $this->setVisibility('all');
        $this->SetFont('helvetica', '', 9);
        // define barcode style
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );

        // Right position
        $style['position'] = 'R';
        $this->write2DBarcode($customer_trans['reference'], 'QRCODE,L', 7, 7, 30, 30, $style, 'N');
        
        $this->Ln(5);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell(10, 5, 'Group', 0, 'L', 0, 0, '', '', true);
        $this->Cell(2, 0);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell(($px2 / 4) - 20, 5, get_sales_group_name($customer_trans['group_no']), 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell(18, 5, 'Area', 0, 'R', 0, 0, '', '', true);
        $this->Cell(2, 0);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell(($px2 / 4) - 20, 5, $area, 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell(18, 5, 'Pricelist', 0, 'R', 0, 0, '', '', true);
        $this->Cell(2, 0);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell(($px2 / 4) - 20, 5, $customer_trans['sales_type'], 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell(18, 5, 'Currency', 0, 'R', 0, 0, '', '', true);
        $this->Cell(2, 0);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell(($px2 / 4) - 20, 5, $currency['currency'], 0, 'L', 0, 0, '', '', true);
        $this->Ln(5);
        $p1x   = $this->getX();
        $p1y   = $this->getY();
        $p2x   = $px2 - 5;
        $p2y   = $p1y;  // Use same y for a straight line
        $this->setVisibility('screen');
        $this->Line($p1x, $p1y, $p2x, $p2y, $colorLine);
        $this->setVisibility('print');
        $this->Line($p1x, $p1y, $p2x, $p2y, $blackLine);
        $this->setVisibility('all');
        $this->Ln(4);
        $this->SetFont('helvetica', '', 8);
        $this->MultiCell($quater, 5, 'Billing Address', 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->MultiCell($quater, 5, 'Kind Atten.', 0, 'L', 0, 0, '', '', true);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);//Shipping Address
        $this->Cell(4, 0);
        $this->MultiCell($quater, 5, 'Logistics', 0, 'L', 0, 0, '', '', true);
        $this->Ln(3);
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell($quater, 5, htmlspecialchars_decode($customer_trans['DebtorName']), 0, 0, 'L', 0, '', 1);
        $this->Cell(4, 5);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell($quater, 5, htmlspecialchars_decode($contact_branch['name']), 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell($quater, 5, '', 0, 0, 'L', 0, '', 1);//br_name
        $this->Cell(4, 5);
        $this->SetFont('helvetica', '', 9);
        $this->Cell($quater, 5, htmlspecialchars_decode($shipping['shipping_company']), 0, 0, 'L', 0, '', 1);
        $this->Ln(5);
        $this->MultiCell($quater, 5, htmlspecialchars_decode($customer_trans['address']), 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);//br_address
        $this->Cell(4, 0);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);
        $this->Ln(4);
        $this->SetFont('helvetica', '', 8);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->MultiCell($quater, 5, 'Mobile No.', 0, 'L', 0, 0, '', '', true);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->MultiCell(($quater / 2) + 4, 5, 'Docket #', 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->MultiCell($quater / 2, 5, 'Vehicle No.', 0, 'L', 0, 0, '', '', true);
        $this->Ln(4);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', '', 9);
        $this->Cell(4, 0);
        $this->MultiCell($quater, 5, $contact_branch['phone'], 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell(($quater / 2) + 4, 5, $shipping['shipment_tracking_no'], 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->MultiCell($quater / 2, 5, $shipping['shipment_vehicle_no'], 0, 'L', 0, 0, '', '', true);
        $this->Ln(8);
        $this->SetFont('helvetica', '', 8);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);
                $this->Cell(4, 0);
        $this->MultiCell($quater, 5, 'Sales Rep.', 0, 'L', 0, 0, '', '', true);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->MultiCell(($quater / 2) + 4, 5, 'Package', 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->MultiCell($quater / 2, 5, 'E-Way Bill', 0, 'L', 0, 0, '', '', true);
        $this->Ln(4);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', '', 9);
        $this->Cell(4, 0);
        $this->MultiCell($quater, 5, htmlspecialchars_decode($customer_branch['salesman_name']), 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->SetFont('helvetica', '', 9);
        if($shipping['shipment_package'] > 0) {
        $this->MultiCell(($quater / 2) + 4, 5, $shipping['shipment_items'].' Items / '.$shipping['shipment_package'].' '.$shipping['abbr'], 0, 'L', 0, 0, '', '', true);
        } else {
        $this->MultiCell(($quater / 2) + 4, 5, '--', 0, 'L', 0, 0, '', '', true);
        }
        $this->Cell(4, 0);
        $this->MultiCell($quater / 2, 5, $shipping['shipment_eway_bill'], 0, 'L', 0, 0, '', '', true);
        $this->Ln(8);
        if($customer_trans['tax_id'] != '') {
        $this->SetFont('helvetica', '', 8);
        $this->MultiCell($quater, 5, 'GSTIN', 0, 'L', 0, 0, '', '', true);
                $this->Cell(4, 0);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);//PAN
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);//GSTIN
        $this->Cell(4, 0);
        $this->MultiCell($quater / 2, 5, '', 0, 'L', 0, 0, '', '', true);//PAN
        $this->Ln(4);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell($quater, 5, $customer_trans['tax_id'], 0, 'L', 0, 0, '', '', true);
        $this->Cell(4, 0);
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);//tax_id
        $this->MultiCell($quater, 5, '', 0, 'L', 0, 0, '', '', true);//
        $this->Cell(4, 0);
        $this->MultiCell($quater / 2, 5, '', 0, 'L', 0, 0, '', '', true);//tax_id
        }
        $this->Ln(6);
        $srWidth = 8;
        $pad = 2;
        $desWidth = 60;
        $qtyWidth = 15;
        $rateWidth = 17;
        $taxableWidth = 17;
        $cgstWidth = 17;
        $sgstWidth = 17;
        $totalWidth = 23;

        $pageMargins  = $this->getMargins();     // Get all margins as array
        $footerMargin = $pageMargins['footer']; // Get the header margin
        $px2          = $pageWidth - $footerMargin; // Compute x value for
        $p1x   = $this->getX();
        $p1y   = $this->getY();
        $p2x   = $px2;
        $p2y   = $p1y;  // Use same y for a straight line
        $colorLine = array('width' => 0.5, 'color' => array(111,177,203));
        $blackLine = array('width' => 0.5, 'color' => array(0,0,0));
        $this->setVisibility('screen');
        $this->Line($p1x, $p1y, $p2x, $p2y, $colorLine);
        $this->setVisibility('print');
        $this->Line($p1x, $p1y, $p2x, $p2y, $blackLine);
        $this->setVisibility('all');
        
        $this->Ln(1);
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell($srWidth, 5, 'Sr.', '', 'C', 0, 0, '', '', true);
        $this->Cell($pad, 5, '', '');
        $this->MultiCell($desWidth, 5, 'Description', '', 'L', 0, 0, '', '', true);
        $this->Cell($pad, 5, '', '');
        $this->MultiCell($qtyWidth, 5, 'Qty', '', 'R', 0, 0, '', '', true);
        $this->Cell($pad, 5, '', '');
        $this->MultiCell($rateWidth, 5, 'Rate', '', 'R', 0, 0, '', '', true);
        $this->Cell($pad, 5, '', '');
        $this->MultiCell($taxableWidth, 5, 'Taxable', '', 'R', 0, 0, '', '', true);
        $this->Cell($pad, 5, '', '');
        $this->MultiCell($cgstWidth, 5, 'CGST', '', 'R', 0, 0, '', '', true);
        $this->Cell($pad, 5, '', '');
        $this->MultiCell($sgstWidth, 5, 'SGST', '', 'R', 0, 0, '', '', true);
        $this->Cell($pad, 5, '', '');
        $this->MultiCell($totalWidth, 5, 'Total Amount', '', 'R', 0, 0, '', '', true);
        $this->Cell($pad, 5, '', '');
        $this->Ln(5);
        
        $pageMargins  = $this->getMargins();     // Get all margins as array
        $footerMargin = $pageMargins['footer']; // Get the header margin
        $px2          = $pageWidth - $footerMargin; // Compute x value for
        $p1x   = $this->getX();
        $p1y   = $this->getY();
        $p2x   = $px2;
        $p2y   = $p1y;  // Use same y for a straight line
        $colorLine = array('width' => 0.5, 'color' => array(111,177,203));
        $blackLine = array('width' => 0.5, 'color' => array(0,0,0));
        $this->setVisibility('screen');
        $this->Line($p1x, $p1y, $p2x, $p2y, $colorLine);
        $this->setVisibility('print');
        $this->Line($p1x, $p1y, $p2x, $p2y, $blackLine);
        $this->setVisibility('all');
	}

	// Page footer
	public function Footer() {
	    global $customer_trans;
	    $this->SetTextColor(0,0,0);
	    $this->SetFillColor(0,0,0);
	    global $bank_account, $customer_info, $dec, $last_page, $currency, $customer_reference;
	    if(strpos($bank_account['bank_address'], '/') !== false) {
	        $bank_address = explode('/', $bank_account['bank_address']);
	    }
	    else {
	        $bank_address = array('NA','NA');
	    }
	    $tos = "1. Goods once sold will not be taken back. Our responsibility ceases the momenent the goods leave our premises." . "\n" . "2. The goods will be dispatched at the purchaser's risk." . "\n" . "3. Interest @ 24% p.a. shall be payable on the invoice amount, if payment is not made within the due date." . "\n" . "4. Cheque dishonored / bounce charge INR 500 per instance." . "\n" . "5. All disputes are subject to Pune jurisdiction only.";
	    $declaration = "DECLARATION: Certified that the particulars given above are true and correct and the amount indicated represents the price actually charged and that there is no flow of additional consideration directly or indirectly from the buyer.";
	    
	    if($last_page == TRUE) {
	    $this->SetY(-77.5);
	    $this->Cell(25, 5, '', 0);
        // new style
        $style = array(
            'border' => 2,
            'padding' => 2,
            'fgcolor' => array(0,0,0),
            'bgcolor' => array(255,255,255)
        );
	    $this->write2DBarcode('upi://pay?pa=Q38601373@ybl&pn=PhonePeMerchant&mc=0000&mode=02&purpose=00&orgid=180001&sign=MEUCIBfRJOFiKalMe/akcadyn0+oB8eCEs4RpWBdUFW9ySTvAiEA00LZotCJiadPBJVu7dxjggdC6mA48Z3zQpw9Z2907vE='.number_format($customer_info['Balance'], $dec,'.','').'&mam=0.01&tn='.$customer_reference.'&cu=INR', 'QRCODE,H', $this->GetX(), $this->GetY(), 29, 29, $style, 'N');
	   // $this->SetY(-74);
	   // $this->SetFont('helvetica', 'B', 7);
	   // $this->Cell(16, 5, 'Scan QR Code', 0, 0, 'L', 0, '', 0, false, 'C', 'C');
	    $this->SetY(-69);
	    $phoneX = $this->GetX();
        $phoneY = $this->GetY();
	    $this->SetY(-59);
	    $upiX = $this->GetX();
        $upiY = $this->GetY();
        $this->SetY(-59);
        $this->SetFont('helvetica', 'B', 7);
        $this->Cell(63, 5, '', 0);
        $this->Cell(43, 5, 'Balance Due', 0);
        $this->Cell(75, 5,'Internet Banking, Credit / Debit Card', 0);
        $this->setVisibility('screen');
        $this->ImageSVG(K_PATH_IMAGES.'phonePe-color.svg', $phoneX, $phoneY, $w=22, $h=11, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        $this->ImageSVG(K_PATH_IMAGES.'upi-color.svg', $upiX, $upiY, $w=24, $h=12, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        $this->setVisibility('print');
        $this->ImageSVG(K_PATH_IMAGES.'phonePe.svg', $phoneX, $phoneY, $w=22, $h=11, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        $this->ImageSVG(K_PATH_IMAGES.'upi.svg', $upiX, $upiY, $w=24, $h=12, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        $this->setVisibility('all');
        $this->Ln(6);
        $this->Cell(63, 5, '', 0);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(43, 5, $currency['curr_abrev'].' '.number_format2($customer_info['Balance'], $dec), 0);
        $this->SetFont('helvetica', '', 9);
        $this->Cell(75, 5, '', 0);
        $this->SetY(-58);
	    $cardX = 165;
        $cardY = $this->GetY();
        $this->setVisibility('screen');
        $this->ImageSVG(K_PATH_IMAGES.'visa-color.svg', $cardX, $cardY, $w=24, $h=12, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        $this->ImageSVG(K_PATH_IMAGES.'mastercard-color.svg', $cardX + 17, $cardY, $w=24, $h=12, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        $this->setVisibility('print');
        $this->ImageSVG(K_PATH_IMAGES.'visa.svg', $cardX, $cardY, $w=24, $h=12, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        $this->ImageSVG(K_PATH_IMAGES.'mastercard.svg', $cardX + 17, $cardY, $w=24, $h=12, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        $this->setVisibility('all');
	    }
		// Position at 46 mm from bottom
		$this->SetY(-46);
        $pageWidth    = $this->getPageWidth();   // Get total page width, without margins
        
        $pageMargins  = $this->getMargins();     // Get all margins as array
        $footerMargin = $pageMargins['footer']; // Get the header margin
        $px2          = $pageWidth - $footerMargin; // Compute x value for second point of line
        
        $p1x   = $this->getX();
        $p1y   = $this->getY();
        $p2x   = $px2;
        $p2y   = $p1y;  // Use same y for a straight line
        $colorLine = array('width' => 0.5, 'color' => array(111,177,203));
        $blackLine = array('width' => 0.5, 'color' => array(0,0,0));
        $this->setVisibility('screen');
        $this->Line($p1x, $p1y, $p2x, $p2y, $colorLine);
        $this->setVisibility('print');
        $this->Line($p1x, $p1y, $p2x, $p2y, $blackLine);
        $this->setVisibility('all');
        
        $this->Ln(3);
        
        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell($px2 / 3, 5, 'Bank Details', 0, 'L', 0, 0, '', '', true);
        $this->MultiCell($px2 - ($px2 / 3), 5, 'Terms & Condition', 0, 'L', 0, 0, '', '', true);
        $this->Ln(4);
        $this->SetFont('helvetica', '', 8);
        $sCol = ($px2 / 3) - 30;
        $this->MultiCell($sCol, 5, 'Account Number', 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(($px2 / 3) - $sCol, 5, $bank_account['bank_account_number'], 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', '', 7);
        $this->MultiCell(($px2 - ($px2 / 3)) - 7, 5, $tos, 0, 'L', 0, 0, '', '', true);
        $this->SetFont('helvetica', '', 8);
        $this->Ln(4);
        $this->MultiCell($sCol, 5, 'IFSC', 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(($px2 / 3) - $sCol, 5, $bank_address[0], 0, 'L', 0, 0, '', '', true);
        $this->MultiCell($px2 - ($px2 / 3), 5, '', 0, 'L', 0, 0, '', '', true);
        $this->Ln(4);
        $this->MultiCell($sCol, 5, 'Bank Name', 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(($px2 / 3) - $sCol, 5, $bank_account['bank_name'], 0, 'L', 0, 0, '', '', true);
        $this->MultiCell($px2 - ($px2 / 3), 5, '', 0, 'L', 0, 0, '', '', true);
        $this->Ln(4);
        $this->MultiCell($sCol, 5, 'Branch Name', 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(($px2 / 3) - $sCol, 5, $bank_address[1], 0, 'L', 0, 0, '', '', true);
        $this->MultiCell($px2 - ($px2 / 3), 5, '', 0, 'L', 0, 0, '', '', true);

        $this->Ln(6);
        $this->SetFont('helvetica', '', 8);
        $this->MultiCell($px2 - 5, 5, $declaration, 0, 'L', 0, 0, '', '', true);
        
        $this->Ln(10);

        $p1x   = $this->getX();
        $p1y   = $this->getY();
        $p2x   = $px2;
        $p2y   = $p1y;  // Use same y for a straight line

        $this->setVisibility('screen');
        $this->Line($p1x, $p1y, $p2x, $p2y, $colorLine);
        $this->setVisibility('print');
        $this->Line($p1x, $p1y, $p2x, $p2y, $blackLine);
        $this->setVisibility('all');
		// Set font
		$this->SetFont('helvetica', '', 8);
		// Page number
		$this->Ln(3);
        if (empty($this->pagegroups)) {
           $this->MultiCell($p2x - 5, 8, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, 'C', 0, 0, '', '', true);
        } else {
        		$this->MultiCell($p2x - 5, 8, 'Page '.$this->getPageNumGroupAlias().' of '.$this->getPageGroupAlias(), 0, 'C', 0, 0, '', '', true);
        }
        $last_page = FALSE;
        unset($customer_trans);
	}
	
    public function MultiRow($left_size, $left, $right_size, $right) {
        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0)

        $page_start = $this->getPage();
        $y_start = $this->GetY();

        // write the left cell
        $this->MultiCell($left_size, 0, $left, 0, 'L', 0, 2, '', '', true, 0);
        $page_end_1 = $this->getPage();
        $y_end_1 = $this->GetY();


        $this->setPage($page_start);

        // write the right cell
        $this->MultiCell($right_size, 0, $right, 0, 'R', 0, 1, $this->GetX() ,$y_start, true, 0);

        $page_end_2 = $this->getPage();
        $y_end_2 = $this->GetY();

        // set the new row position by case
        if (max($page_end_1,$page_end_2) == $page_start) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif ($page_end_1 == $page_end_2) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif ($page_end_1 > $page_end_2) {
            $ynew = $y_end_1;
        } else {
            $ynew = $y_end_2;
        }

        $this->setPage(max($page_end_1,$page_end_2));
        $this->SetXY($this->GetX(),$ynew);
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator('Impulse Solutions');
$pdf->SetAuthor('Mohsin Frioz Mujawar');
$pdf->SetTitle('INVOICE');
$pdf->SetSubject('Invoice');
$pdf->SetKeywords('Urwa Billing Software');

// //set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
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
while($row = db_fetch($range))
{

        global $company;
        global $currency;
        global $customer_trans;
        global $customer_trans_details;
        global $gst_state;
        global $supply_state;
        global $bank_account;
        global $copy;
        global $last_page;
        global $dec;
        global $shipping;
		if (!exists_customer_trans(ST_SALESINVOICE, $row['trans_no']))
			continue;
			
		$customer_trans = get_customer_trans($row['trans_no'], ST_SALESINVOICE);
		if ($customer && $customer_trans['debtor_no'] != $customer) {
			continue;
		}
		if ($invoice_currency != ALL_TEXT && $customer_trans['curr_code'] != $invoice_currency) {
			continue;
		}
        $c = array('Original Copy For Recipient', 'Duplicate Copy For Transporter',  'Triplicate Copy For Supplier');
        $copy = $c[$i];
        
        $company = get_company_Pref();
        $shipping = get_shipping_info($row['trans_no'], ST_SALESINVOICE);
        $gst_state = get_gst_state();
        $sales_order = get_sales_order_header($customer_trans['order_'], ST_SALESORDER);
        if(strlen($customer_trans['tax_id']) == 15) {
        $supply_state = substr($customer_trans['tax_id'], 0, 2);
        }
        else {
        $supply_state = 'DEFAULT';
        }

        $area = get_area_name($customer_trans['area']);
        $currency = get_currency($company['curr_default']);
        $customer_branch = get_branch($customer_trans["branch_code"]);
        $contact_branch = get_branch_contacts($customer_branch['branch_code'], 'invoice', $customer_branch['debtor_no'], true);
        $customer_trans_details = get_customer_trans_details(ST_SALESINVOICE, $customer_trans['trans_no']);

        $bank_account = get_default_bank_account($customer_trans['curr_code']);
        $dec = user_price_dec();

        $srWidth = 8;
        $pad = 2;
        $desWidth = 60;
        $qtyWidth = 15;
        $rateWidth = 17;
        $taxableWidth = 17;
        $cgstWidth = 17;
        $sgstWidth = 17;
        $totalWidth = 23;

        $x = 1;
    
        $sign = 1;
        $discount_count = 0;
        $discount_amount = 0;
        $subTotal = 0;
        // add a page
        $pdf->startPageGroup();
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);
        $pdf->AddPage();
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFillColor(0,0,0);
        $pdf->Ln(2);
        while($product=db_fetch($customer_trans_details)) {
		if ($product["quantity"] == 0)
			continue;

		$taxable = round2($sign * ((1 - $product["discount_percent"]) * $product["unit_price"] * $product["quantity"]),
		   user_price_dec());
		$subTotal += $taxable;
        $gst = (($product["unit_tax"] - ($sign * ($product['discount_percent']) * $product['unit_tax']))) * $product["quantity"] / 2;
        $total = $taxable + ($gst * 2);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->MultiCell($srWidth, 5, $x, 0, 'C', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($desWidth, 5, htmlspecialchars_decode(clean(substr($product['description'], 0, 38))), 0, 'L', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($qtyWidth, 5, $product['quantity'], 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($rateWidth, 5, $product['unit_price'], 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
		if ($SysPrefs->alternative_tax_include_on_docs() == 1) { 
        $pdf->MultiCell($taxableWidth, 5, number_format2($taxable, $dec), 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($cgstWidth, 5, number_format2($gst, $dec), 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($sgstWidth, 5, number_format2($gst, $dec), 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($totalWidth, 5, number_format2($total, $dec), 0, 'R', 0, 0, '', '', true);
        } else {
        $pdf->MultiCell($taxableWidth, 5, number_format2($taxable - ($gst*2), $dec), 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($cgstWidth, 5, number_format2($gst, $dec), 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($sgstWidth, 5, number_format2($gst, $dec), 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($totalWidth, 5, number_format2($total - ($gst*2), $dec), 0, 'R', 0, 0, '', '', true);
		}
        $pdf->Ln(4);
        $stock = get_item($product['stock_id']);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->MultiCell($srWidth, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($desWidth, 5, 'ITEM CODE '.$stock['stock_id'].'   '.$stock['long_description'], 0, 'L', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($qtyWidth, 5, strtoupper($product['units']), 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        if($product['discount_percent'] > 0) {
        $discount = 'Dis. '.($product['discount_percent'] * 100).'%';
        $discount_amount += ($product["unit_price"] * $product["quantity"]) - $taxable;
        $discount_count += 1;
        }
        else {
            $discount= '';
        }
		//$igst = $product['unit_tax'];
        $pdf->MultiCell($rateWidth, 5, $discount, 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($taxableWidth, 5, '', 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
		$pdf->MultiCell($cgstWidth, 5, round($igst / 2).'%', 0, 'R', 0, 0, '', '', true);
        //$pdf->MultiCell($cgstWidth, 5, '9%', 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
		$pdf->MultiCell($sgstWidth, 5, round($igst / 2).'%', 0, 'R', 0, 0, '', '', true);
        //$pdf->MultiCell($sgstWidth, 5, '9%', 0, 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5);
        $pdf->MultiCell($totalWidth, 5, ' ', 0, 'R', 0, 0, '', '', true);
       
        $pdf->Ln(6);
        $x++;
        }
        $pageWidth    = $pdf->getPageWidth();
        $pageMargins  = $pdf->getMargins();     // Get all margins as array
        $footerMargin = $pageMargins['footer']; // Get the header margin
        $px2          = $pageWidth - $footerMargin; // Compute x value for
        $p1x   = $pdf->getX();
        $p1y   = $pdf->getY();
        $p2x   = $px2;
        $p2y   = $p1y;  // Use same y for a straight line
        $colorLine = array('width' => 0.5, 'color' => array(111,177,203));
        $blackLine = array('width' => 0.5, 'color' => array(0,0,0));
        $pdf->setVisibility('screen');
        $pdf->Line($p1x, $p1y, $p2x, $p2y, $colorLine);
        $pdf->setVisibility('print');
        $pdf->Line($p1x, $p1y, $p2x, $p2y, $blackLine);
        $pdf->setVisibility('all');
        $pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($pad, 5, '', '');
        $pdf->MultiCell($srWidth + $desWidth, 5, 'TOTAL', '', 'L', 0, 0, '', '', true);
        $pdf->Cell($pad, 5, '', '');
        $pdf->MultiCell($qtyWidth, 5, '', '', 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5, '', '');
        $pdf->MultiCell($rateWidth, 5, '', '', 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5, '', '');
        $pdf->MultiCell($taxableWidth, 5, number_format2($customer_trans['ov_amount'], $dec), '', 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5, '', '');
        $pdf->MultiCell($cgstWidth, 5, number_format2($customer_trans['ov_gst'] / 2, $dec), '', 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5, '', '');
        $pdf->MultiCell($sgstWidth, 5, number_format2($customer_trans['ov_gst'] / 2, $dec), '', 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5, '', '');
        $pdf->MultiCell($totalWidth, 5, number_format2($customer_trans['Total'] - $customer_trans["ov_freight"], $dec), '', 'R', 0, 0, '', '', true);
        $pdf->Cell($pad, 5, '', '');
        $pdf->setVisibility('screen');
        $pdf->Ln(5);
        $pageWidth    = $pdf->getPageWidth();
        $pageMargins  = $pdf->getMargins();     // Get all margins as array
        $footerMargin = $pageMargins['footer']; // Get the header margin
        $px2          = $pageWidth - $footerMargin; // Compute x value for
        $p1x   = $pdf->getX();
        $p1y   = $pdf->getY();
        $p2x   = $px2;
        $p2y   = $p1y;  // Use same y for a straight line
        $pdf->Line($p1x, $p1y, $p2x, $p2y, $colorLine);
        $pdf->setVisibility('print');
        $pdf->Line($p1x, $p1y, $p2x, $p2y, $blackLine);
        $pdf->setVisibility('all');
        $pdf->Ln(3);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(127, 5,'Notes', 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiRow(38,'Shipping', 25, number_format2($sign*$customer_trans["ov_freight"],$dec)); 
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(127, 5, $customer_trans['memo_'], 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiRow(38,'Subtotal', 25, number_format2($subTotal,$dec));
        
        $customer_info = get_customer_details($customer_trans['debtor_no']);
        $customer_reference = $customer_trans['reference'];
		$tax_items = get_trans_tax_details(ST_SALESINVOICE, $customer_trans['trans_no']);
		$first = true;
		
		while ($tax_item = db_fetch($tax_items))
		{
			if ($tax_item['amount'] == 0)
				continue;
			$DisplayTax = number_format2($sign*$tax_item['amount'], $dec);

			if ($SysPrefs->suppress_tax_rates() == 1)
				$tax_type_name = $tax_item['tax_type_name'];
			else
				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";
				
    			if ($customer_trans['tax_included'])
    			{
    				if ($SysPrefs->alternative_tax_include_on_docs() == 1)
    				{
    					if ($first)
    					{
                        	$pdf->SetFont('helvetica', '', 9);
                            $pdf->Cell(127, 5,'', 0);
                            $pdf->MultiRow(38, 'Total Tax Excluded', 25, number_format2($sign*$tax_item['net_amount'], $dec));
    					}
                    	$pdf->SetFont('helvetica', '', 9);
                        $pdf->Cell(127, 5,'', 0);
                        $pdf->MultiRow(38, $tax_type_name, 25, $DisplayTax);
						$first = false;
    				}
    				else
                    	$pdf->SetFont('helvetica', '', 9);
                        $pdf->Cell(127, 5,'', 0);
                        $pdf->MultiRow(38, 'Included ('.$tax_type_name.')', 25, $DisplayTax);
				}
    			else
    			{
                	$pdf->SetFont('helvetica', '', 9);
                    $pdf->Cell(127, 5,'', 0);
                    $pdf->MultiRow(38, $tax_type_name, 25, $DisplayTax);
				}
		}
		$DisplayTotal = number_format2($sign*($customer_trans["ov_freight"] + $customer_trans["ov_gst"] +
				$customer_trans["ov_amount"]+$customer_trans["ov_freight_tax"]),$dec);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(32, 5,'Payment Terms', 0);
        $pdf->SetFont('helvetica', 'I', 9);
        $pdf->Cell(95, 5, $customer_info['terms'], 0);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->MultiRow(38,'TOTAL', 25, $DisplayTotal);
        if($discount_count > 0) {
        $pdf->Ln(4);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(127, 5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->MultiRow(38,'Discount Received', 25, $discount_amount);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(127, 5);
        $pdf->MultiRow(38,'On '.$discount_count.' Items', 25, 'Included In TOTAL');
        }
        $pdf->Ln(6);
        
		$words = amount_in_words($customer_trans['prepaid'] ? $customer_trans['prep_amount'] : $customer_trans['Total']
		, $currency);
        
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(127, 5,'Total Amount (In Words)', 0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiRow(5,null, 58, 'Bharathi Mobiles & Traders');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->MultiCell(140, 5, $words, 0, 'L', 0, 0, '', '', true);
        $pdf->Ln(1);
        $pdf->Cell(165, 5,'', 0);
        $pdf->SetXY($pdf->GetX(), $pdf->GetY());
        $pdf->setVisibility('screen');
        //Stamp svg file on below K_PATH_IMAGES//
        $pdf->ImageSVG(K_PATH_IMAGES.'MSR-2130846316.svg', '', '', $w=25, $h=25, $link='', $align='R', $palign='', $border=0, $fitonpage=false);
        $pdf->setVisibility('print');
        $pdf->ImageSVG(K_PATH_IMAGES.'MSR-2130846316.svg', '', '', $w=25, $h=25, $link='', $align='R', $palign='', $border=0, $fitonpage=false);
        $pdf->setVisibility('all');
        $pdf->Ln(8);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell(63, 5, '', 0);
        $pdf->Cell(43, 5, 'Generated By', 0);
        $pdf->Cell(75, 5,'Date and Time', 0);
        $pdf->Ln(6);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(63, 5, '', 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(43, 5, $_SESSION["wa_current_user"]->name, 0);
        $pdf->Cell(75, 5, date('d M Y  H:i:s A'), 0);
        $pdf->Ln(12);
        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->MultiRow(5,null, 132, 'This is computer generated invoice, no stamp or signature is required.', 0, 'L');
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell(127, 5,'', 0);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->MultiRow(5,null, 58, 'Authorised Signatory');
        $pdf->SetFont('helvetica', '', 9);

        if (empty($pdf->pagegroups)) {
           $last_page = (number_format($pdf->getAliasNumPage()) == (number_format($pdf->getAliasNbPages()) ? TRUE : FALSE));
        } else {
        	$last_page = (number_format($pdf->getPageNumGroupAlias()) == (number_format($pdf->getPageGroupAlias()) ? TRUE : FALSE));
        }
    
// ---------------------------------------------------------
}
ob_end_clean();

    $filename = str_replace('/','-',$customer_trans['reference']);
    if ($filename == FALSE) {
        $filename = 'Invoice';
    }

	$root_dir =  $_SERVER['DOCUMENT_ROOT'].'accounting/'.substr(company_path(),3). '/pdf_files';
	$dir = company_path(). '/pdf_files';

	$fname = $root_dir.'/'.$filename.'.pdf';
	$pdf->Output($fname, 'F');


    if ($d = @opendir($dir)) {
    	while (($file = readdir($d)) !== false) {
    		if (!is_file($dir.'/'.$file) || $file == 'index.php') continue;
    	// then check to see if this one is too old
    		$ftime = filemtime($dir.'/'.$file);
    	 // seems 3 min is enough for any report download, isn't it?
    		if (time()-$ftime > 180){
    			unlink($dir.'/'.$file);
    		}
    	}
    	closedir($d);
    }
    
    global $Ajax;
    $Ajax->popup($dir.'/'.$filename.'.pdf'); 
    exit;
