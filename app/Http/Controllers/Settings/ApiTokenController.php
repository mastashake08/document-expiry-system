<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\CreateTokenRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApiTokenController extends Controller
{
    /**
     * Show the API token management page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('settings/ApiTokens', [
            'tokens' => $request->user()->tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'last_used_at' => $token->last_used_at?->diffForHumans(),
                    'created_at' => $token->created_at->format('M d, Y'),
                ];
            }),
        ]);
    }

    /**
     * Create a new API token.
     */
    public function store(CreateTokenRequest $request): RedirectResponse
    {
        $token = $request->user()->createToken($request->name);

        return to_route('api-tokens.index')->with('flash', [
            'token' => $token->plainTextToken,
            'message' => 'API token created successfully. Make sure to copy it now as you won\'t be able to see it again.',
        ]);
    }

    /**
     * Delete an API token.
     */
    public function destroy(Request $request, string $tokenId): RedirectResponse
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return to_route('api-tokens.index')->with('status', 'API token deleted successfully.');
    }
}
