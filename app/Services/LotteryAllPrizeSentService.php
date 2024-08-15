<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LotteryAllPrizeSentService
{
    public function AllWinPrizeSentForAdmin()
    {
        try {
            $results = DB::table('lottery_two_digit_pivot')
                ->join('users', 'lottery_two_digit_pivot.user_id', '=', 'users.id')
                ->select(
                    'users.name as user_name',
                    'users.phone as user_phone',
                    'lottery_two_digit_pivot.bet_digit',
                    'lottery_two_digit_pivot.res_date',
                    'lottery_two_digit_pivot.sub_amount',
                    'lottery_two_digit_pivot.session',
                    'lottery_two_digit_pivot.res_time',
                    'lottery_two_digit_pivot.prize_sent'
                )
                ->where('lottery_two_digit_pivot.prize_sent', true)
                ->get();

            // Calculate total prize amount
            $morning_totalPrizeAmount = 0;
        $evening_totalPrizeAmount = 0;

        foreach ($results as $result) {
            if ($result->session == 'morning') {
                $morning_prizeAmount = $result->sub_amount * 85; // Prize multiplier
                $morning_totalPrizeAmount += $morning_prizeAmount; // Accumulate the total prize amount
            } elseif ($result->session == 'evening') {
                $evening_prizeAmount = $result->sub_amount * 90; // Prize multiplier
                $evening_totalPrizeAmount += $evening_prizeAmount; // Accumulate the total prize amount
            }
        }

        return [
            'results' => $results,
            'morning_totalPrizeAmount' => $morning_totalPrizeAmount,
            'evening_totalPrizeAmount' => $evening_totalPrizeAmount
        ];

        } catch (\Exception $e) {
            Log::error('Error retrieving prize_sent data: '.$e->getMessage());

            return ['results' => collect([]), 'morning_totalPrizeAmount' => 0];
        }
    }
}
