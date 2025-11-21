@if(!empty($topScorers))
    <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm relative">
        {{-- League Header --}}
        <div class="mb-6 pb-4 border-b border-slate-700/50">
            <h3 class="text-xl sm:text-2xl font-bold text-white">
                {{ $selectedLeague['country_name'] ? $selectedLeague['country_name'] . ': ' : '' }}{{ $selectedLeague['name'] ?? 'N/A' }}
            </h3>
        </div>
        
        {{-- Top Scorers Table --}}
        <div class="overflow-x-auto scrollbar-hide">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-emerald-500/20 to-green-600/20 border-b border-emerald-500/30">
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-emerald-400 uppercase tracking-wider">#</th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-emerald-400 uppercase tracking-wider">Cầu thủ</th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-emerald-400 uppercase tracking-wider">Đội bóng</th>
                        <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-emerald-400 uppercase tracking-wider">Trận</th>
                        <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-emerald-400 uppercase tracking-wider">Bàn thắng</th>
                        <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-emerald-400 uppercase tracking-wider">Sân nhà</th>
                        <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-emerald-400 uppercase tracking-wider">Sân khách</th>
                        <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-emerald-400 uppercase tracking-wider">Phạt đền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topScorers as $index => $scorer)
                        @php
                            $pos = $index + 1;
                            $playerName = $scorer['player_name'] ?? 'N/A';
                            $teamName = $scorer['team_name'] ?? 'N/A';
                            $matchesPlayed = $scorer['matches_played'] ?? 0;
                            $goals = $scorer['goals'] ?? [];
                            $overallGoals = $goals['overall'] ?? 0;
                            $homeGoals = $goals['home'] ?? 0;
                            $awayGoals = $goals['away'] ?? 0;
                            $penalties = $scorer['penalties'] ?? null;
                        @endphp
                        <tr class="hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200">
                            <td class="px-3 sm:px-4 py-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                    {{ $pos <= 3 ? 'bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold' : 
                                       ($pos <= 10 ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold' : 
                                       'bg-slate-700 text-gray-300 font-medium') }}">
                                    {{ $pos }}
                                </div>
                            </td>
                            <td class="px-3 sm:px-4 py-3">
                                <div class="text-sm font-semibold text-white">{{ $playerName }}</div>
                            </td>
                            <td class="px-3 sm:px-4 py-3">
                                <div class="text-sm text-gray-300">{{ $teamName }}</div>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-center">
                                <div class="text-sm text-gray-300">{{ $matchesPlayed }}</div>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-center">
                                <div class="inline-flex items-center justify-center px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white text-sm font-black rounded-lg shadow-lg shadow-emerald-500/25 min-w-[50px]">
                                    {{ $overallGoals }}
                                </div>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-center">
                                <div class="text-sm text-gray-300">{{ $homeGoals }}</div>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-center">
                                <div class="text-sm text-gray-300">{{ $awayGoals }}</div>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-center">
                                <div class="text-sm text-gray-300">
                                    @if($penalties !== null)
                                        {{ $penalties }}
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

