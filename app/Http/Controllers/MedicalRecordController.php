<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MedicalRecordController extends Controller
{
    // Show all records (optional: add pagination/filtering later)
    public function index()
    {
        $records = MedicalRecord::all();
        return response()->json([
            'status' => 'success',
            'data' => $records
        ]);
    }

    // Get a specific record
    public function show(MedicalRecord $medicalRecord)
    {
        return response()->json([
            'status' => 'success',
            'data' => $medicalRecord
        ]);
    }

    // Get records for a specific user
    public function getByUser($userId)
    {
        $record = MedicalRecord::where('user_id', $userId)->first();
        
        if (!$record) {
            Log::info("No medical records found for user: $userId");
            return response()->json([
                'status' => 'error',
                'message' => 'No medical records found for this user'
            ], 404);
        }
        
        Log::info("Returning medical record for user: $userId", ['record' => $record->toArray()]);
        return response()->json([
            'status' => 'success',
            'data' => $record
        ]);
    }

    // Create a new record
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'chronic_conditions' => 'nullable|string',
            'previous_illnesses' => 'nullable|string',
            'surgeries_hospitalizations' => 'nullable|string',
            'allergies' => 'nullable|string',
            'immunization_history' => 'nullable|string',
            'childhood_illnesses' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $record = MedicalRecord::create($request->all());
            
            // Get a fresh instance to ensure all attributes are loaded
            $record = $record->fresh();
            
            return response()->json([
                'status' => 'success',
                'data' => $record
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create medical record', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create medical record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update a record
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validator = Validator::make($request->all(), [
            'chronic_conditions' => 'nullable|string',
            'previous_illnesses' => 'nullable|string',
            'surgeries_hospitalizations' => 'nullable|string',
            'allergies' => 'nullable|string',
            'immunization_history' => 'nullable|string',
            'childhood_illnesses' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Log the data before update
            Log::info('Medical record before update', ['record' => $medicalRecord->toArray()]);
            Log::info('Update request data', ['data' => $request->all()]);
            
            $medicalRecord->update($request->only([
                'chronic_conditions',
                'previous_illnesses',
                'surgeries_hospitalizations',
                'allergies',
                'immunization_history',
                'childhood_illnesses'
            ]));

            // Fetch the fresh model to ensure we return the updated data
            $medicalRecord = $medicalRecord->fresh();
            
            // Debug log to see what we're returning
            Log::info('Returning updated medical record', ['record' => $medicalRecord->toArray()]);

            return response()->json([
                'status' => 'success',
                'data' => $medicalRecord
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating medical record', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update medical record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a record
    public function destroy(MedicalRecord $medicalRecord)
    {
        try {
            $medicalRecord->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Medical record deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting medical record', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete medical record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}