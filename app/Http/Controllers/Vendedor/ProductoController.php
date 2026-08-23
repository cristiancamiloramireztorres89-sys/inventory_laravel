<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
    /**
     * Consultar catálogo y stock disponible para el Vendedor.
     */
    public function index(Request $request): View
    {
        $productos  = Producto::activos()->with('categoria')->orderBy('nombre', 'asc')->get();
        $categorias = Categoria::orderBy('nombre', 'asc')->get();

        return view('vendedor.productos', compact('productos', 'categorias'));
    }
}
