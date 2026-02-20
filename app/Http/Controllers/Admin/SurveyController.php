<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'Survey',
            'active' => 'survey',
            'surveys' => Survey::all(),
            'table' => 'Surveys'
        ];
        return view('admin.survey.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = [
            'title' => 'Detail Survey',
            'active' => 'survey',
            'survey' => Survey::find($id),
            'table' => 'Detail Survey'
        ];
        return view('admin.survey.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage and delete the contact from Keap CRM.
     */
    public function destroy(string $id)
    {
        $survey = Survey::find($id);
        if (!$survey) {
            return redirect()->back()->with('error', 'Survey not found.');
        }

        $contactId = $survey->contact_id;
        $keapDeleted = false;

        if (!empty($contactId)) {
            $response = Http::withHeaders([
                'X-Keap-API-Key' => 'KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
                'Authorization' => 'Bearer KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
                'Content-Type' => 'application/json',
            ])->delete("https://api.infusionsoft.com/crm/rest/v1/contacts/{$contactId}");

            $keapDeleted = $response->successful();
        }

        $survey->delete();

        if ($contactId && !$keapDeleted) {
            return redirect()->back()->with('warning', 'Survey deleted. Keap contact could not be deleted (may already be removed or API error).');
        }

        return redirect()->back()->with('success', 'Survey and Keap contact deleted successfully.');
    }
}
