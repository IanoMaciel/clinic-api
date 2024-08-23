<?php

namespace App\Http\Controllers;

use App\Models\Scheduling;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    protected $schedule;

    public function __construct(Scheduling $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index (Request $request) {
        $query = $this->schedule->query()->with('customer')->with('product');

        // get filter parameter
        $filter = $request->query('filter');

        // apply date filters
        if ($filter === 'today') {
            $query->whereDate('date_time', Carbon::today());
        }

        if ($filter === 'week') {
            $query->whereBetween('date_time', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }

        if ($filter === 'month') {
            $query->whereBetween('date_time', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ]);
        }

        if ($status = $request->input('status')) {
            $query->where('status', 'like', '%' . $status . '%');
        }

        // order by date_time ascending
        $query->orderBy('date_time', 'asc');

        $per_page = $request->get('per_page', 10);
        $schedule = $query->paginate($per_page);

        return response()->json($schedule, 200);
    }

    /**
     *
     * Regra: somente atendentes e adiministadores podem cadastrar novos agendamentos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        // 1. checks if the user has authorization
        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 3. valida a solicitação está usando as regras definidas no modelo
        $request->validate($this->schedule->rules());

        // 2. checks if date and time is valid
        $dateTime = Carbon::parse($request->get('date_time'));

        // verifica se a data e hora estão no passado
        if ($dateTime->isPast()) return response()->json(['error' => 'Connot schedule in the past'], 400);

        //4. verifica se horário já está ocupado
        $existingSchedule = $this->schedule->query()->where('date_time', $dateTime)->first();
        if ($existingSchedule) return response()->json(['error' => 'Time slot already token'], 400);

        // 5. create a new schedule
        $schedule = $this->schedule->create($request->all());
        return response()->json($schedule, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        // 1. Authentication and Authorization
        if ($this->isAuthorized()) return response()->json(['error' => 'Unauthorized'], 401);

        // 2. Find schedule
        $schedule = $this->schedule->query()->with('customer')->with('product')->find($id);

        // 3. return schedule
        return response()->json($schedule, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update (Request $request, $id) {
        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        # procura o id de agendamento e verifica se existe
        $schedule = $this->schedule->query()->find($id);
        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        # verifica os campos recebido pelo request body
        $request->validate($this->schedule->rules(true));

        if ($request->has('date_time')) {
            # verifica se a data é válida
            $dateTime = Carbon::parse($request->get('date_time'));
            if ($dateTime->isPast()) {
                return response()->json(['error' => 'Cannot schedule in the past'], 400);
            }

            # verifica se horário já está ocupado
            $existingSchedule = $this->schedule->query()->where('date_time', $dateTime)->first();
            if ($existingSchedule && $existingSchedule->id != $id) {
                return response()->json(['error' => 'Time slot already taken'], 400);
            }
        }
        $schedule->update($request->all());
        return response()->json($schedule, 201);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy ($id) {
        # autorização
        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        # procura o id de agendamento e verifica se existe
        $schedule = $this->schedule->query()->find($id);
        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        # remove o agendamento específico
        $schedule->delete();
        return response()->json(null, 204);
    }


    /**
     * @return bool
     */
    private function isAuthorized(): bool {
        $authUser = auth()->user();
        $isAdmin = $authUser->getAttribute('is_admin');
        $isCommon = $authUser->getAttribute('is_common');

        // if you not user admin and common, it should return 401 (Unauthorized)
        if (!$isAdmin && !$isCommon) return true;
        else return false;
    }
}
