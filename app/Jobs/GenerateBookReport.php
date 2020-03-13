<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
    private $items = null;
    private $user = null;
    private $company = null;
    private $year = null;
    private $month = null;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $items, $user, $company, $year, $month)
    {
        $this->type = $type;
        $this->items = $items;
        $this->user = $user;
        $this->year = $year;
        $this->month = $month;
        $this->company = $company;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $type = $this->type;
        $items = $this->items;
        $user = $this->user;
        $year = $this->year;
        $month = $this->month;
        $company = $this->company;
        
        if( $type == "BILL" ){
            foreach($items as $item){
                $item->calcularAcreditablePorLinea();
            }
            $filePath = "/libros/$company->id_number/libro-compras-".$year.$month.".xlsx";
            if( $company->id == 1110 ){
                $file = Excel::store(new LibroComprasExportSM($year, $month), $filePath, 's3');
            }else{
                $file = Excel::store(new LibroComprasExport($year, $month),   $filePath, 's3');
            }
            
            Mail::to( replaceAccents( $user->email) )->send(
                new BookReportEmail([
                    "title" => "Libro de Compras $month/$year - $company->id_number",
                    "message" => "Adjunto a este correo encontrará el libro de compras correspondiente al periodo $month/$year, con corte de fecha el día ".Carbon::now()->format('d/m/Y').".",
                    "filePath" => $filePath
                ])
            );
        }else{
            $filePath = "/libros/$company->id_number/libro-ventas-".$year.$month.".xlsx";
            if( $company->id == 1110 ){
                $file = Excel::store(new LibroVentasExportSM($year, $month), $filePath, 's3');
            }else{
                $file = Excel::store(new LibroVentasExport($year, $month),   $filePath, 's3');
            }
            
            Mail::to( replaceAccents( $user->email) )->send(
                new BookReportEmail([
                    "title" => "Libro de Ventas $month/$year - $company->id_number",
                    "message" => "Adjunto a este correo encontrará el libro de compras correspondiente al periodo $month/$year, con corte de fecha el día ".Carbon::now()->format('d/m/Y').".",
                    "filePath" => $filePath
                ])
            );
        }
        
    }
}
