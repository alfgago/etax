<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\InvoiceItem;
use App\BillItem;
use App\Exports\LibroComprasExport;
use App\Exports\LibroComprasExportSM;
use App\Exports\LibroVentasExport;
use App\Exports\LibroVentasExportSM;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\BookReportEmail;
use \Carbon\Carbon;

class GenerateBookReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $type = null;
    private $user = null;
    private $company = null;
    private $year = null;
    private $month = null;
    private $limit = null;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $user, $company, $year, $month, $limit = 27000)
    {
        $this->type = $type;
        $this->user = $user;
        $this->year = $year;
        $this->month = $month;
        $this->company = $company;
        $this->limit = $limit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $type = $this->type;
        $user = $this->user;
        $year = $this->year;
        $month = $this->month;
        $company = $this->company;
        $limit = $this->limit;
        
        if( $type == "BILL" ){
            //Busca todos los que aun no tienen el IVA calculado, lo calcula y lo guarda
            $items = BillItem::query()
            ->with(['bill', 'bill.provider', 'productCategory', 'ivaType'])
            ->where('year', $year)
            ->where('month', $month)
            ->where('iva_amount', '>', 0)
            ->where('iva_acreditable', 0)
            ->where('iva_gasto', 0)
            ->whereHas('bill', function ($query) use ($company){
                $query->where('company_id', $company->id)
                ->where('is_void', false)
                ->where('is_authorized', true)
                ->where('is_code_validated', true)
                ->where('accept_status', 1)
                ->where('hide_from_taxes', false);
            })->get();
            
            foreach($items as $item){
                $item->calcularAcreditablePorLinea();
            }
            $filePath = "/libros/$company->id_number/libro-compras-".$year.$month.".xlsx";
            if( $company->id == 1110 ){
                $file = Excel::store(new LibroComprasExportSM($year, $month, $company->id), $filePath, 's3');
            }else{
                $file = Excel::store(new LibroComprasExport($year, $month, $company->id),   $filePath, 's3');
            }
            
            Mail::to( replaceAccents( $user->email) )->send(
                new BookReportEmail([
                    "title" => "Libro de Compras $month/$year - $company->id_number",
                    "message" => "Adjunto a este correo encontrará el libro de compras correspondiente al periodo $month/$year, con corte de fecha el día ".Carbon::now()->format('d/m/Y').".",
                    "filePath" => $filePath
                ])
            );
        }else{
            Log::debug("Tiene un limite de $limit " . json_encode($limit < 27000));
            //if($limit <= 27001){
                $filePath = "/libros/$company->id_number/libro-ventas-".$year.$month.".xlsx";
                if( $company->id == 1110 ){
                    $file = Excel::store(new LibroVentasExportSM($year, $month, $company->id), $filePath, 's3');
                }else{
                    $file = Excel::store(new LibroVentasExport($year, $month, $company->id),   $filePath, 's3');
                }
                
                Mail::to( replaceAccents( $user->email) )->send(
                    new BookReportEmail([
                        "title" => "Libro de Ventas $month/$year - $company->id_number",
                        "message" => "Adjunto a este correo encontrará el libro de ventas correspondiente al periodo $month/$year, con corte de fecha el día ".Carbon::now()->format('d/m/Y').".",
                        "filePath" => $filePath
                    ])
                );
            /*}else{
                $from = 0;
                $to = 0;
                while($from < $limit){
                    $to = $from + 27000;
                    if($to >= $limit){
                        $to = $limit;
                    }
                    GenerateBookReportBatch::dispatch('INVOICE', $user, $company, $year, $month, $to, $from)->onQueue('default_long');
                    $from = $to;
                }
            }*/
            
        }
        
    }
}
