<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-white mb-1">アカウント作成</h1>
        <p class="text-slate-500 text-sm">無料で始めてトレーニングを記録しましょう</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- 名前 --}}
        <div>
            <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">名前</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="山田 太郎"
                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm
                          placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
            @error('name')
            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- メールアドレス --}}
        <div>
            <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   placeholder="example@email.com"
                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm
                          placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
            @error('email')
            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワード --}}
        <div>
            <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">パスワード</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   placeholder="8文字以上"
                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm
                          placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
            @error('password')
            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワード確認 --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1.5">パスワード（確認）</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   placeholder="もう一度入力"
                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm
                          placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
            @error('password_confirmation')
            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- 登録ボタン --}}
        <button type="submit"
                class="w-full py-3 rounded-xl text-sm font-bold text-white
                       bg-gradient-to-r from-violet-600 to-indigo-500 hover:from-violet-500 hover:to-indigo-400
                       shadow-lg shadow-violet-500/20 transition mt-2">
            アカウントを作成
        </button>
    </form>

    {{-- ログインリンク --}}
    <div class="mt-6 pt-6 border-t border-slate-800 text-center">
        <p class="text-sm text-slate-500">
            すでにアカウントをお持ちの方は
            <a href="{{ route('login') }}" class="text-violet-400 hover:text-violet-300 font-medium transition">
                サインイン
            </a>
        </p>
    </div>
</x-guest-layout>
