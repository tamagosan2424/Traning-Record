<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-white">種目マスタ</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- 種目追加フォーム --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">＋ カスタム種目を追加</h3>
                <form method="POST" action="{{ route('exercises.store') }}" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    <div class="flex-1 min-w-44">
                        <label class="block text-xs text-slate-500 mb-1.5 font-medium">種目名 <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="例: インクラインダンベルプレス"
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-3 py-2.5 text-sm
                                      placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                        @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1.5 font-medium">部位 <span class="text-red-400">*</span></label>
                        <select name="body_part_id"
                                class="bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                            <option value="">選択...</option>
                            @foreach($bodyParts as $bp)
                            <option value="{{ $bp->id }}" @selected(old('body_part_id') == $bp->id)>
                                {{ $bp->icon }} {{ $bp->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('body_part_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1.5 font-medium">種別 <span class="text-red-400">*</span></label>
                        <select name="type"
                                class="bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                            <option value="weight"     @selected(old('type','weight') === 'weight')>重量</option>
                            <option value="bodyweight" @selected(old('type') === 'bodyweight')>自重</option>
                            <option value="cardio"     @selected(old('type') === 'cardio')>有酸素</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white
                                   bg-gradient-to-r from-violet-600 to-indigo-500 hover:from-violet-500 hover:to-indigo-400
                                   shadow-lg shadow-violet-500/20 transition">
                        追加
                    </button>
                </form>
            </div>

            {{-- 部位別種目一覧 --}}
            @foreach($bodyParts as $bp)
            @if($bp->exercises->isNotEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-800 bg-slate-800/50">
                    <h3 class="font-semibold text-slate-200 text-sm">{{ $bp->icon }} {{ $bp->name }}</h3>
                </div>
                <div class="divide-y divide-slate-800/60">
                    @foreach($bp->exercises as $exercise)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-800/30 transition">
                        <div class="flex items-center gap-3">
                            {{-- お気に入りボタン --}}
                            <form method="POST" action="{{ route('exercises.favorite', $exercise) }}">
                                @csrf
                                <button type="submit" class="text-lg leading-none hover:scale-110 transition-transform"
                                        title="{{ in_array($exercise->id, $favoriteIds) ? 'お気に入り解除' : 'お気に入り登録' }}">
                                    {{ in_array($exercise->id, $favoriteIds) ? '⭐' : '☆' }}
                                </button>
                            </form>
                            <div>
                                <p class="text-sm font-medium text-slate-200">{{ $exercise->name }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs px-1.5 py-0.5 rounded-md font-medium
                                        @if($exercise->type === 'weight') bg-indigo-500/10 text-indigo-400
                                        @elseif($exercise->type === 'bodyweight') bg-emerald-500/10 text-emerald-400
                                        @else bg-amber-500/10 text-amber-400 @endif">
                                        @if($exercise->type === 'weight') 重量
                                        @elseif($exercise->type === 'bodyweight') 自重
                                        @else 有酸素 @endif
                                    </span>
                                    @if($exercise->is_default)
                                    <span class="text-xs text-slate-600">標準</span>
                                    @else
                                    <span class="text-xs text-violet-500">カスタム</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(!$exercise->is_default && $exercise->user_id === Auth::id())
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    onclick="openEditModal({{ $exercise->id }}, '{{ addslashes($exercise->name) }}', {{ $exercise->body_part_id }}, '{{ $exercise->type }}')"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 bg-slate-800 border border-slate-700 hover:bg-slate-700 transition">
                                編集
                            </button>
                            <form method="POST" action="{{ route('exercises.destroy', $exercise) }}"
                                  onsubmit="return confirm('この種目を削除しますか？')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-red-400 bg-red-400/10 border border-red-400/20 hover:bg-red-400/20 transition">
                                    削除
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach

        </div>
    </div>

    {{-- 編集モーダル --}}
    <div id="edit-modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
            <h3 class="font-bold text-white mb-5">種目を編集</h3>
            <form method="POST" id="edit-form">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">種目名</label>
                        <input type="text" name="name" id="edit-name"
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">部位</label>
                        <select name="body_part_id" id="edit-body-part"
                                class="w-full bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                            @foreach($bodyParts as $bp)
                            <option value="{{ $bp->id }}">{{ $bp->icon }} {{ $bp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">種別</label>
                        <select name="type" id="edit-type"
                                class="w-full bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                            <option value="weight">重量</option>
                            <option value="bodyweight">自重</option>
                            <option value="cardio">有酸素</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white
                                   bg-gradient-to-r from-violet-600 to-indigo-500 hover:from-violet-500 hover:to-indigo-400 transition">
                        更新
                    </button>
                    <button type="button" onclick="closeEditModal()"
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-400 bg-slate-800 border border-slate-700 hover:bg-slate-700 transition">
                        キャンセル
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
function openEditModal(id, name, bodyPartId, type) {
    document.getElementById('edit-form').action = `/exercises/${id}`;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-body-part').value = bodyPartId;
    document.getElementById('edit-type').value = type;
    const modal = document.getElementById('edit-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeEditModal() {
    const modal = document.getElementById('edit-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
</x-app-layout>
