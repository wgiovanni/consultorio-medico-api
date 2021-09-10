<?php

namespace App\Http\Controllers;

use App\Models\Doctors;
use App\Models\Patients;
use App\Models\Quotes;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Extension\SmartPunct\Quote;
use Symfony\Component\HttpFoundation\Response;

class PatientsController extends Controller
{
    public function createQuotes(Request $request, $id)
    {
        try {
            $rules = [
                'date' => 'required|date',
                'doctor_id' => 'required|integer',
            ];
            $message = [
                'date.required' => 'El :attribute es obligatorio.',
                'date.date'     => 'El :attribute debe ser una fecha.',
                'doctor_id.required' => 'El :attribute es obligatorio.',
                'doctor_id.integer'     => 'El :attribute debe ser tipo entero.'
            ];
            $validator = Validator::make($request->all(), $rules, $message);

            if ($validator->fails()) {
                return response()->json(array('message' => $validator->errors(), Response::HTTP_NOT_FOUND));
            }
            //buscamos el paciente y el doctor
            $patient = Patients::where('id', $id)->first();
            $doctor = Doctors::where('id', $request->doctor_id)->first();
            if (empty($patient)) {
                return response()->json(array('message' => 'Paciente no encontrado', Response::HTTP_NOT_FOUND));
            }
            if (empty($doctor)) {
                return response()->json(array('message' => 'Doctor no encontrado', Response::HTTP_NOT_FOUND));
            }


            DB::beginTransaction();
            $quote = new Quotes();
            $quote->patient_id = $id;
            $quote->doctor_id = $request->doctor_id;
            $quote->attention_at = $request->date;
            $quote->status = false;

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
}
