<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('workouts.index') }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-300 hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="font-bold text-lg text-white">{{ $workout->date->format('Y年n月j日') }}</h2>
                    <p class="text-xs text-slate-500">{{ $workout->date->locale('ja')->isoFormat('dddd') }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('workouts.edit', $workout) }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-300 bg-slate-800 border border-slate-700 hover:bg-slate-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    編集
                </a>
                <form method="POST" action="{{ route('workouts.destroy', $workout) }}"
                      onsubmit="return confirm('削除しますか？')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium text-red-400 bg-red-400/10 border border-red-400/20 hover:bg-red-400/20 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        削除
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- サマリー --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">種目数</p>
                        <p class="text-2xl font-bold text-white">{{ $workout->workoutExercises->count() }}<span class="text-sm font-normal text-slate-500 ml-1">種目</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">総セット数</p>
                        <p class="text-2xl font-bold text-white">{{ $workout->workoutExercises->sum(fn($we) => $we->sets->count()) }}<span class="text-sm font-normal text-slate-500 ml-1">set</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">総ボリューム</p>
                        <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-indigo-400">
                            {{ number_format($workout->total_volume) }}<span class="text-sm font-normal text-slate-500 ml-1">kg</span>
                        </p>
                    </div>
                </div>
                @if($workout->memo)
                <div class="mt-4 pt-4 border-t border-slate-800 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    <p class="text-sm text-slate-400">{{ $workout->memo }}</p>
                </div>
                @endif
            </div>

            {{-- 種目別記録 --}}
            @foreach($workout->workoutExercises as $we)
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-8 rounded-full
                            @if($we->exercise->type === 'weight') bg-gradient-to-b from-indigo-500 to-blue-500
                            @elseif($we->exercise->type === 'bodyweight') bg-gradient-to-b from-emerald-500 to-teal-500
                            @else bg-gradient-to-b from-amber-500 to-orange-500 @endif">
                        </div>
                        <div>
                            <p class="font-semibold text-white text-sm">{{ $we->exercise->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $we->exercise->bodyPart->icon ?? '' }} {{ $we->exercise->bodyPart->name ?? '' }}</p>
                        </div>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-lg font-medium
                        @if($we->exercise->type === 'weight') bg-indigo-500/10 text-indigo-400 border border-indigo-500/20
                        @elseif($we->exercise->type === 'bodyweight') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                        @else bg-amber-500/10 text-amber-400 border border-amber-500/20 @endif">
                        @if($we->exercise->type === 'weight') 重量
                        @elseif($we->exercise->type === 'bodyweight') 自重
                        @else 有酸素 @endif
                    </span>
                </div>

                <div class="p-5">
                    @if($we->exercise->type === 'cardio')
                    <div class="space-y-2">
                        @foreach($we->sets as $set)
                        <div class="flex items-center gap-4 text-sm">
                            <span class="text-slate-600 font-mono w-6 text-center text-xs">{{ $set->set_number }}</span>
                            @if($set->duration_min) <span class="text-slate-300">{{ $set->duration_min }} <span class="text-slate-500 text-xs">分</span></span> @endif
                            @if($set->distance_km) <span class="text-slate-300">{{ $set->distance_km }} <span class="text-slate-500 text-xs">km</span></span> @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left">
                                <th class="pb-2 text-xs font-medium text-slate-600 w-10">SET</th>
                                <th class="pb-2 text-xs font-medium text-slate-600">重量</th>
                                <th class="pb-2 text-xs font-medium text-slate-600">回数</th>
                                <th class="pb-2 text-xs font-medium text-slate-600 text-right">ボリューム</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($we->sets as $set)
                            <tr class="border-t border-slate-800/60">
                                <td class="py-2.5 text-slate-600 font-mono text-xs">{{ $set->set_number }}</td>
                                <td class="py-2.5 font-semibold text-white">{{ $set->weight ?? '—' }} <span class="text-xs text-slate-500 font-normal">kg</span></td>
                                <td class="py-2.5 text-slate-300">{{ $set->reps ?? '—' }} <span class="text-xs text-slate-500">回</span></td>
                                <td class="py-2.5 text-right">
                                    @if($set->weight && $set->reps)
                                    <span class="text-sm font-medium text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-indigo-400">
                                        {{ number_format($set->weight * $set->reps) }}
                                    </span>
                                    <span class="text-xs text-slate-600"> kg</span>
                                    @else
                                    <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-slate-700">
                                <td colspan="3" class="pt-2.5 text-xs text-slate-500">合計</td>
                                <td class="pt-2.5 text-right font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-indigo-400">
                                    {{ number_format($we->sets->sum(fn($s) => ($s->weight ?? 0) * ($s->reps ?? 0))) }} kg
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    @endif
                </div>
            </div>
            @endforeach

        </div>
    </div>
</x-app-layout>
