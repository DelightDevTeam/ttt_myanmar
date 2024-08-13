<?php

namespace App\Http\Controllers\Admin\TwoD;

use App\Http\Controllers\Controller;
use App\Services\LotteryAllPrizeSentService;

class AllLotteryWinPrizeSentController extends Controller
{
    protected $prizeSentService;

    public function __construct(LotteryAllPrizeSentService $prizeSentService)
    {
        $this->prizeSentService = $prizeSentService;
    }

    public function TwoAllWinHistoryForAdmin()
    {
        try {
            $data = $this->prizeSentService->AllWinPrizeSentForAdmin();

            return view('admin.two_d.all_winner_history', [
                'results' => $data['results'],
                'morning_totalPrizeAmount' => $data['morning_totalPrizeAmount'],
                'evening_totalPrizeAmount' => $data['evening_totalPrizeAmount'],

            ]);

        } catch (\Exception $e) {
            return view('admin.two_d.all_winner_history', [
                'error' => 'Failed to retrieve data. Please try again later.',
            ]);
        }
    }
}
