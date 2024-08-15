<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthWinLotteryPrizeService
{
    
    public function LotteryWinnersPrize()
{
    try {
        $today = Carbon::today()->startOfDay(); // Start of the current day
        $tomorrow = Carbon::tomorrow()->startOfDay(); // Start of the next day, for upper boundary
        $userId = Auth::id(); // Get the authenticated user's ID

        // Retrieve results with adjusted conditions, focusing on current day's data
        $results = DB::table('lottery_two_digit_pivot')
            ->join('users', 'lottery_two_digit_pivot.user_id', '=', 'users.id')
            ->select(
                'users.name as user_name',
                'users.phone as user_phone',
                'users.profile as user_profile',
                'lottery_two_digit_pivot.bet_digit',
                'lottery_two_digit_pivot.res_date',
                'lottery_two_digit_pivot.sub_amount',
                'lottery_two_digit_pivot.res_time',
                'lottery_two_digit_pivot.prize_sent',
                'lottery_two_digit_pivot.session'
            )
            ->where('lottery_two_digit_pivot.prize_sent', true)
            ->whereBetween('lottery_two_digit_pivot.res_date', [$today, $tomorrow])
            ->where('lottery_two_digit_pivot.user_id', $userId)
            ->get();

        // Initialize prize amounts
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
        Log::error('Error retrieving prize_sent data: ' . $e->getMessage());

        return [
            'results' => collect([]), 
            'morning_totalPrizeAmount' => 0,
            'evening_totalPrizeAmount' => 0
        ];
    }
}

    // public function LotteryWinnersPrize()
    // {
    //     try {
    //         $today = Carbon::today()->startOfDay(); // Start of the current day
    //         $tomorrow = Carbon::tomorrow()->startOfDay(); // Start of the next day, for upper boundary
    //         $userId = Auth::id(); // Get the authenticated user's ID

    //         // Retrieve results with adjusted conditions, focusing on current day's data
    //         $results = DB::table('lottery_two_digit_pivot')
    //             ->join('users', 'lottery_two_digit_pivot.user_id', '=', 'users.id')
    //             ->select(
    //                 'users.name as user_name',
    //                 'users.phone as user_phone',
    //                 'users.profile as user_profile',
    //                 'lottery_two_digit_pivot.bet_digit',
    //                 'lottery_two_digit_pivot.res_date',
    //                 'lottery_two_digit_pivot.sub_amount',
    //                 'lottery_two_digit_pivot.res_time',
    //                 'lottery_two_digit_pivot.prize_sent',
    //                 'lottery_two_digit_pivot.session'

    //             )
    //             // Only include prize-sent records
    //             ->where('lottery_two_digit_pivot.prize_sent', true)
    //             // Filter by today's date
    //             ->whereBetween('lottery_two_digit_pivot.res_date', [$today, $tomorrow]) // Today's data
    //             // Only consider authenticated user
    //             ->where('lottery_two_digit_pivot.user_id', $userId)
    //             ->get();

    //         // Calculate the total prize amount
    //         $morning_totalPrizeAmount = 0;
    //         $evening_totalPrizeAmount = 0;
    //         foreach ($results as $result) {
    //             if($result->session == 'morning'){
    //             $morning_prizeAmount = $result->sub_amount * 85; // Prize multiplier
    //             }else{
    //                 $evening_totalPrizeAmount = $result->sub_amount * 95;
    //             }

    //             $morning_totalPrizeAmount += $morning_prizeAmount; // Accumulate the total prize amount
    //         }

    //         return ['results' => $results, 'morning_totalPrizeAmount' => $morning_totalPrizeAmount];
    //     } catch (\Exception $e) {
    //         Log::error('Error retrieving prize_sent data: ' . $e->getMessage());

    //         return ['results' => collect([]), 'morning_totalPrizeAmount' => 0];
    //     }
    // }
}