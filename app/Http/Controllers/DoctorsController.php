<?php

namespace App\Http\Controllers;

use App\Models\Doctors;
use App\Models\Quotes;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class DoctorsController extends Controller
{
    public function changeStatus(Request $request, $id)
    {
        try {
            $rules = [
                'status' => 'required|boolean',
            ];
            $message = [
                'status.required' => 'El :attribute es obligatorio.',
                'status.boolean'     => 'El :attribute debe ser un boolean.'
            ];
            $validator = Validator::make($request->all(), $rules, $message);

            if ($validator->fails()) {
                return response()->json(array('message' => $validator->errors(), Response::HTTP_NOT_FOUND));
            }
            //buscamos la cita
            $quote = Quotes::where('id', $id)->first();
            if (empty($quote)) {
                return response()->json(array('message' => 'Cita no encontrada', Response::HTTP_NOT_FOUND));
            }

            DB::beginTransaction();
            $quote->status = $request->status;

            if ($quote->save()) {
                DB::commit();
            } else {
                DB::rollback();
            }
        } catch (ValidationException $e) {
            return response()->json(array('message' => $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY));
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage(), Response::HTTP_NOT_FOUND));
        }

        return response()->json(array(
            'quote' => $quote
        ), Response::HTTP_OK);
    }

    public function getAllByDate(Request $request, $id)
    {
        try {

            $rules = [
                'date' => 'required|date',
            ];
            $message = [
                'date.required' => 'El :attribute es obligatorio.',
                'date.date'     => 'El :attribute debe ser una fecha.'
            ];
            $validator = Validator::make($request->all(), $rules, $message);

            if ($validator->fails()) {
                return response()->json(array('message' => $validator->errors(), Response::HTTP_NOT_FOUND));
            }

            $doctor = Doctors::where('id', $id)->first();
            if (empty($doctor)) {
                return response()->json(array('message' => 'Doctor no encontrado', Response::HTTP_NOT_FOUND));
            }
            //buscamos las citas
            $quotes = Quotes::where('doctor_id', $id)->where('attention_at', $request->date)->get();

        } catch (ValidationException $e) {
            return response()->json(array('message' => $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY));
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage(), Response::HTTP_NOT_FOUND));
        }

        return response()->json(array(
            'quotes' => $quotes
        ), Response::HTTP_OK);
    }
}
