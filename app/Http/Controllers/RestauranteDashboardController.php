<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RestauranteDashboardController extends Controller
{
    public function getStats()
    {
        $user = Auth::user();

        // 1. Contar pedidos activos
        $activeOrdersCount = $user->pedidosRestaurante()
            ->whereIn('estado', ['pendiente', 'en_preparacion', 'listo_para_recoger'])
            ->count();

        // 2. Calcular ingresos del día
        $todayIncome = $user->pedidosRestaurante()
            ->where('estado', 'entregado')
            ->whereDate('updated_at', Carbon::today())
            ->sum('subtotal');

        // 3. Verificar si el perfil está completo
        $profileComplete = $user->restaurant_address &&
                           $user->cuisine_type &&
                           $user->contact_phone &&
                           $user->restaurantDetail &&
                           $user->restaurantDetail->attention_schedule;

        $profileStatus = $profileComplete ? 'Completo' : 'Incompleto';

        return response()->json([
            'activeOrders' => $activeOrdersCount,
            'todayIncome' => number_format($todayIncome, 2),
            'profileStatus' => $profileStatus
        ]);
    }
}