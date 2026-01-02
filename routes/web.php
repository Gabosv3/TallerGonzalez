<?php


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/administrativo/login');
});

// Email Verification Routes
Route::get('email/verify', [\App\Http\Controllers\EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::get('email/verify/{id}', [\App\Http\Controllers\EmailVerificationController::class, 'verifySimple'])->name('verification.verify.simple');
Route::get('email/verify/{id}/{hash}', [\App\Http\Controllers\EmailVerificationController::class, 'verifyLegacy'])->name('verification.verify.legacy');
 
// Product reports
Route::get('administrativo/productos/{producto}/reporte', [\App\Http\Controllers\ProductoReportController::class, 'reporte'])->name('productos.reporte');
Route::get('administrativo/productos/export-csv', [\App\Http\Controllers\ProductoReportController::class, 'exportCsv'])->name('productos.export_csv');
Route::get('administrativo/productos/reporte-general', [\App\Http\Controllers\ProductoReportController::class, 'reporteGeneral'])->name('productos.reporte_general');
Route::get('administrativo/productos/reporte-reorden', [\App\Http\Controllers\ProductoReportController::class, 'reorderReport'])->name('productos.reporte_reorden');
// Pedidos reports
Route::get('administrativo/pedidos/{pedido}/reporte-pdf', [\App\Http\Controllers\PedidoReportController::class, 'reporte'])->name('pedidos.reporte.pdf');
Route::get('administrativo/pedidos/reporte-multiple', [\App\Http\Controllers\PedidoReportController::class, 'multiple'])->name('pedidos.reporte.multiple');
Route::get('administrativo/pedidos/reporte-compras-por-producto', [\App\Http\Controllers\PedidoReportController::class, 'comprasPorProducto'])->name('pedidos.reporte.compras_por_producto');
// Facturas reports
Route::get('administrativo/facturas/{factura}/reporte-pdf', [\App\Http\Controllers\FacturaReportController::class, 'show'])->name('facturas.reporte.pdf');
Route::get('administrativo/facturas/reporte-periodo', [\App\Http\Controllers\FacturaReportController::class, 'periodo'])->name('facturas.reporte.periodo');

// Report Exports
Route::get('administrativo/reports/facturas/pdf', [\App\Http\Controllers\ReportExportController::class, 'exportFacturasPDF'])->name('reports.facturas.pdf');
Route::get('administrativo/reports/facturas/excel', [\App\Http\Controllers\ReportExportController::class, 'exportFacturasExcel'])->name('reports.facturas.excel');
Route::get('administrativo/reports/productos/pdf', [\App\Http\Controllers\ReportExportController::class, 'exportProductosPDF'])->name('reports.productos.pdf');
Route::get('administrativo/reports/productos/excel', [\App\Http\Controllers\ReportExportController::class, 'exportProductosExcel'])->name('reports.productos.excel');
Route::get('administrativo/reports/clientes/pdf', [\App\Http\Controllers\ReportExportController::class, 'exportClientesPDF'])->name('reports.clientes.pdf');
Route::get('administrativo/reports/clientes/excel', [\App\Http\Controllers\ReportExportController::class, 'exportClientesExcel'])->name('reports.clientes.excel');

// Serve API docs UI
use App\Http\Controllers\ApiDocsController;
Route::get('/api/docs', [ApiDocsController::class, 'ui']);

Route::get('/test-mail', [\App\Http\Controllers\TestMailController::class, 'send']);

Route::get('/password/reset/{token}', function ($token) {
    return redirect(route('filament.administrativo.auth.password-reset.reset', ['token' => $token, 'email' => request()->email]));
})->name('password.reset');