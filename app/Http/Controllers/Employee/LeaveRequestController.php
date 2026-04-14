<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('employee.leave_requests.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:leave,sick,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        LeaveRequest::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan cuti berhasil dikirim! Menunggu persetujuan Owner.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->user_id !== auth()->id()) {
            abort(403);
        }

        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan yang masih status "Menunggu" yang dapat dibatalkan.');
        }

        $leaveRequest->delete();

        return back()->with('success', 'Pengajuan cuti berhasil dibatalkan.');
    }
}
