{"filter":false,"title":"InvoiceController.php","tooltip":"/app/Http/Controllers/InvoiceController.php","undoManager":{"mark":13,"position":13,"stack":[[{"start":{"row":2180,"column":12},"end":{"row":2181,"column":0},"action":"insert","lines":["",""],"id":9},{"start":{"row":2181,"column":0},"end":{"row":2181,"column":12},"action":"insert","lines":["            "]}],[{"start":{"row":2181,"column":12},"end":{"row":2188,"column":57},"action":"insert","lines":["$xlsInvoices = XlsInvoice::select('consecutivo', 'company_id','autorizado')","                ->where('company_id',$company->id)->where('autorizado',1)->distinct('consecutivo')->get();*/","","            $apiHacienda = new BridgeHaciendaApi();","            $tokenApi = $apiHacienda->login(false);","            if ($tokenApi !== false) {","","                //foreach ($xlsInvoices as $xlsInvoice) {"],"id":10}],[{"start":{"row":2182,"column":50},"end":{"row":2183,"column":0},"action":"insert","lines":["",""],"id":11},{"start":{"row":2183,"column":0},"end":{"row":2183,"column":16},"action":"insert","lines":["                "]}],[{"start":{"row":2183,"column":39},"end":{"row":2184,"column":0},"action":"insert","lines":["",""],"id":12},{"start":{"row":2184,"column":0},"end":{"row":2184,"column":16},"action":"insert","lines":["                "]}],[{"start":{"row":2184,"column":41},"end":{"row":2185,"column":0},"action":"insert","lines":["",""],"id":13},{"start":{"row":2185,"column":0},"end":{"row":2185,"column":16},"action":"insert","lines":["                "]}],[{"start":{"row":2185,"column":25},"end":{"row":2185,"column":26},"action":"remove","lines":["/"],"id":14},{"start":{"row":2185,"column":24},"end":{"row":2185,"column":25},"action":"remove","lines":["*"]}],[{"start":{"row":2185,"column":24},"end":{"row":2190,"column":0},"action":"remove","lines":["","","            $apiHacienda = new BridgeHaciendaApi();","            $tokenApi = $apiHacienda->login(false);","            if ($tokenApi !== false) {",""],"id":15}],[{"start":{"row":2186,"column":12},"end":{"row":2186,"column":18},"action":"remove","lines":["    //"],"id":16}],[{"start":{"row":2187,"column":0},"end":{"row":2187,"column":4},"action":"insert","lines":["    "],"id":17},{"start":{"row":2188,"column":0},"end":{"row":2188,"column":4},"action":"insert","lines":["    "]}],[{"start":{"row":2188,"column":62},"end":{"row":2189,"column":0},"action":"insert","lines":["",""],"id":18},{"start":{"row":2189,"column":0},"end":{"row":2189,"column":16},"action":"insert","lines":["                "]},{"start":{"row":2189,"column":16},"end":{"row":2189,"column":17},"action":"insert","lines":["}"]},{"start":{"row":2189,"column":0},"end":{"row":2189,"column":16},"action":"remove","lines":["                "]},{"start":{"row":2189,"column":0},"end":{"row":2189,"column":12},"action":"insert","lines":["            "]}],[{"start":{"row":2187,"column":55},"end":{"row":2187,"column":56},"action":"insert","lines":[","],"id":19}],[{"start":{"row":2187,"column":56},"end":{"row":2187,"column":57},"action":"insert","lines":[" "],"id":20}],[{"start":{"row":2187,"column":57},"end":{"row":2187,"column":68},"action":"insert","lines":["$xlsInvoice"],"id":21}],[{"start":{"row":2187,"column":47},"end":{"row":2187,"column":57},"action":"remove","lines":["$company, "],"id":22}]]},"ace":{"folds":[],"scrolltop":30213,"scrollleft":0,"selection":{"start":{"row":2185,"column":24},"end":{"row":2185,"column":24},"isBackwards":false},"options":{"guessTabSize":true,"useWrapMode":false,"wrapToView":true},"firstLineState":{"row":2157,"state":"php-start","mode":"ace/mode/php"}},"timestamp":1582827609319,"hash":"7927ee282c62524ae850aba2620c7c6a11e8be3c"}