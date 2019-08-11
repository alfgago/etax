<?php

namespace App\Exports;

use App\Bill;
use App\BillItem;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BillExport implements WithHeadings, WithMapping, FromQuery, WithEvents
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
                $cellRangeExplainer = 'A2:AB3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRangeExplainer)->getFont()->setSize(8);
                $event->sheet->getDelegate()->getStyle($cellRangeExplainer)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth('20');
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth('20');
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
                $cellRangeHeaders = 'A3:AB3'; // All headers
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
        
        $billItems = BillItem::query()
        ->with(['bill', 'bill.provider'])
        ->where('year', $this->year)
        ->where('month', $this->month)
        ->whereHas('bill', function ($query) use ($current_company){
            $query->where('company_id', $current_company);
        });
        
        return $billItems;
    }
    
    public function map($map): array
    {
        return [
            $map->bill->document_type,
            $map->bill->document_key,
            $map->bill->document_number,
            $map->bill->currency,
            $map->bill->currency_rate,
            $map->bill->generatedDate()->format('d/m/Y'),
            $map->bill->provider->code ?? '',
            $map->bill->providerName(),
            $map->bill->provider->tipo_persona ?? 'F',
            $map->bill->provider_id_number,
            $map->bill->provider_email ? $map->bill->provider_email : $map->bill->provider->email,
            $map->bill->sale_condition,
            $map->bill->payment_type,
            $map->item_number,
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
            $map->bill->total,
            $map->bill->commercial_activity,
            $map->bill->accept_status,
        ];
    }						

     public function headings(): array 
     {
        return [
            [ "Archivo exportado desde etaxcr.com" ],
            [ 
                'Elija el código equivalente al tipo de documento emitido.', 
                'Indique la clave de su comprobante (50 digitos)', 
                'Indique el número de consecutivo de su comprobante.', 
                'Elija el tipo de divisa. ', 
                'Indique el tipo de cambio considerado al momento de realizar la venta.', 
                'Indique la fecha de emisión de la factura en el formato indicado.',
                'Indique el código de providere utilizado en eTax.', 
                'Indique el nombre de la persona física o persona jurídica.', 
                'Elija el código equivalente al tipo de identificación del providere.', 
                'Indíque el número de identificación del providere.', 
                'Indique el correo electrónico de su providere', 
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
                'Indique 1 si la factura ya está aceptada, o 0 si desea aceptar desde eTax.',    
            ],
            [
                'TipoDocumento',
                'ClaveFactura',
                'ConsecutivoComprobante',
                'Moneda',
                'TipoCambio',
                'FechaEmision',
                'CodigoProveedor',
                'NombreProveedor',
                'TipoIdentificacion',
                'IdentificacionProveedor',
                'CorreoProveedor',
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
                'ActividadComercial',
                'Aceptada'
            ]
        ];
    }
    
    
}
