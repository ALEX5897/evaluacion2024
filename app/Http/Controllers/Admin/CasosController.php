<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCasosRequest;
use App\Http\Requests\StoreCasosRequest;
use App\Http\Requests\UpdateCasosRequest;
use App\Models\Flight;
use App\Models\Casos;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CasosrController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('casos_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $casos = Casos::with(['abogados'])->get();

        return view('admin.casos.index', compact('casos'));
    }

    public function create()
    {
        abort_if(Gate::denies('casos_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $abogaods = Flight::pluck('name', 'id');

        return view('admin.casos.create', compact('abogados'));
    }

    public function store(StoreCasosRequest $request)
    {
        $casos = Casos::create($request->all());
        $casos->abogados()->sync($request->input('abogados', []));

        return redirect()->route('admin.casos.index');
    }

    public function edit(Casos $casos)
    {
        abort_if(Gate::denies('casos_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $abogados = Flight::pluck('name', 'id');

        $casos->load('abogados');

        return view('admin.casos.edit', compact('casos', 'abogados'));
    }

    public function update(UpdateCasosRequest $request, Casos $casos)
    {
        $casos->update($request->all());
        $casos->abogados()->sync($request->input('abogados', []));

        return redirect()->route('admin.casos.index');
    }

    public function show(Casos $casos)
    {
        abort_if(Gate::denies('casos_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $casos->load('abogados');

        return view('admin.casos.show', compact('casos'));
    }

    public function destroy(Casos $casos)
    {
        abort_if(Gate::denies('casos_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $casos->delete();

        return back();
    }

    public function massDestroy(MassDestroyCasosRequest $request)
    {
        $casos = Casos::find(request('ids'));

        foreach ($casos as $casos) {
            $casos->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
