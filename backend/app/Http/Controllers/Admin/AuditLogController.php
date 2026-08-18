<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = AdminAuditLog::with('admin')->latest()->paginate(50);
        return view('admin.audit_logs', compact('logs'));
    }
}
