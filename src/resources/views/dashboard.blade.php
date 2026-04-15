<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ヒーローバナー --}}
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-violet-600/20 via-slate-900 to-indigo-900/30 border border-violet-500/20 p-6 sm:p-8">
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(139,92,246,0.15),_transparent_60%)]"></div>
                <div class="relative">
                    <p class="text-slate-400 text-sm mb-1">おかえりなさい 👋</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-4">
                        {{ Auth::user()->name }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-indigo-400">さん</span>
                    </h1>
                    <a href="{{ route('workouts.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white
                              bg-gradient-to-r from-violet-600 to-indigo-500 hover:from-violet-500 hover:to-indigo-400
                              shadow-lg shadow-violet-500/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        今日のワークアウトを記録する
                    </a>
                </div>
            </div>

            {{-- 統計カード --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">今週</span>
                    </div>
                    <p class="text-3xl font-bold text-white">{{ $stats['week_count'] }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">トレーニング</p>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">今週ボリューム</span>
                    </div>
                    <p class="text-3xl font-bold text-white">{{ number_format($stats['week_volume'] / 1000, 1) }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">トン</p>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">今月</span>
                    </div>
                    <p class="text-3xl font-bold text-white">{{ $stats['month_count'] }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">トレーニング</p>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">ストリーク</span>
                    </div>
                    <p class="text-3xl font-bold text-white">{{ $stats['streak'] }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">日連続</p>
                </div>
            </div>

            {{-- 最近のワークアウト --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">最近のワークアウト</h2>
                    <a href="{{ route('workouts.index') }}" class="text-sm text-violet-400 hover:text-violet-300 transition">すべて見る →</a>
                </div>

                @if($recentWorkouts->isEmpty())
                <div class="bg-slate-900 border border-dashed border-slate-700 rounded-2xl p-10 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <p class="text-slate-400 font-medium mb-1">まだ記録がありません</p>
                    <p class="text-slate-600 text-sm mb-4">最初のワークアウトを記録しましょう！</p>
                    <a href="{{ route('workouts.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white
                              bg-gradient-to-r from-violet-600 to-indigo-500 hover:from-violet-500 hover:to-indigo-400 transition">
                        ＋ 最初の記録を作る
                    </a>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($recentWorkouts as $workout)
                    <a href="{{ route('workouts.show', $workout) }}"
                       class="block bg-slate-900 border border-slate-800 hover:border-slate-700 rounded-2xl p-5 transition group">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-sm font-semibold text-white">
                                        {{ $workout->date->format('n月j日') }}
                                        <span class="text-slate-500 font-normal text-xs ml-1">{{ $workout->date->locale('ja')->isoFormat('ddd') }}</span>
                                    </span>
                                    @if($workout->memo)
                                    <span class="text-xs text-slate-500 truncate">{{ $workout->memo }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($workout->workoutExercises->take(4) as $we)
                                    <span class="text-xs px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 border border-slate-700">
                                        {{ $we->exercise->name }}
                                    </span>
                                    @endforeach
                                    @if($workout->workoutExercises->count() > 4)
                                    <span class="text-xs px-2 py-0.5 rounded-md bg-slate-800 text-slate-500">+{{ $workout->workoutExercises->count() - 4 }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-indigo-400">
                                    {{ number_format($workout->total_volume) }}
                                </p>
                                <p class="text-xs text-slate-500">kg vol</p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
