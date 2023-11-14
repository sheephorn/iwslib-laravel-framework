<?php

namespace IwslibLaravel\Http;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends BaseController
{

    public function description(): string
    {
        return "viewの返却";
    }

    public function entry(Request $request)
    {
        if (!$request->expectsJson()) {
            return response()->view('index')->withHeaders($this->makeHeader());
        } else {
            return response()->json([], 404);
        }
    }

    private function makeHeader(): array
    {
        $header = [];
        $user = Auth::user();
        if ($user) {
            $header["User-Auth"] = "yes";
        } else {
            $header["User-Auth"] = 'none';
        }
        return $header;
    }
}
