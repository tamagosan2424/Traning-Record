<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-white">統計・進捗</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- 今月のサマリー --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">今月のトレーニング回数</span>
                    </div>
                    <p class="text-4xl font-bold text-white">{{ $monthStats['count'] }}<span class="text-base font-normal text-slate-500 ml-2">回</span></p>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">今月の総ボリューム</span>
                    </div>
                    <p class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-indigo-400">
                        {{ number_format($monthStats['total_volume'] / 1000, 1) }}<span class="text-base font-normal text-slate-500 ml-2">トン</span>
                    </p>
                </div>
            </div>

            {{-- 部位別トレーニング頻度 --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <h3 class="font-semibold text-white mb-5">今月の部位別トレーニング頻度</h3>
                @if(collect($bodyPartFrequency)->sum('count') === 0)
                    <p class="text-slate-500 text-sm text-center py-6">今月のトレーニング記録がありません</p>
                @else
                <div class="space-y-4">
                    @foreach($bodyPartFrequency as $item)
                    @if($item['count'] > 0)
                    <div class="flex items-center gap-3">
                        <span class="text-lg w-7 text-center shrink-0">{{ $item['icon'] }}</span>
                        <span class="text-sm text-slate-300 w-14 shrink-0">{{ $item['name'] }}</span>
                        <div class="flex-1 bg-slate-800 rounded-full h-2.5 overflow-hidden">
                            @php
                                $maxCount = collect($bodyPartFrequency)->max('count');
                                $width = $maxCount > 0 ? round(($item['count'] / $maxCount) * 100) : 0;
                            @endphp
                            <div class="bg-gradient-to-r from-violet-500 to-indigo-500 h-2.5 rounded-full transition-all"
                                 style="width: {{ $width }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-slate-300 w-10 text-right shrink-0">{{ $item['count'] }}回</span>
                    </div>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- 種目別進捗グラフ --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <h3 class="font-semibold text-white mb-5">種目別進捗グラフ</h3>

                <div class="flex flex-wrap gap-3 mb-5">
                    <div class="flex-1 min-w-52">
                        <select id="exercise-select"
                                class="w-full bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                            <option value="">種目を選択してください</option>
                            @foreach($exercises->groupBy('bodyPart.name') as $bpName => $exList)
                            <optgroup label="{{ $bpName }}">
                                @foreach($exList as $ex)
                                <option value="{{ $ex->id }}">{{ $ex->name }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select id="period-select"
                                class="bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
                            <option value="month1">1ヶ月</option>
                            <option value="month3" selected>3ヶ月</option>
                            <option value="month6">6ヶ月</option>
                            <option value="year1">1年</option>
                        </select>
                    </div>
                </div>

                <div id="chart-container" class="hidden">
                    <div class="flex flex-wrap gap-4 mb-4">
                        <label class="flex items-center gap-2 text-sm text-slate-400 cursor-pointer hover:text-slate-200 transition">
                            <input type="radio" name="chart-type" value="estimated1rm" checked class="accent-violet-500">
                            推定1RM (kg)
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-400 cursor-pointer hover:text-slate-200 transition">
                            <input type="radio" name="chart-type" value="volume" class="accent-violet-500">
                            総ボリューム (kg)
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-400 cursor-pointer hover:text-slate-200 transition">
                            <input type="radio" name="chart-type" value="max_weight" class="accent-violet-500">
                            最大重量 (kg)
                        </label>
                    </div>
                    <canvas id="progress-chart" height="90"></canvas>
                </div>
                <div id="chart-empty" class="py-10 text-center text-slate-500 text-sm">
                    <svg class="w-10 h-10 text-slate-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    種目を選択するとグラフが表示されます
                </div>
            </div>

        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let chart = null;
let chartData = [];

const exerciseSelect = document.getElementById('exercise-select');
const periodSelect   = document.getElementById('period-select');

function loadChart() {
    const exerciseId = exerciseSelect.value;
    const period = periodSelect.value;
    if (!exerciseId) return;

    fetch(`/statistics/exercise-data?exercise_id=${exerciseId}&period=${period}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    }).then(r => r.json()).then(data => {
        chartData = data;
        renderChart();
    });
}

function renderChart() {
    const type   = document.querySelector('input[name="chart-type"]:checked').value;
    const labels = chartData.map(d => d.date);
    const values = chartData.map(d => d[type]);

    const labelMap = { estimated1rm: '推定1RM (kg)', volume: '総ボリューム (kg)', max_weight: '最大重量 (kg)' };
    const colorMap = { estimated1rm: '#8b5cf6', volume: '#10b981', max_weight: '#f59e0b' };

    document.getElementById('chart-container').classList.remove('hidden');
    document.getElementById('chart-empty').classList.add('hidden');

    if (chart) chart.destroy();

    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = '#1e293b';

    const ctx = document.getElementById('progress-chart').getContext('2d');
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: labelMap[type],
                data: values,
                borderColor: colorMap[type],
                backgroundColor: colorMap[type] + '15',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: colorMap[type],
                pointBorderColor: '#0f172a',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    borderColor: '#334155',
                    borderWidth: 1,
                    titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9',
                    callbacks: { label: ctx => `  ${ctx.parsed.y.toFixed(1)} kg` }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: { color: '#1e293b' },
                    ticks: { color: '#64748b' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b' }
                }
            }
        }
    });
}

exerciseSelect.addEventListener('change', loadChart);
periodSelect.addEventListener('change', loadChart);
document.querySelectorAll('input[name="chart-type"]').forEach(r => r.addEventListener('change', renderChart));
</script>
</x-app-layout>
