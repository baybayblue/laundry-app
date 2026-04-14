<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function clockPage()
    {
        $todayAttendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', Carbon::today())
            ->first();

        return view('employee.attendances.clock', compact('todayAttendance'));
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'lat' => 'required',
            'long' => 'required',
            'note' => 'nullable|string|max:255',
        ]);

        $now = Carbon::now();
        $date = $now->toDateString();

        // Check if already clocked in
        $exists = Attendance::where('user_id', auth()->id())
            ->whereDate('date', $date)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }

        // Determine status (08:30 threshold)
        $startTime = Carbon::createFromTimeString('08:30:00');
        $status = $now->gt($startTime) ? 'late' : 'present';

        Attendance::create([
            'user_id' => auth()->id(),
            'date' => $date,
            'clock_in' => $now->toTimeString(),
            'lat_in' => $request->lat,
            'long_in' => $request->long,
            'status' => $status,
            'note' => $request->note,
        ]);

        return redirect()->route('employee.attendances.clock')->with('success', 'Berhasil melakukan absen masuk!');
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'lat' => 'required',
            'long' => 'required',
        ]);

        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Data absen masuk tidak ditemukan.');
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'Anda sudah melakukan absen keluar hari ini.');
        }

        $attendance->update([
            'clock_out' => Carbon::now()->toTimeString(),
            'lat_out' => $request->lat,
            'long_out' => $request->long,
        ]);

        return redirect()->route('employee.attendances.clock')->with('success', 'Berhasil melakukan absen keluar!');
    }
}
