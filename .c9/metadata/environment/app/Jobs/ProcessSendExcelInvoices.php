{"filter":false,"title":"ProcessSendExcelInvoices.php","tooltip":"/app/Jobs/ProcessSendExcelInvoices.php","undoManager":{"mark":7,"position":7,"stack":[[{"start":{"row":217,"column":9},"end":{"row":220,"column":9},"action":"remove","lines":["","        if ($docType == '08') {","            $lastSale = $company->last_invoice_pur_ref_number + 1;","        }"],"id":2},{"start":{"row":217,"column":9},"end":{"row":220,"column":9},"action":"insert","lines":["","        if ($docType == '08') {","            $lastSale = $company->last_invoice_pur_ref_number + 1;","        }"]}],[{"start":{"row":220,"column":9},"end":{"row":223,"column":9},"action":"insert","lines":["","        if ($docType == '08') {","            $lastSale = $company->last_invoice_pur_ref_number + 1;","        }"],"id":3}],[{"start":{"row":242,"column":9},"end":{"row":245,"column":9},"action":"remove","lines":["","        if ($docType == '08') {","            $ref = $company->last_invoice_pur_ref_number + 1;","        }"],"id":4},{"start":{"row":242,"column":9},"end":{"row":245,"column":9},"action":"insert","lines":["","        if ($docType == '08') {","            $ref = $company->last_invoice_pur_ref_number + 1;","        }"]}],[{"start":{"row":245,"column":9},"end":{"row":248,"column":9},"action":"insert","lines":["","        if ($docType == '08') {","            $ref = $company->last_invoice_pur_ref_number + 1;","        }"],"id":5}],[{"start":{"row":218,"column":26},"end":{"row":218,"column":27},"action":"remove","lines":["8"],"id":6},{"start":{"row":218,"column":26},"end":{"row":218,"column":27},"action":"insert","lines":["3"]}],[{"start":{"row":243,"column":26},"end":{"row":243,"column":27},"action":"remove","lines":["8"],"id":7},{"start":{"row":243,"column":26},"end":{"row":243,"column":27},"action":"insert","lines":["3"]}],[{"start":{"row":244,"column":29},"end":{"row":244,"column":56},"action":"remove","lines":["last_invoice_pur_ref_number"],"id":8},{"start":{"row":244,"column":29},"end":{"row":244,"column":49},"action":"insert","lines":["last_note_ref_number"]}],[{"start":{"row":219,"column":34},"end":{"row":219,"column":61},"action":"remove","lines":["last_invoice_pur_ref_number"],"id":9},{"start":{"row":219,"column":34},"end":{"row":219,"column":54},"action":"insert","lines":["last_note_ref_number"]}]]},"ace":{"folds":[],"scrolltop":1637,"scrollleft":0,"selection":{"start":{"row":242,"column":9},"end":{"row":242,"column":9},"isBackwards":false},"options":{"guessTabSize":true,"useWrapMode":false,"wrapToView":true},"firstLineState":{"row":20,"state":"php-start","mode":"ace/mode/php"}},"timestamp":1574964932096,"hash":"544a20cf37f12abd01fe35e1418836ebf75f5b15"}