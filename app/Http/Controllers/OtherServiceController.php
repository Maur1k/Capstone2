<?php

namespace App\Http\Controllers;

use App\Models\OtherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OtherServiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'service_type' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'purpose' => 'nullable|string|max:500',
            'additional_info' => 'nullable|array',
        ]);

        $tracking = OtherService::generateTrackingNumber();
        $queueNumber = OtherService::getNextQueueNumber();

        $record = OtherService::create([
            'tracking_number' => $tracking,
            'queue_number' => $queueNumber,
            'service_type' => $validated['service_type'],
            'status' => 'submitted',
            'full_name' => $validated['full_name'],
            'contact_number' => $validated['contact_number'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'purpose' => $validated['purpose'] ?? null,
            'additional_info' => $validated['additional_info'] ?? null,
            'print_status' => 'pending',
        ]);

        $printData = [
            'name' => $validated['full_name'],
            'service' => $validated['service_type'],
            'tracking' => $tracking,
            'queue_no' => $queueNumber,
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i:s'),
        ];

        try {
            $printRes = Http::timeout(10)->post('http://localhost:4000/print', $printData);
            if ($printRes->successful()) {
                $record->update(['print_status' => 'printed']);
                $printMessage = 'Printed successfully';
            } else {
                $record->update(['print_status' => 'failed']);
                $printMessage = 'Print service returned error';
            }
        } catch (\Throwable $e) {
            $record->update(['print_status' => 'failed']);
            $printMessage = 'Print service unavailable';
        }

        return response()->json([
            'success' => true,
            'message' => 'Request submitted successfully!',
            'tracking_number' => $tracking,
            'queue_number' => $queueNumber,
            'print_status' => $printMessage,
            'data' => $printData,
        ]);
    }
}


