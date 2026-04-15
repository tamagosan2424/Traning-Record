<x-guest-layout>
    {{-- セッションステータス --}}
    @if (session('status'))
    <div class="mb-5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm">
        {{ session('status') }}
    </div>
    @endif

    <div class="mb-6">
        <h1 class="text-xl font-bold text-white mb-1">おかえりなさい</h1>
        <p class="text-slate-500 text-sm">アカウントにサインインしてください</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- メールアドレス --}}
        <div>
            <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm
                          placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
            @error('email')
            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワード --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-slate-300">パスワード</label>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-violet-400 hover:text-violet-300 transition">
                    パスワードを忘れた？
                </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm
                          placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
            @error('password')
            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- ログイン状態を保持 --}}
        <label class="flex items-center gap-2.5 cursor-pointer">
            <input id="remember_me" type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-violet-500 focus:ring-violet-500/30">
            <span class="text-sm text-slate-400">ログイン状態を保持する</span>
        </label>

        {{-- ログインボタン --}}
        <button type="submit"
                class="w-full py-3 rounded-xl text-sm font-bold text-white
                       bg-gradient-to-r from-violet-600 to-indigo-500 hover:from-violet-500 hover:to-indigo-400
                       shadow-lg shadow-violet-500/20 transition mt-2">
            サインイン
        </button>
    </form>

    {{-- 登録リンク --}}
    <div class="mt-6 pt-6 border-t border-slate-800 text-center">
        <p class="text-sm text-slate-500">
            アカウントをお持ちでない方は
            <a href="{{ route('register') }}" class="text-violet-400 hover:text-violet-300 font-medium transition">
                新規登録
            </a>
        </p>
    </div>
</x-guest-layout>
