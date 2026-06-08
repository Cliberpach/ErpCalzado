<?php

namespace App\Http\Controllers\Mantenimiento\CopiaSeguridad;

use App\Http\Controllers\Controller;
use App\Http\Services\Mantenimiento\CopiaSeguridad\CopiasSeguridadService;
use App\Jobs\Mantenimiento\CopiaSeguridad\GenerarBackupJob;
use App\Mantenimiento\CopiaSeguridad\CopiaSeguridad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CopiasSeguridadController extends Controller
{
    private CopiasSeguridadService $service;

    public function __construct()
    {
        $this->service = new CopiasSeguridadService();
    }

    public function index()
    {
        return view('mantenimiento.copias_seguridad.index');
    }

    public function getBackups(Request $request)
    {
        $query = CopiaSeguridad::with('user')
            ->select('copias_seguridad.*')
            ->latest();

        return DataTables::of($query)
            ->addColumn('usuario', fn($row) => $row->user->name ?? '-')
            ->addColumn('tamano', function ($row) {
                if (!$row->tamano_bytes) return '-';
                if ($row->tamano_bytes >= 1073741824) return number_format($row->tamano_bytes / 1073741824, 2) . ' GB';
                if ($row->tamano_bytes >= 1048576)    return number_format($row->tamano_bytes / 1048576, 2) . ' MB';
                if ($row->tamano_bytes >= 1024)       return number_format($row->tamano_bytes / 1024, 2) . ' KB';
                return $row->tamano_bytes . ' B';
            })
            ->addColumn('fecha', fn($row) => $row->created_at->format('d/m/Y H:i:s'))
            ->addColumn('estado_badge', function ($row) {

                switch ($row->estado) {
                    case 'COMPLETADO':
                        return '<span class="badge badge-success">COMPLETADO</span>';

                    case 'GENERANDO':
                        return '<span class="badge badge-warning">GENERANDO</span>';

                    case 'FALLIDO':
                        return '<span class="badge badge-danger" title="' . e($row->error) . '">FALLIDO</span>';

                    default:
                        return $row->estado;
                }
            })
            ->rawColumns(['estado_badge'])
            ->make(true);
    }

    public function generate(Request $request)
    {
        try {
            $registro = $this->service->crearRegistro(Auth::id());
            GenerarBackupJob::dispatch($registro->id);
            return response()->json(['success' => true, 'message' => 'Backup en cola. Actualizando estado...']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function download(int $id)
    {
        try {
            $path     = $this->service->rutaBackup($id);
            $filename = basename($path);

            while (ob_get_level()) {
                ob_end_clean();
            }

            return response()->download($path, $filename, [
                'Content-Type'   => 'application/octet-stream',
                'Content-Length' => filesize($path),
                'Cache-Control'  => 'no-cache, must-revalidate',
                'Pragma'         => 'public',
            ]);
        } catch (Throwable $th) {
            abort(404, $th->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->eliminarBackup($id);
            return response()->json(['success' => true, 'message' => 'COPIA ELIMINADA']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
