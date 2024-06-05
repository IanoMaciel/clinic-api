<?php

namespace App\Http\Controllers;

use App\Models\Scheduling;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
    public function index () {
        $schedule = $this->schedule->query()->with('customer')->paginate(10);
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
        $authUser = auth()->user();
        $isAdmin = $authUser->getAttribute('is_admin');
        $isCommon = $authUser->getAttribute('is_common');

        // if you not user admin and common, it should return 401 (Unauthorized)
        if (!$isAdmin && !$isCommon) return response()->json(['error' => 'Unauthorized'], 401);

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }
}
