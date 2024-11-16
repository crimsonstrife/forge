<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class DashboardController
 *
 * This controller handles the dashboard functionalities for the application.
 * It extends the base Controller class provided by the framework.
 *
 * @package App\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard index page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $dashboards = Auth::user()?->dashboards ?? collect();

        if ($dashboards->isEmpty()) {
            return view('landing-page'); // General landing page when no dashboards are available
        }

        return view('dashboards.index', ['dashboards' => $dashboards]);
    }

    /**
     * Display the general landing page or the first available dashboard.
     *
     * @return \Illuminate\Contracts\View\View | \Illuminate\Http\RedirectResponse
     */
    public function landingPage()
    {
        // Intialize the user model
        $userModel = new User;

        // Initialize the user variable
        $user = null;

        // Get the authenticated user
        $user = $userModel->find(Auth::id());

        if ($user == null) {
            // Redirect to the login page if the user is not authenticated
            return redirect()->route('login');
        }

        // Check if the user has any dashboards
        $dashboard = $user->dashboards()->first();

        if ($dashboard) {
            // Redirect to the first dashboard as the default behavior
            return redirect()->route('dashboards.view', $dashboard->id);
        }

        // Fallback: Render the general landing page
        return view('dashboards.landing');
    }

    /**
     * Show a specific dashboard.
     */
    public function show($id)
    {
        $dashboard = Dashboard::findOrFail($id);

        // Ensure the user has access to the dashboard
        if ($dashboard->owner_id !== Auth::id() && !$dashboard->is_shared) {
            abort(403);
        }

        return view('dashboards.show', compact('dashboard'));
    }

    /**
     * Manage the dashboard view and related functionalities.
     *
     * @return \Illuminate\View\View
     */
    public function manage()
    {
        $dashboards = Auth::user()?->dashboards ?? collect();

        return view('dashboards.manage', compact('dashboards'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $dashboard = Dashboard::create([
            'name' => $validated['name'],
        ]);

        Auth::user()->dashboards()->attach($dashboard->id);

        return redirect()->route('dashboards.manage')->with('success', 'Dashboard created successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboards.create');
    }
}
