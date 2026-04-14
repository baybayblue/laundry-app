<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        
        $query = LeaveRequest::with('user')->latest();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $requests = $query->paginate(15);

        return view('admin.leave_requests.index', compact('requests', 'status'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Status pengajuan sudah diproses.');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Generate attendance records for the leave period
        $period = CarbonPeriod::create($leaveRequest->start_date, $leaveRequest->end_date);
        
        foreach ($period as $date) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $leaveRequest->user_id,
                    'date' => $date->format('Y-m-d'),
                ],
                [
                    'status' => 'leave',
                    'note' => 'Izin/Cuti: ' . $leaveRequest->reason,
                ]
            );
        }

        return back()->with('success', 'Pengajuan cuti disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Status pengajuan sudah diproses.');
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Pengajuan cuti ditolak.');
    }
}
