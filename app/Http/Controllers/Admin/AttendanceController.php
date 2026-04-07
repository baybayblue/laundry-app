<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $attendances = Attendance::with('user')
            ->whereDate('date', $date)
            ->latest()
            ->paginate(15);

        // Stats for the day
        $stats = [
            'total_present' => Attendance::whereDate('date', $date)->count(),
            'total_late' => Attendance::whereDate('date', $date)->where('status', 'late')->count(),
            'total_absent' => User::where('role', 'employee')->count() - Attendance::whereDate('date', $date)->count(),
        ];

        return view('admin.attendances.index', compact('attendances', 'date', 'stats'));
    }

    public function clockPage()
    {
        $todayAttendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', Carbon::today())
            ->first();

        return view('admin.attendances.clock', compact('todayAttendance'));
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

        return redirect()->route('admin.attendances.clock')->with('success', 'Berhasil melakukan absen masuk!');
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

        return redirect()->route('admin.attendances.clock')->with('success', 'Berhasil melakukan absen keluar!');
    }

    public function report(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $employees = User::where('role', 'employee')->get();
        
        $reportData = [];
        foreach ($employees as $employee) {
            $stats = DB::table('attendances')
                ->where('user_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->selectRaw("
                    COUNT(*) as present,
                    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late,
                    SUM(CASE WHEN status = 'leave' THEN 1 ELSE 0 END) as leave_count
                ")
                ->first();

            $reportData[] = [
                'name' => $employee->name,
                'position' => $employee->position,
                'present' => $stats->present,
                'late' => $stats->late,
                'leave' => $stats->leave_count,
                'absent' => 0 // This needs logic based on work days in month
            ];
        }

        return view('admin.attendances.report', compact('reportData', 'month', 'year'));
    }
}
