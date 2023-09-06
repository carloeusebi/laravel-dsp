<?php

namespace App\Http\Controllers;

use App\Helpers\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $page = $this->getRouteName($request);
        $status = $request->session()->pull('status');

        $page_title = $this->getPageTitle($page);

        return view($page, compact('status', 'page_title'));
    }

    private function getRouteName(Request $request): string
    {
        return $request->path() === '/' ? 'home' : $request->path();
    }

    private function getPageTitle(string $page): string
    {
        return match ($page) {
            'home' => 'Home',
            'chi-sono' => 'Chi Sono',
            'cosa-aspettarsi' => 'Cosa Aspettarsi dalla Terapia',
            'di-cosa-mi-occupo' => 'Di cosa mi Occupo',
            'contatti' => 'Contatti',
            default => 'Pagina non trovata'
        };
    }
}
