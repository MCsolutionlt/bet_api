<?php

namespace App\Http\Controllers;

use App\BalanceTransaction;
use App\Bet;
use App\BetSelections;
use App\Player;
use App\Services\BetService;
use Illuminate\Support\Facades\Cache;
use Validator;
use Illuminate\Http\Request;

class BetController extends Controller
{
    /**
     * @param Request $request
     * @param BetService $betService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, BetService $betService)
    {
        if ($request->isMethod('post') && $request->all()) {
            $validator = Validator::make($request->all(), [
                'player_id'    => 'required|integer',
                'stake_amount' => 'required|string',
                'selections'   => 'required|array',
                'selections.*.id'   => 'required|integer',
                'selections.*.odds' => 'required|string',
            ]);

            if ($validator->fails()) {
                $response = $request->all();
                $response['errors'][] = $betService->getErrorMessage(1);

                return response()->json($response, 400);
            }

            // Panaudojau cache vietoje session nes lumen nepalaiko session
            if (
                Cache::get('player_id') &&
                Cache::get('player_id') == $request->input('player_id')
            ) {
                $response = $request->all();
                $response['errors'][] = $betService->getErrorMessage(10);

                return response()->json($response, 400);
            } else {
                Cache::put('player_id', $request->input('player_id'), 1);
            }

            $response = $betService->checkBet($request->all());

            if (!empty($response)) {
                return response()->json($response, 400);
            }

            sleep(rand(1,30));

            \DB::beginTransaction();
            try {
                $player = Player::find($request->input('player_id'));

                if (is_null($player)) {
                    $player = Player::create([
                        'id'      => $request->input('player_id'),
                        'balance' => 1000
                    ]);

                    BalanceTransaction::create([
                        'player_id'     => $player->id,
                        'amount'        => $player->balance,
                        'amount_before' => 0
                    ]);
                }

                if ($player->balance < $request->input('stake_amount')) {
                    throw new \Exception('11');
                }

                $bet = Bet::create([
                    'stake_amount' => $request->input('stake_amount'),
                    'created_at'   => date('Y-m-d H:i:s')
                ]);

                foreach ($request->input('selections') as $item) {
                    BetSelections::create([
                       'bet_id'        => $bet->id,
                       'selection_id'  => $item['id'],
                       'odds'          => $item['odds']
                   ]);
                }

                BalanceTransaction::create([
                    'player_id'     => $player->id,
                    'amount'        => $request->input('stake_amount'),
                    'amount_before' => $player->balance
                ]);

                $player->balance = $player->balance - $request->input('stake_amount');
                $player->save();

                \DB::commit();
            } catch (\Throwable $t) {
                \DB::rollBack();

                Cache::forget('player_id');
                $response = $request->all();
                $response['errors'][] = $betService->getErrorMessage($t->getMessage());

                return response()->json($response, 400);
            }   catch (\Exception $e) {
                \DB::rollBack();

                Cache::forget('player_id');
                $response = $request->all();
                $response['errors'][] = $betService->getErrorMessage($e->getMessage());

                return response()->json($response, 400);
            }

            Cache::forget('player_id');

            return response()->json($response, 201);
        }

        return response()->json($betService->getErrorMessage(0), 400);
    }
}
