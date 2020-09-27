<?php
/*=================================================================\
|                        FA TCPDF                                 |
|-----------------------------------------------------------------|
|   Creator: Mohsin Mujawar<contact@impulsesolutions.in>          |
|   Date :   31-Aug-2020                                          |
|   Description: TCPDF MODULE FOR FRONT ACCOUNTING 2.4.8          |
|   Free software under GNU GPL                                   |
|                                                                 |
\==================================================================*/
define ('IMPULSE_TCPDF', 251<<8);

class hooks_TCPDF extends hooks {

	function __construct() {
		$this->module_name = 'TCPDF';
	}
	
    function install_access() {
        $security_sections[IMPULSE_TCPDF] =  _('TCPDF');
        $security_areas['PULSE_PDF'] = array(IMPULSE_TCPDF|1, _('Allow PDF Printing'));
        return array($security_areas, $security_sections);
    }
	
    function activate_extension($company, $check_only=true) {
        global $db_connections;
        
        $updates = array( 'update.sql' => array('TCPDF'));
 
        return $this->update_databases($company, $updates, $check_only);
    }
    
    function deactivate_extension($company, $check_only=true) {
        global $db_connections;

        $updates = array('remove.sql' => array('TCPDF'));

        return $this->update_databases($company, $updates, $check_only);
    }
	
}
	