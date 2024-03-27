<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAbogadoRequest;
use App\Http\Requests\StoreAbogadoRequest;
use App\Http\Requests\UpdateAbogadoRequest;
use App\Models\Abogado;
use App\Models\Casos;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AbogadoController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('abogado_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $abogados = Abogado::with(['casoss'])->get();

        return view('admin.abogados.index', compact('abogados'));
    }

    public function create()
    {
        abort_if(Gate::denies('abogado_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $casoss = Casos::pluck('nombre', 'id');

        return view('admin.abogados.create', compact('casoss'));
    }

    public function store(StoreAbogadoRequest $request)
    {
        $abogado = Abogado::create($request->all());
        $abogado->casoss()->sync($request->input('casoss', []));

        return redirect()->route('admin.abogados.index');
    }

    public function edit(Abogado $abogado)
    {
        abort_if(Gate::denies('abogado_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $casoss = Casos::pluck('nombre', 'id');

        $abogado->load('casoss');

        return view('admin.abogado.edit', compact('abogado', 'casoss'));
    }

    public function update(UpdateAbogadoRequest $request, Abogado $abogado)
    {
        $abogado->update($request->all());
        $abogado->casoss()->sync($request->input('casoss', []));

        return redirect()->route('admin.abogado.index');
    }

    public function show(Abogado $abogado)
    {
        abort_if(Gate::denies('abogado_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $abogado->load('casoss');

        return view('admin.abogado.show', compact('abogado'));
    }

    public function destroy(Abogado $abogado)
    {
        abort_if(Gate::denies('abogado_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $abogado->delete();

        return back();
    }

    public function massDestroy(MassDestroyAbogadoRequest $request)
    {
        $abogado = Abogado::find(request('ids'));

        foreach ($abogado as $abogado) {
            $abogado->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
