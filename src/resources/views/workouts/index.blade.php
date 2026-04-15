<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-lg text-white">ワークアウト履歴</h2>
            <a href="{{ route('workouts.create') }}"
               class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white
                      bg-gradient-to-r from-violet-600 to-indigo-500 hover:from-violet-500 hover:to-indigo-400
                      shadow-lg shadow-violet-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                記録する
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- フィルタ --}}
            <form method="GET" class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
                <div class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-32">
                        <label class="block text-xs text-slate-500 mb-1.5 font-medium">開始日</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500 transition">
                    </div>
                    <div class="flex-1 min-w-32">
                        <label class="block text-xs text-slate-500 mb-1.5 font-medium">終了日</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1.5 font-medium">部位</label>
                        <select name="body_part"
                                class="bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500 transition">
                            <option value="">すべて</option>
                            @foreach($bodyParts as $bp)
                            <option value="{{ $bp->slug }}" @selected(request('body_part') === $bp->slug)>
                                {{ $bp->icon }} {{ $bp->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-violet-600 hover:bg-violet-500 transition">
                            検索
                        </button>
                        @if(request()->hasAny(['date_from','date_to','body_part']))
                        <a href="{{ route('workouts.index') }}"
                           class="px-4 py-2 rounded-xl text-sm font-medium text-slate-400 bg-slate-800 hover:bg-slate-700 transition">
                            リセット
                        </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- ワークアウト一覧 --}}
            @if($workouts->isEmpty())
            <div class="bg-slate-900 border border-dashed border-slate-700 rounded-2xl p-12 text-center">
                <svg class="w-12 h-12 text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-slate-400 font-medium mb-1">記録がありません</p>
                <p class="text-slate-600 text-sm mb-5">最初のワークアウトを記録しましょう</p>
                <a href="{{ route('workouts.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white
                          bg-gradient-to-r from-violet-600 to-indigo-500 hover:from-violet-500 hover:to-indigo-400 transition">
                    ＋ 記録する
                </a>
            </div>
            @else
            <div class="space-y-3">
                @foreach($workouts as $workout)
                <div class="bg-slate-900 border border-slate-800 hover:border-slate-700 rounded-2xl transition group">
                    <a href="{{ route('workouts.show', $workout) }}" class="block p-5">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <span class="font-bold text-white">{{ $workout->date->format('Y年n月j日') }}</span>
                                    <span class="text-xs text-slate-500 bg-slate-800 px-2 py-0.5 rounded-md">
                                        {{ $workout->date->locale('ja')->isoFormat('ddd') }}
                                    </span>
                                    @if($workout->memo)
                                    <span class="text-xs text-slate-500">— {{ $workout->memo }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-base font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-indigo-400">
                                    {{ number_format($workout->total_volume) }} kg
                                </p>
                                <p class="text-xs text-slate-600">総ボリューム</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($workout->workoutExercises as $we)
                            <span class="text-xs px-2.5 py-1 rounded-lg bg-slate-800 text-slate-400 border border-slate-700/50">
                                {{ $we->exercise->bodyPart->icon ?? '' }} {{ $we->exercise->name }}
                                <span class="text-slate-600 ml-1">{{ $we->sets->count() }}set</span>
                            </span>
                            @endforeach
                        </div>
                    </a>
                    <div class="flex border-t border-slate-800/80">
                        <a href="{{ route('workouts.edit', $workout) }}"
                           class="flex-1 py-2.5 text-center text-xs font-medium text-slate-500 hover:text-slate-300 hover:bg-slate-800/50 transition rounded-bl-2xl">
                            ✏️ 編集
                        </a>
                        <form method="POST" action="{{ route('workouts.destroy', $workout) }}"
                              onsubmit="return confirm('削除しますか？')" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-full py-2.5 text-xs font-medium text-slate-600 hover:text-red-400 hover:bg-red-400/5 transition rounded-br-2xl">
                                🗑 削除
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ページネーション --}}
            @if($workouts->hasPages())
            <div class="flex justify-center">
                {{ $workouts->links() }}
            </div>
            @endif
            @endif

        </div>
    </div>
</x-app-layout>
