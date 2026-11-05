<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Seguridad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-green-600">Exitosos</h3>
                    <p class="text-3xl">{{ $stats['success'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-yellow-600">Fallidos</h3>
                    <p class="text-3xl">{{ $stats['failed'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-red-600">Bloqueados</h3>
                    <p class="text-3xl">{{ $stats['blocked'] }}</p>
                </div>
            </div>

            <!-- Tabla de IPs sospechosas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">IPs Sospechosas (3+ fallos en 5 min)</h3>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border">IP</th>
                            <th class="p-2 border">Fallos</th>
                            <th class="p-2 border">Clasificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suspiciousIps as $item)
                            <tr>
                                <td class="p-2 border">{{ $item->ip_address }}</td>
                                <td class="p-2 border">{{ $item->total }}</td>
                                <td class="p-2 border font-bold {{ $item->total >= 5 ? 'text-red-600' : 'text-yellow-600' }}">
                                    {{ $item->total >= 5 ? 'CRÍTICO' : 'ALERTA' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-2 border text-center">No hay IPs sospechosas en este momento.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>