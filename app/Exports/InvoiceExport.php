<?php

namespace App\Exports;

use App\Invoice;
use App\InvoiceItem;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvoiceExport implements WithHeadings, WithMapping, FromQuery, WithEvents
{
    
    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRangeExplainer = 'A2:AA2'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRangeExplainer)->getFont()->setSize(8);
                $event->sheet->getDelegate()->getStyle($cellRangeExplainer)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth('25');
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth('25');
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth('30');
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('R')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('S')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('T')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('U')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('V')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('W')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('X')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('Y')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('Z')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('AA')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('AB')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('AC')->setWidth('20');
                $cellRangeHeaders = 'A3:AA3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FF4472C4');
            },
        ];
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $current_company = currentCompany();
        
        $invoiceItems = InvoiceItem::query()
        ->with(['invoice', 'invoice.client'])
        ->where('year', $this->year)
        ->whereHas('invoice', function ($query) use ($current_company){
            $query->where('company_id', $current_company);
        });
        
        if($this->month > 0){
            $invoiceItems->where('month', $this->month);
        }
        
        return $invoiceItems;
    }
    
    public function map($map): array
    {
        $current_company = currentCompanyModel();
        return [
            $current_company->id_number,
            $map->invoice->document_type,
            $map->invoice->document_key,
            $map->invoice->document_number,
            $map->invoice->currency,
            $map->invoice->currency_rate,
            $map->invoice->generatedDate()->format('d/m/Y'),
            $map->invoice->client->code ?? '',
            $map->invoice->clientName(),
            $map->invoice->client->tipo_persona ?? 'F',
            $map->invoice->client_id_number,
            $map->invoice->client_email ? $map->invoice->client_email : $map->invoice->client->email,
            $map->invoice->sale_condition,
            $map->invoice->payment_type,
            $map->item_number == 0 ? '0' : $map->item_number,
            $map->name,
            $map->code,
            $map->item_count,
            $map->measure_unit,
            $map->unit_price,
            $map->subtotal,
            $map->discount,
            $map->iva_type,
            $map->product_type,
            $map->iva_amount,
            $map->total,
            $map->invoice->total,
            $map->invoice->commercial_activity
        ];
    }						

     public function headings(): array 
     {
        return [
            [ "Archivo exportado desde etaxcr.com" ],
            [ 
                'Ingrese la cédula de su empresa o persona física en todas las líneas.',
                'Elija el código equivalente al tipo de documento emitido.', 
                'Indique la clave de su comprobante (50 digitos)', 
                'Indique el número de consecutivo de su comprobante.', 
                'Elija el tipo de divisa. ', 
                'Indique el tipo de cambio considerado al momento de realizar la venta.', 
                'Indique la fecha de emisión de la factura en el formato indicado.',
                'Indique el código de cliente utilizado en eTax.', 
                'Indique el nombre de la persona física o persona jurídica.', 
                'Elija el código equivalente al tipo de identificación del cliente.', 
                'Indíque el número de identificación del cliente.', 
                'Indique el correo electrónico de su cliente', 
                'Elija el código correspondiente a la condición de venta.', 
                'Elija el código correspondiente al método de pago.',    
                'Indíque el número de línea de la factura.',    
                'Indique el detalle de producto definido en la línea de la factura.',    
                'Indique el código de producto utilizado en eTax.',    
                'Indique la cantidad de ítems detallados en la línea de la factura.',    
                'Indique la unidad de medición detallada en la línea de la factura.',    
                'Indique el precio unitario detallado en la línea de la factura.',    
                'Subtotal de la línea.',    
                'Indique el monto de descuento detallado en la línea de la factura.',     
                'Indique el código de IVA de eTax correspondiente al tipo de venta.',   
                'Indique el código de categoría de Hacienda según el documento adjunto',       
                'Indique el monto de IVA correspondiente al subtotal de línea.',     
                'Total de línea. Este es un cálculo automático. Verifique el dato en su factura y cambiélo manualmente en caso de ser necesario.',     
                'Indique el monto total de la factura',    
                'Indique el código de la actividad comercial registrada ante Ministerio de Hacienda a la cual corresponde el registro.',    
            ],
            [
                'CedulaEmpresa',
                'TipoDocumento',
                'ClaveFactura',
                'ConsecutivoComprobante',
                'Moneda',
                'TipoCambio',
                'FechaEmision',
                'CodigoCliente',
                'NombreCliente',
                'TipoIdentificacion',
                'IdentificacionReceptor',
                'CorreoReceptor',
                'CondicionVenta',
                'MetodoPago',
                'NumeroLinea',
                'DetalleProducto',
                'CodigoProducto',
                'Cantidad',
                'UnidadMedicion',
                'PrecioUnitario',
                'SubtotalLinea',
                'MontoDescuento',
                'CodigoIVAEtax',
                'CategoriaHacienda',
                'MontoIVA',
                'TotalLinea',
                'TotalDocumento',
                'ActividadComercial'
            ]
        ];
    }
    
    
}
