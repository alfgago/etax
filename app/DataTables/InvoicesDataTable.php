<?php

namespace App\DataTables;

use App\Invoice;
use Yajra\DataTables\Services\DataTable;

class InvoicesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable()
    {
        
        return datatables()
            ->eloquent( $this->query() )
            ->orderColumn('reference_number', '-reference_number $1')
            ->addColumn('actions', function($invoice) {
                return view('datatables.actions', [
                    'routeName' => 'facturas-emitidas',
                    'deleteTitle' => 'Anular factura',
                    'editTitle' => 'Editar factura',
                    'id' => $invoice->id
                ])->render();
            }) 
            ->editColumn('client', function(Invoice $invoice) {
                return $invoice->client->fullname;
            })
            ->editColumn('generated_date', function(Invoice $invoice) {
                return $invoice->generatedDate()->format('d/m/Y');
            })
            ->editColumn('due_date', function(Invoice $invoice) {
                return $invoice->dueDate()->format('d/m/Y');
            })
            ->rawColumns(['actions']);
    }
    
    /**
     * Get the query object to be processed by dataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query()
    {
        $current_company = currentCompany();
        $query = Invoice::where('company_id', $current_company)->where('is_void', false)->where('is_totales', false)->with('client');

        return $this->applyScopes($query);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            [ 'data' => 'reference_number', 'name' => 'reference_number', 'title' => '#' ],
            [ 'data' => 'document_number', 'name' => 'document_number', 'title' => 'Comprobante' ],
            [ 'data' => 'client', 'name' => 'client.first_name', 'title' => 'Receptor' ],
            [ 'data' => 'currency', 'name' => 'currency', 'title' => 'Moneda' ],
            [ 'data' => 'subtotal', 'name' => 'subtotal', 'title' => 'Subtotal' ],
            [ 'data' => 'iva_amount', 'name' => 'iva_amount', 'title' => 'Monto IVA#' ],
            [ 'data' => 'total', 'name' => 'total', 'title' => 'Total' ],
            [ 'data' => 'generated_date', 'name' => 'generated_date', 'title' => '#' ],
            [ 'data' => 'actions', 'name' => 'actions', 'title' => 'Acciones', 'searchable' => false, 'orderable' => false ],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'FacturasEmitidas_' . date('YmdHis');
    }
}
