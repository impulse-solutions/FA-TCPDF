<?php
    // Write Rep107 Related Functions Here.
    function get_invoice_range($from, $to)
    {
        $ref = ($GLOBALS['SysPrefs']->prefs['print_invoice_no'] == 1 ? "trans_no" : "reference");

        $sql = "SELECT trans.trans_no, trans.reference
            FROM ".TB_PREF."debtor_trans trans 
                LEFT JOIN ".TB_PREF."voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
            WHERE trans.type=".ST_SALESINVOICE
                ." AND ISNULL(voided.id)"
                ." AND trans.trans_no BETWEEN ".db_escape($from)." AND ".db_escape($to)			
            ." ORDER BY trans.tran_date, trans.$ref";

        return db_query($sql, "Cant retrieve invoice range");
    }

    function get_number_to_words($number) 
    { 
        $Bn = floor($number / 1000000000); /* Billions (giga) */ 
        $number -= $Bn * 1000000000; 
        $Gn = floor($number / 1000000);  /* Millions (mega) */ 
        $number -= $Gn * 1000000; 
        $kn = floor($number / 1000);     /* Thousands (kilo) */ 
        $number -= $kn * 1000; 
        $Hn = floor($number / 100);      /* Hundreds (hecto) */ 
        $number -= $Hn * 100; 
        $Dn = floor($number / 10);       /* Tens (deca) */ 
        $n = $number % 10;               /* Ones */

        $res = ""; 

        if ($Bn) 
            $res .= get_number_to_words($Bn) . " Billion"; 
        if ($Gn) 
            $res .= (empty($res) ? "" : " ") . get_number_to_words($Gn) . " Million"; 
        if ($kn) 
            $res .= (empty($res) ? "" : " ") . get_number_to_words($kn) . " Thousand"; 
        if ($Hn) 
            $res .= (empty($res) ? "" : " ") . get_number_to_words($Hn) . " Hundred"; 

        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
            "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
            "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", 
            "Nineteen"); 
        $tens = array("", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", 
            "Seventy", "Eighty", "Ninety"); 

        if ($Dn || $n) 
        { 
            if (!empty($res)) 
                $res .= " "; 
            if ($Dn < 2) 
                $res .= $ones[$Dn * 10 + $n]; 
            else 
            { 
                $res .= $tens[$Dn]; 
                if ($n) 
                    $res .= " " . $ones[$n]; 
            } 
        } 

        if (empty($res)) 
            $res = "Zero"; 
        return $res; 
    } 

    function amount_in_words($amount,$currency)
    {

        if ($amount < 0 || $amount > 999999999999)
            return "";
        $dec = user_price_dec();
        if ($dec > 0)
        {
            $divisor = pow(10, $dec);
            $frac = round2($amount - floor($amount), $dec) * $divisor;
            $frac = sprintf("%0{$dec}d", round2($frac, 0));
            $and = _(" and ");
            $frac = ' '. $currency['currency']. $and.  get_number_to_words(intval($frac));
        }
        else
            $frac = "";
        return get_number_to_words(intval($amount)) . $frac. ' '. $currency['hundreds_name'];
    }