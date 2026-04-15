<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('workouts.index') }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-300 hover:bg-slate-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-bold text-lg text-white">ワークアウトを記録</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('workouts.store') }}" id="workout-form">
                @csrf

                @if($errors->any())
                <div class="mb-5 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
                @endif

                {{-- 基本情報 --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 mb-5">
                    <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">基本情報</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">日付 <span class="text-red-400">*</span></label>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-3 py-2.5 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">メモ <span class="text-slate-600 font-normal">（任意）</span></label>
                            <input type="text" name="memo" value="{{ old('memo') }}" placeholder="例: 胸の日、調子良かった"
                                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-3 py-2.5 text-sm
                                          placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                        </div>
                    </div>
                </div>

                {{-- 種目リスト（JSで動的生成） --}}
                <div id="exercises-container" class="space-y-4"></div>

                {{-- 空の状態メッセージ --}}
                <div id="empty-hint" class="bg-slate-900/50 border border-dashed border-slate-700 rounded-2xl p-8 text-center mb-4">
                    <svg class="w-10 h-10 text-slate-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <p class="text-slate-500 text-sm">下から種目を追加してください</p>
                </div>

                {{-- 種目追加セレクター --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">種目を追加</p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select id="exercise-selector"
                                class="flex-1 bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                            <option value="">種目を選択...</option>
                            @foreach($bodyParts as $bp)
                            <optgroup label="{{ $bp->icon }} {{ $bp->name }}">
                                @foreach($exercises->get($bp->name, collect()) as $ex)
                                <option value="{{ $ex->id }}" data-name="{{ $ex->name }}" data-type="{{ $ex->type }}">
                                    {{ in_array($ex->id, $favoriteIds) ? '⭐ ' : '' }}{{ $ex->name }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                        <button type="button" id="add-exercise-btn"
                                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white whitespace-nowrap
                                       bg-gradient-to-r from-violet-600 to-indigo-500 hover:from-violet-500 hover:to-indigo-400
                                       shadow-lg shadow-violet-500/20 transition">
                            ＋ 追加
                        </button>
                    </div>
                </div>

                {{-- 送信 --}}
                <div class="mt-6 flex gap-3">
                    <button type="submit"
                            class="flex-1 py-3.5 rounded-xl text-sm font-bold text-white
                                   bg-gradient-to-r from-violet-600 to-indigo-500 hover:from-violet-500 hover:to-indigo-400
                                   shadow-lg shadow-violet-500/20 transition">
                        💾 ワークアウトを保存
                    </button>
                    <a href="{{ route('workouts.index') }}"
                       class="px-5 py-3.5 rounded-xl text-sm font-semibold text-slate-400 bg-slate-800 border border-slate-700 hover:bg-slate-700 transition">
                        キャンセル
                    </a>
                </div>
            </form>
        </div>
    </div>

<script>
let exerciseIndex = 0;
const typeLabel = { weight: '重量トレーニング', bodyweight: '自重トレーニング', cardio: '有酸素' };
const typeColor = { weight: 'text-indigo-400', bodyweight: 'text-emerald-400', cardio: 'text-amber-400' };

function createSetRow(exIdx, setIdx, type) {
    const num = `<span class="set-num text-xs text-slate-600 w-7 text-center shrink-0 font-mono">${setIdx + 1}</span>`;
    const del = `<button type="button" onclick="removeSet(this)" class="ml-auto text-slate-600 hover:text-red-400 transition shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>`;
    const inputCls = `w-20 bg-slate-800 border border-slate-700 text-white rounded-lg px-2.5 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500 transition`;
    const labelCls = `text-xs text-slate-500`;

    if (type === 'cardio') {
        return `<div class="flex gap-2 items-center py-1.5">
            ${num}
            <input type="number" name="exercises[${exIdx}][sets][${setIdx}][duration_min]" placeholder="0" min="0" step="1" class="${inputCls}">
            <span class="${labelCls}">分</span>
            <input type="number" name="exercises[${exIdx}][sets][${setIdx}][distance_km]" placeholder="0.0" min="0" step="0.1" class="${inputCls}">
            <span class="${labelCls}">km</span>
            ${del}
        </div>`;
    }
    return `<div class="flex gap-2 items-center py-1.5">
        ${num}
        <input type="number" name="exercises[${exIdx}][sets][${setIdx}][weight]" placeholder="0" min="0" step="0.5" class="${inputCls}">
        <span class="${labelCls}">kg</span>
        <svg class="w-3 h-3 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <input type="number" name="exercises[${exIdx}][sets][${setIdx}][reps]" placeholder="0" min="0" step="1" class="${inputCls}">
        <span class="${labelCls}">回</span>
        ${del}
    </div>`;
}

function addSet(btn, exIdx, type) {
    const container = btn.previousElementSibling;
    const idx = container.children.length;
    container.insertAdjacentHTML('beforeend', createSetRow(exIdx, idx, type));
}

function removeSet(btn) {
    const row = btn.closest('div.flex');
    const container = row.parentElement;
    if (container.children.length > 1) {
        row.remove();
        Array.from(container.children).forEach((r, i) => {
            const span = r.querySelector('.set-num');
            if (span) span.textContent = i + 1;
        });
    }
}

function removeExercise(btn) {
    btn.closest('.exercise-block').remove();
    document.getElementById('empty-hint').classList.toggle('hidden', document.getElementById('exercises-container').children.length > 0);
}

function createExerciseBlock(id, name, type) {
    const idx = exerciseIndex++;
    const div = document.createElement('div');
    div.className = 'exercise-block bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden';
    div.innerHTML = `
        <input type="hidden" name="exercises[${idx}][exercise_id]" value="${id}">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-1 h-8 rounded-full bg-gradient-to-b from-violet-500 to-indigo-500"></div>
                <div>
                    <p class="font-semibold text-white text-sm">${name}</p>
                    <p class="text-xs ${typeColor[type] || 'text-slate-500'} mt-0.5">${typeLabel[type] || type}</p>
                </div>
            </div>
            <button type="button" onclick="removeExercise(this)"
                    class="p-1.5 rounded-lg text-slate-600 hover:text-red-400 hover:bg-red-400/10 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
        <div class="px-5 py-3">
            <div class="prev-info hidden text-xs text-violet-400 mb-3 flex items-center gap-1.5">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>前回: <span class="prev-text"></span></span>
            </div>
            <div class="sets-container space-y-1">${createSetRow(idx, 0, type)}</div>
            <button type="button" onclick="addSet(this, ${idx}, '${type}')"
                    class="mt-3 flex items-center gap-1.5 text-xs font-medium text-violet-400 hover:text-violet-300 transition py-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                セットを追加
            </button>
        </div>`;

    fetch(`/workouts/previous-sets?exercise_id=${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    }).then(r => r.ok ? r.json() : null).then(data => {
        if (data?.sets?.length > 0) {
            const info = div.querySelector('.prev-info');
            const text = data.sets.map(s => type === 'cardio'
                ? `${s.duration_min ?? '-'}分/${s.distance_km ?? '-'}km`
                : `${s.weight ?? 0}kg×${s.reps ?? 0}`
            ).join('  ');
            div.querySelector('.prev-text').textContent = text;
            info.classList.remove('hidden');
        }
    }).catch(() => {});

    return div;
}

document.getElementById('add-exercise-btn').addEventListener('click', function () {
    const sel = document.getElementById('exercise-selector');
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) { alert('種目を選択してください'); return; }
    const container = document.getElementById('exercises-container');
    container.appendChild(createExerciseBlock(opt.value, opt.dataset.name, opt.dataset.type));
    document.getElementById('empty-hint').classList.add('hidden');
    sel.selectedIndex = 0;
});
</script>
</x-app-layout>
