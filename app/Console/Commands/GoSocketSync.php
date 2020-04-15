<?php

namespace App\Console\Commands;

use App\Bill;
use App\IntegracionEmpresa;
use App\Invoice;
use App\Utils\BridgeGoSocketApi;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GoSocketSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gosocket:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command GoSocket Sync documents';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $apiGoSocket = new BridgeGoSocketApi();
        $users = IntegracionEmpresa::where('status', 1)->get();
        $this->info('Usuarios con token '. $users->count());

        foreach ($users as $user) {
            $token = $user->session_token;
            $tipos_facturas = $apiGoSocket->getDocumentTypes($token);

            foreach ($tipos_facturas as $tipo_factura) {

                $facturas = $apiGoSocket->getSentDocuments($token, $user->company_token, $tipo_factura);

                foreach ($facturas as $factura) {
                    $APIStatus = $apiGoSocket->getXML($token, $factura['DocumentId']);
                    $company = currentCompanyModel();
                    $xml  = base64_decode($APIStatus);
                    $xml = simplexml_load_string( $xml);
                    $json = json_encode( $xml );
                    $arr = json_decode( $json, TRUE );
                    try {
                        $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0 ;
                    } catch(\Exception $e) {
                        $identificacionReceptor = 0;
                    };

                    $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
                    $consecutivoComprobante = $arr['NumeroConsecutivo'];

                    //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                    if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionEmisor ) ) {
                        //Registra el XML. Si todo sale bien, lo guarda en S3.
                        Invoice::saveInvoiceXML( $arr, 'GS' );
                    }
                    $company->save();
                }

                $facturas = $apiGoSocket->getReceivedDocuments($token, $user->company_token, $tipo_factura);

                foreach ($facturas as $factura) {
                    $APIStatus = $apiGoSocket->getXML($token, $factura['DocumentId']);
                    $company = currentCompanyModel();
                    $xml  = base64_decode($APIStatus);
                    $xml = simplexml_load_string( $xml);
                    $json = json_encode( $xml );
                    $arr = json_decode( $json, TRUE );
                    $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0;
                    $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
                    $consecutivoComprobante = $arr['NumeroConsecutivo'];
                    $clave = $arr['Clave'];
                    //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                    if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionReceptor ) ) {
                        //Registra el XML. Si todo sale bien, lo guarda en S3
                        Bill::saveBillXML( $arr, 'XML' );
                    }
                    $company->save();
                }
            }

        }

    }
}
