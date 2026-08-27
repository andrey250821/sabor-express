<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\ComprobantePago;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Contadores
        $pedidos = Pedido::count();

        $pedidosEntregados = Pedido::where('estado', 'entregado')->count();

        $clientes = User::where('role_id', 2)->count();

        $deliverys = User::where('role_id', 3)->count();

        $productos = Producto::count();

        // Ventas del mes
        $ventasMes = Pedido::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total');

        // Últimos pedidos
        $ultimosPedidos = Pedido::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Ventas últimos 7 días
        $ventasSemana = Pedido::selectRaw('DATE(created_at) as fecha, SUM(total) as total')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Comprobantes pendientes
        $comprobantesPendientes = ComprobantePago::where('estado', 'pendiente')->count();

        return view('admin.dashboard.index', compact(
            'pedidos',
            'pedidosEntregados',
            'clientes',
            'deliverys',
            'productos',
            'ventasMes',
            'ultimosPedidos',
            'ventasSemana',
            'comprobantesPendientes'
        ));
    }
}