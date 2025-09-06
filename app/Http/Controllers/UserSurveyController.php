<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;

class UserSurveyController extends Controller
{
    public function signup(){
        return view('user.survey.quote-form');
    }

    public function submitSurvey(Request $request){

       $survey =  Survey::updateOrCreate(['email_address' => $request->email_address],
        [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'telephone_number' => $request->telephone_number,
            'full_address' => $request->full_address,
            'postcode' => $request->postcode,
            'house_or_flat' => $request->house_or_flat,
            'number_of_bedrooms' => $request->number_of_bedrooms,
            'market_value' => $request->market_value,
            'listed_building' => $request->listed_building ?? 'no',
            'over1650' => $request->over1650 ?? 'no',
            'sqft_area' => $request->sqft_area,
            'current_step' => 0,
            'is_submitted' => false,
        ]);

        return redirect()->route('user.flettons.listing.page', $survey->id);
    }

    public function flettonsListingPage($id){
        $survey = Survey::find($id);
        $data =[
            'survey' => $survey
        ];

        return view('user.survey.listing', $data);
    }

    public function submitListingPage(Request $request){

        $survey = Survey::find($request->id);
        $survey->update([
            'level' => $request->level,
            'level_total' => $request->level_total,
            'breakdown' => $request->breakdown_of_estimated_repair_costs,
            'aerial' => $request->aerial_roof_and_chimney,
            'insurance' => $request->insurance_reinstatement_valuation,
            'current_step' => 1,
        ]);

        return redirect()->route('user.flettons.rics.survey.page', $survey->id);
    }


    public function flettonsRicsSurveyPage($id){
        $survey = Survey::find($id);
        $data =[
            'survey' => $survey
        ];

        return view('user.survey.rics-survey', $data);
    }

    public function submitRicsSurveyPage(Request $request){
        $survey = Survey::find($request->id);
        $data = $request->all();
        $data['current_step'] = 2;
        $data['is_submitted'] = true;
        $survey->update($data);

        return redirect('/')->with('success', 'Survey submitted successfully!');
    }

}
