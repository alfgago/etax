{"filter":false,"title":"BillItem.php","tooltip":"/app/BillItem.php","undoManager":{"mark":21,"position":21,"stack":[[{"start":{"row":108,"column":9},"end":{"row":108,"column":10},"action":"insert","lines":["*"],"id":2},{"start":{"row":108,"column":10},"end":{"row":108,"column":11},"action":"insert","lines":["/"]}],[{"start":{"row":106,"column":8},"end":{"row":106,"column":9},"action":"insert","lines":["/"],"id":3},{"start":{"row":106,"column":9},"end":{"row":106,"column":10},"action":"insert","lines":["*"]}],[{"start":{"row":64,"column":7},"end":{"row":65,"column":0},"action":"insert","lines":["",""],"id":5},{"start":{"row":65,"column":0},"end":{"row":65,"column":6},"action":"insert","lines":["      "]},{"start":{"row":65,"column":6},"end":{"row":66,"column":0},"action":"insert","lines":["",""]},{"start":{"row":66,"column":0},"end":{"row":66,"column":6},"action":"insert","lines":["      "]}],[{"start":{"row":66,"column":6},"end":{"row":71,"column":9},"action":"insert","lines":["if( (strpos( strtolower($this->name),\"diesel\") !== false) || ","            (strpos( strtolower($this->name),\"gasolina\") !== false) ||","            (strpos( strtolower($this->name),\"vehiculo\") !== false)","        ){","          $this->product_type = 59;","        }"],"id":6}],[{"start":{"row":69,"column":0},"end":{"row":69,"column":2},"action":"remove","lines":["  "],"id":7},{"start":{"row":70,"column":0},"end":{"row":70,"column":2},"action":"remove","lines":["  "]},{"start":{"row":71,"column":0},"end":{"row":71,"column":2},"action":"remove","lines":["  "]}],[{"start":{"row":66,"column":64},"end":{"row":68,"column":67},"action":"remove","lines":["|| ","            (strpos( strtolower($this->name),\"gasolina\") !== false) ||","            (strpos( strtolower($this->name),\"vehiculo\") !== false)"],"id":8}],[{"start":{"row":66,"column":64},"end":{"row":67,"column":6},"action":"remove","lines":["","      "],"id":9}],[{"start":{"row":66,"column":44},"end":{"row":66,"column":50},"action":"remove","lines":["diesel"],"id":10},{"start":{"row":66,"column":44},"end":{"row":66,"column":45},"action":"insert","lines":["a"]},{"start":{"row":66,"column":45},"end":{"row":66,"column":46},"action":"insert","lines":["g"]},{"start":{"row":66,"column":46},"end":{"row":66,"column":47},"action":"insert","lines":["u"]},{"start":{"row":66,"column":47},"end":{"row":66,"column":48},"action":"insert","lines":["a"]}],[{"start":{"row":67,"column":8},"end":{"row":67,"column":33},"action":"remove","lines":["$this->product_type = 59;"],"id":11},{"start":{"row":67,"column":8},"end":{"row":67,"column":26},"action":"insert","lines":["$firstDigit = 'S';"]}],[{"start":{"row":66,"column":64},"end":{"row":67,"column":0},"action":"insert","lines":["",""],"id":14},{"start":{"row":67,"column":0},"end":{"row":67,"column":8},"action":"insert","lines":["        "]}],[{"start":{"row":67,"column":8},"end":{"row":67,"column":27},"action":"insert","lines":["$this->measure_unit"],"id":15}],[{"start":{"row":67,"column":27},"end":{"row":67,"column":28},"action":"insert","lines":[" "],"id":16},{"start":{"row":67,"column":28},"end":{"row":67,"column":29},"action":"insert","lines":["¿"]}],[{"start":{"row":67,"column":28},"end":{"row":67,"column":29},"action":"remove","lines":["¿"],"id":17}],[{"start":{"row":67,"column":28},"end":{"row":67,"column":29},"action":"insert","lines":["="],"id":18}],[{"start":{"row":67,"column":29},"end":{"row":67,"column":30},"action":"insert","lines":[" "],"id":19}],[{"start":{"row":67,"column":30},"end":{"row":67,"column":32},"action":"insert","lines":["''"],"id":20}],[{"start":{"row":67,"column":31},"end":{"row":67,"column":32},"action":"insert","lines":["O"],"id":21},{"start":{"row":67,"column":32},"end":{"row":67,"column":33},"action":"insert","lines":["s"]}],[{"start":{"row":67,"column":34},"end":{"row":67,"column":35},"action":"insert","lines":[";"],"id":22}],[{"start":{"row":94,"column":7},"end":{"row":98,"column":7},"action":"remove","lines":["","      if( $this->exoneration_amount > 0){","        $this->iva_type = $firstDigit.\"080\";","        $this->product_type = 62;","      }"],"id":23}],[{"start":{"row":110,"column":7},"end":{"row":114,"column":7},"action":"insert","lines":["","      if( $this->exoneration_amount > 0){","        $this->iva_type = $firstDigit.\"080\";","        $this->product_type = 62;","      }"],"id":24}],[{"start":{"row":110,"column":7},"end":{"row":111,"column":0},"action":"insert","lines":["",""],"id":25},{"start":{"row":111,"column":0},"end":{"row":111,"column":6},"action":"insert","lines":["      "]}],[{"start":{"row":106,"column":9},"end":{"row":109,"column":11},"action":"remove","lines":["","        /*if( $actividad == '751302' || $actividad == '654901' ){","          $this->product_type = 50;","        }*/"],"id":27}]]},"ace":{"folds":[],"scrolltop":1139.5,"scrollleft":0,"selection":{"start":{"row":100,"column":54},"end":{"row":100,"column":54},"isBackwards":false},"options":{"guessTabSize":true,"useWrapMode":false,"wrapToView":true},"firstLineState":{"row":211,"state":"php-start","mode":"ace/mode/php"}},"timestamp":1589992140608,"hash":"d951108a0f8500ff185672a6cfeca538ab4ea134"}