<?php

namespace App\Http\Controllers;

use App\Models\Local;
use Illuminate\Http\Request;

class LocalController extends Controller {
    protected $local;

    public function __construct(Local $local) {
        $this->local = $local;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $locals = $this->local->all();
        return response()->json($locals, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $local = $this->local->create($request->all());
        return response()->json($local, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Local  $local
     * @return \Illuminate\Http\Response
     */
    public function show(Local $local)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Local  $local
     * @return \Illuminate\Http\Response
     */
    public function edit(Local $local)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Local  $local
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Local $local)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Local  $local
     * @return \Illuminate\Http\Response
     */
    public function destroy(Local $local)
    {
        //
    }
}
