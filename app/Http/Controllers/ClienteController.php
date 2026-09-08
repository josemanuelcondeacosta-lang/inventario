<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::withCount(['ventas' => fn($q) => $q->where('anulada', false)])
            ->withSum(['ventas' => fn($q) => $q->where('anulada', false)], 'total')
            ->withSum(['ventas' => fn($q) => $q->where('anulada', false)], 'saldo')
            ->when($request->buscar, function ($q) use ($request) {
                $q->where('cedula', 'like', '%' . $request->buscar . '%')
                  ->orWhere('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('telefono', 'like', '%' . $request->buscar . '%');
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['ventas' => function ($q) {
            $q->with('detalles.producto')->orderByDesc('fecha');
        }]);

        $totalComprado = $cliente->ventas->where('anulada', false)->sum('total');
        $saldoTotal    = $cliente->ventas->where('anulada', false)->sum('saldo');

        return view('clientes.show', compact('cliente', 'totalComprado', 'saldoTotal'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $datos = $request->validate([
            'cedula'   => 'required|string|max:20|unique:clientes,cedula,' . $cliente->id,
            'nombre'   => 'required|string|max:150',
            'telefono' => 'nullable|string|max:20',
        ]);

        $cliente->update($datos);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Datos del cliente actualizados.');
    }
}