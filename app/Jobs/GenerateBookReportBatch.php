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

class GenerateBookReportBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $type = null;
    private $user = null;
    private $company = null;
    private $year = null;
    private $month = null;
    private $from = null;
    private $to = null;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $user, $company, $year, $month, $from, $to)
    {
        $this->type = $type;
        $this->user = $user;
        $this->year = $year;
        $this->month = $month;
        $this->company = $company;
        $this->from = $from;
        $this->to = $to;
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
        $from = $this->from;
        $to = $this->to;
        
        if( $type == "BILL" ){
            
        }else{
                
            $filePath = "/libros/$company->id_number/libro-ventas-".$year.$month."-$from-a-$to.xlsx";
            $file = Excel::store(new LibroVentasExport($year, $month, $company->id, $from, $to),   $filePath, 's3');
            
            Mail::to( replaceAccents( $user->email) )->send(
                new BookReportEmail([
                    "title" => "Libro de Ventas $month/$year - $company->id_number ($from a $to)",
                    "message" => "Adjunto a este correo encontrará el libro de ventas correspondiente al periodo $month/$year, desde la linea $from a la linea $to. Con corte de fecha el día ".Carbon::now()->format('d/m/Y').".",
                    "filePath" => $filePath
                ])
            );
        }
        
    }
}
