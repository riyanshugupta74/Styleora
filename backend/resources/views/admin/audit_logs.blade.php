@extends('layouts.admin')
@section('title', 'Admin | Audit Logs')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <h1 class="font-outfit text-2xl font-bold text-gray-900 tracking-tight">Audit Logs</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Timestamp</th>
                        <th class="p-4">Admin</th>
                        <th class="p-4">Action</th>
                        <th class="p-4">Entity</th>
                        <th class="p-4">IP Address</th>
                        <th class="p-4 text-center">Details</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($logs as $log)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-gray-600 whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            <td class="p-4 font-medium text-gray-900">{{ $log->admin->name ?? 'System' }} <br><span class="text-xs text-gray-500 font-normal">{{ $log->admin->email ?? '' }}</span></td>
                            <td class="p-4">
                                <span class="font-bold text-gray-700 uppercase text-[10px] tracking-wider">{{ $log->action }}</span>
                            </td>
                            <td class="p-4 text-gray-900">
                                @if($log->entity)
                                    <span class="bg-gray-100 px-2.5 py-1 rounded-sm text-[10px] font-bold uppercase tracking-wider">{{ $log->entity }}</span>
                                    @if($log->entity_id) <span class="font-mono text-gray-500 text-xs ml-1">#{{ $log->entity_id }}</span> @endif
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono text-xs text-gray-500">{{ $log->ip_address ?? 'N/A' }}</td>
                            <td class="p-4 text-center">
                                @if($log->details)
                                    <div x-data="{ open: false }">
                                        <button @click="open = true" class="text-[#ff3f6c] hover:text-[#e02e5c] font-bold text-xs uppercase tracking-widest border border-[#ff3f6c] hover:bg-[#ff3f6c] hover:text-white px-3 py-1.5 rounded-sm transition-colors inline-block">View</button>
                                        
                                        <!-- Modal -->
                                        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;">
                                            <div @click.away="open = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-lg overflow-hidden text-left">
                                                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                                                    <h3 class="font-bold text-gray-900 uppercase tracking-wider text-sm">Log Details</h3>
                                                    <button @click="open = false" class="text-gray-400 hover:text-gray-900"><i class="fa-solid fa-xmark"></i></button>
                                                </div>
                                                <div class="p-6 bg-gray-50 max-h-96 overflow-y-auto">
                                                    <pre class="text-xs font-mono text-gray-700 whitespace-pre-wrap">{{ $log->details }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($logs->count() == 0)
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">No audit logs found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
