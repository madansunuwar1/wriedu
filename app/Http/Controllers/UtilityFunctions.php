<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\History;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class UtilityFunctions
{
    /**
     * Get the user's IP address.
     *
     * @return string
     */
    public static function getUserIP()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        
        return 'UNKNOWN';
    }

    /**
     * Get roles based on the user's privileges.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getRole()
    {
        $user = Auth::user();

        if ($user && $user->isSuperAdmin()) {
            return Role::with('permissions')->whereNotIn('id', [1])->get();
        }

        return Role::with('permissions')->whereNotIn('id', [1, 2])->get();
    }

    /**
     * Create a history record.
     *
     * @param string $message
     * @param string $type
     * @return void
     */
    public static function createHistory($message, $type)
    {
        History::create([
            'description' => $message,
            'user_id' => Auth::id(),
            'type' => $type,
            'ip_address' => self::getUserIP(),
        ]);
    }

    /**
     * Get an empty or null value if the input is empty.
     *
     * @param string|null $input
     * @return string|null
     */
    public static function getEmptyName($input)
    {
        return (!isset($input) || trim($input) === '') ? null : $input;
    }

    /**
     * Create a custom paginator.
     *
     * @param int $currentPage
     * @param array $array
     * @param int $perPage
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function customPaginate($currentPage, $array, $perPage, $request)
    {
        $itemCollection = collect($array);

        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();

        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath($request->url());

        return $paginatedItems;
    }
}
