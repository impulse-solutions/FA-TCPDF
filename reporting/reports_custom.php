<?php

define('RC_PRINT', 8);
global $reports, $dim;

// Override Reports Here
// You Can FInd This In reporting/reports_main.php

$reports->addReport(RC_CUSTOMER, 107, _('Print &Invoices'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Orientation') => 'ORIENTATION',
			_('Copy') => 'INV_COPY'
));

// Example To Register Controls

function invoice_copy($name, $type) {
	if($type == 'INV_COPY')
	    return "<select name = '".$name."'><option value='0'>"._('Original Copy For Recipient')."</option><option value='1'>"._('Duplicate Copy For Transporter ')."</option><option value='2'>"._('Triplicate Copy For Supplier')."</option></select>";
}

$reports->register_controls('invoice_copy');