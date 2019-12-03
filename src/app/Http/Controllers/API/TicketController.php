<?php

namespace App\Http\Controllers\API;

use App\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TicketResource as TicketResource;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = TicketResource::collection(Ticket::all());
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|max:255',
            'description' => 'required|string',
            'employee_id' => 'required|numeric|exists:employees,id',
            'status_id' => 'required|numeric|exists:ticket_status,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $ticket = new Ticket([
            'subject' => $request['subject'],
            'description' => $request['description'],
            'employee_id' => $request['employee_id'],
            'status_id' => $request['status_id']
        ]);

        try {
            $ticket->save();
            return response()->json($ticket, 201);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        try {
            $resource = new TicketResource($ticket);
            return response()->json($resource, isset($ticket) ? 200 : 404);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|max:255',
            'description' => 'required',
            'employee_id' => 'required|numeric',
            'status_id' => 'required|numeric',
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 400);

        $ticket->subject = $request['subject'];
        $ticket->description = $request['description'];
        $ticket->employee_id = $request['employee_id'];;
        $ticket->status_id = $request['status_id'];

        try {
            $ticket->save();
            return response()->json($ticket, 200);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        try {
            $ticket->delete();
            return response()->json(['message' => 'Deleted'], 200);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()], 500);
        }
    }
}
