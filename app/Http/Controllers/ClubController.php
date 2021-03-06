<?php

namespace App\Http\Controllers;

use App\Club;
use App\Contract;
use App\RequestToJoinTheClub;
use App\Role;
use Auth;
use File;
use Illuminate\Http\Request;
use Image;

class ClubController extends Controller
{
    public function menu(){

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.menu')->render();
            return response()->json($view);
        }
        else{
            return view('clubs.menu');
        }
    }

    public function validateContracts(Club $club){

        $authUserId = Auth::user()->id;
        $bindingContracts = $club->contracts->where('status', 'signed');
        $authUserBindingContractExpired = false;

        if (request()->ajax()){
            if ($bindingContracts->isNotEmpty()){
                foreach ($bindingContracts as $bindingContract){
                    $contractExpired = $this->checkIfContractExpired($bindingContract->date_and_time_of_end);

                    if ($contractExpired){

                        $user = $bindingContract->user;
                        $club = $bindingContract->club;

                        $user->club_id = NULL;
                        $user->save();

                        $club->number_of_footballers--;
                        $club->save();

                        $bindingContract->delete();

                        if ($user->id === $authUserId){
                            $authUserBindingContractExpired = true;
                        }
                    }
                }

                if ($authUserBindingContractExpired){
                    return response()->json('Your contract expired :(');
                }
            }
        }
    }

    public function show(Club $club){

        $clubPresident = $club->users->where('role_id', Role::ClubPresident)->first();
        $footballers = $club->users->where('role_id', Role::Footballer);

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.show',
                compact('club', 'clubPresident', 'footballers'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.show', compact('club', 'clubPresident', 'footballers'));
        }
    }

    public function getTrophiesForFirstPlace(Club $club){

        $trophiesForFirstPlace = $club->trophies()->where('label', 'first place')->paginate(3);

        if (request()->ajax()){

            $view = view('layouts.elements.trophies.trophies-first-place-list',
                compact('trophiesForFirstPlace'))->render();
            return response()->json($view);
        }
        else{
            return view('layouts.elements.trophies.trophies-first-place-list', compact('trophiesForFirstPlace'));
        }
    }

    public function getTrophiesForSecondPlace(Club $club){

        $trophiesForSecondPlace = $club->trophies()->where('label', 'second place')->paginate(3);

        if (request()->ajax()){

            $view = view('layouts.elements.trophies.trophies-second-place-list',
                compact('trophiesForSecondPlace'))->render();
            return response()->json($view);
        }
        else{
            return view('layouts.elements.trophies.trophies-second-place-list', compact('trophiesForSecondPlace'));
        }
    }

    public function getTrophiesForThirdPlace(Club $club){

        $trophiesForThirdPlace = $club->trophies()->where('label', 'third place')->paginate(3);

        if (request()->ajax()){

            $view = view('layouts.elements.trophies.trophies-third-place-list',
                compact('trophiesForThirdPlace'))->render();
            return response()->json($view);
        }
        else{
            return view('layouts.elements.trophies.trophies-third-place-list', compact('trophiesForThirdPlace'));
        }
    }

    public function create(){

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.create')->render();
            return response()->json($view);
        }
        else{
            return view('clubs.create');
        }
    }

    public function update(Club $club, Request $request){

        if ($request->has('name')){

            $this->validate($request, [
                'name' => 'required|string|min:3|max:20|unique:clubs',
            ]);

            $club->name = $request->name;
            $club->save();
        }

        if ($request->hasFile('club_emblem')){

            $emblem = $request->file('club_emblem');

            $clubName = implode('_', explode(' ', $club->name));

            $directoryName = 'uploads/clubs/emblems/'. $clubName. '/';
            $fileName = $clubName. '_emblem.'. $emblem->getClientOriginalExtension();

            if(!File::exists(public_path($directoryName))) {
                File::makeDirectory(public_path($directoryName));
            }
            else{
                File::deleteDirectory(public_path($directoryName), true);
            }

            Image::make($emblem)->resize(150, 150)->save(public_path($directoryName. $fileName));

            $club->emblem_dir = $directoryName;
            $club->emblem = $fileName;
            $club->save();
        }

        flashy()->success('Club profile was updated');
        return redirect()->back();
    }

    public function destroy(Club $club){

        $authUser = Auth::user();
        $authUser->role_id = Role::Footballer;
        $authUser->save();

        $club->delete();

        flashy()->success('Club was destroyed');
        return redirect()->to(route('home'));
    }

    public function clubMenu(Club $club){

        $currentDate = date('Y-m-d H:i');
        $clubTournaments = $club->tournaments;

        $nextMatch = null;

        foreach ($clubTournaments as $clubTournament){

            $clubTournamentNextMatch = $clubTournament->matches()
                ->whereNotNull('start_date_and_time')
                ->orderBy('start_date_and_time')
                ->where('start_date_and_time', '>', $currentDate)
                ->first();

            if ($nextMatch !== null && $clubTournamentNextMatch !== null
                && $clubTournamentNextMatch->start_date_and_time < $nextMatch->start_date_and_time){

                $nextMatch = $clubTournamentNextMatch;
            }
            elseif ($nextMatch === null){
                $nextMatch = $clubTournamentNextMatch;
            }
        }

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.your-club-menu', compact('club', 'nextMatch'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.your-club-menu', compact('club', 'nextMatch'));
        }
    }

    public function clubSubMenu1(Club $club){

        $currentDate = date('Y-m-d H:i');
        $clubTournaments = $club->tournaments;

        $nextMatch = null;

        foreach ($clubTournaments as $clubTournament){

            $clubTournamentNextMatch = $clubTournament->matches()
                ->whereNotNull('start_date_and_time')
                ->orderBy('start_date_and_time')
                ->where('start_date_and_time', '>', $currentDate)
                ->first();

            if ($nextMatch !== null && $clubTournamentNextMatch !== null
                && $clubTournamentNextMatch->start_date_and_time < $nextMatch->start_date_and_time){

                $nextMatch = $clubTournamentNextMatch;
            }
            elseif ($nextMatch === null){
                $nextMatch = $clubTournamentNextMatch;
            }
        }

        if (request()->ajax()){

            $view = view('layouts.elements.clubs.menu.your-club-menu.sub-menu-1',
                compact('club', 'nextMatch'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.your-club-menu', compact('club', 'nextMatch'));
        }
    }

    public function clubSubMenu2(Club $club){

        if (request()->ajax()){

            $view = view('layouts.elements.clubs.menu.your-club-menu.sub-menu-2', compact('club'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.your-club-menu', compact('club'));
        }
    }

    public function joinRequests(Club $club){

        $requestsToJoinTheClub = RequestToJoinTheClub::where('club_id', $club->id)
            ->orderBy('created_at', 'desc')
            ->paginate(3);

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.join-requests.join-requests',
                compact('requestsToJoinTheClub'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.join-requests.join-requests', compact('requestsToJoinTheClub'));
        }
    }

    public function createdContracts(Club $club){

        $contracts = Contract::where('club_id', $club->id)
            ->where('status', 'created')
            ->orderBy('created_at', 'desc')
            ->paginate(3);

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.waiting-contracts', compact('contracts'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.waiting-contracts', compact('contracts'));
        }
    }

    public function signedContracts(Club $club){

        $contracts = Contract::where('club_id', $club->id)
            ->where('status', 'signed')
            ->orderBy('date_and_time_of_end', 'asc')
            ->paginate(3);

        $remainingContractsDuration = $this->getRemainingContractsDuration($contracts);

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.signed-contracts',
                compact('contracts', 'remainingContractsDuration'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.signed-contracts', compact('contracts', 'remainingContractsDuration'));
        }
    }

    public function extensionProposedContracts(Club $club){

        $contracts = Contract::where('club_id', $club->id)
            ->where('status', 'extension proposed')
            ->orderBy('date_and_time_of_end', 'asc')
            ->paginate(3);

        $remainingContractsDuration = $this->getRemainingContractsDuration($contracts);

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.extension-propositions-for-contracts',
                compact('contracts', 'remainingContractsDuration'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.extension-propositions-for-contracts',
                compact('contracts', 'remainingContractsDuration'));
        }
    }

    public function openTournaments(Club $club){

        $tournaments = $club->tournaments()
            ->where('status', 'open')
            ->orderBy('start_date', 'asc')
            ->paginate(3);

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.open-tournaments',
                compact('tournaments'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.open-tournaments', compact('tournaments'));
        }
    }

    public function ongoingTournaments(Club $club){

        $tournaments = $club->tournaments()
            ->where('status', 'ongoing')
            ->orderBy('end_date', 'asc')
            ->paginate(3);

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.ongoing-tournaments',
                compact('tournaments'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.ongoing-tournaments', compact('tournaments'));
        }
    }

    public function closedTournaments(Club $club){

        $tournaments = $club->tournaments()
            ->where('status', 'closed')
            ->orderBy('start_date', 'asc')
            ->paginate(3);

        if (request()->ajax()){

            $view = view('dynamic-content.clubs.closed-tournaments',
                compact('tournaments'))->render();
            return response()->json($view);
        }
        else{
            return view('clubs.closed-tournaments', compact('tournaments'));
        }
    }

    public function store(Request $request){

        $authUser = Auth::user();

        if ($request->has('name') && $request->has('country') && $request->has('city')){

            $this->validate($request, [
                'name' => 'required|string|min:3|max:20|unique:clubs',
                'country' => 'required|string|min:3|max:12',
                'city' => 'required|string|min:3|max:12'
            ]);

            $club = new Club;
            $club->name = $request->name;
            $club->founded_by = $authUser->username;
            $club->country = $request->country;
            $club->city = $request->city;

            if ($request->hasFile('club_emblem')){

                $emblem = $request->file('club_emblem');

                $clubName = implode('_', explode(' ', $club->name));

                $directoryName = 'uploads/clubs/emblems/'. $clubName. '/';
                $fileName = $clubName. '_emblem.'. $emblem->getClientOriginalExtension();

                if(!File::exists(public_path($directoryName))) {
                    File::makeDirectory(public_path($directoryName));
                }
                else{
                    File::deleteDirectory(public_path($directoryName), true);
                }

                Image::make($emblem)->resize(150, 150)->save(public_path($directoryName. $fileName));

                $club->emblem_dir = $directoryName;
                $club->emblem = $fileName;
            }

            $club->save();

            $authUser->club_id = $club->id;
            $authUser->role_id = Role::ClubPresident;
            $authUser->save();

            // Delete all waiting contracts and requests to join the club if exists
            $userWaitingContracts = $authUser->contracts();
            $userWaitingContracts->delete();

            $userRequestsToJoinTheClub = $authUser->requestsToJoinTheClub();
            $userRequestsToJoinTheClub->delete();

            flashy()->success('Club was successfully created. You are club president now !!');
            return redirect()->to(route('home'));
        }

        flashy()->error('Club cannot be created. Error occurs');
        return redirect()->back();
    }

    public function join(Club $club){

        if (request()->ajax()){

            $authUserId = Auth::user()->id;

            $requestToJoinTheClub = new RequestToJoinTheClub;
            $requestToJoinTheClub->club_id = $club->id;
            $requestToJoinTheClub->user_id = $authUserId;
            $requestToJoinTheClub->save();

            return response()->json('Request with try to join the club was sent');
        }
    }

    // Search clubs - search, filters and sort

    public function listAndSearch(Request $request){

        $clubs = Club::paginate(5);

        if ($request->sortBy === 'name'){

            if ($request->direction === 'desc'){
                $clubs = Club::orderBy('name', 'desc')->paginate(5);
            }
            else{
                $clubs = Club::orderBy('name', 'asc')->paginate(5);
            }
        }
        elseif ($request->sortBy === 'country'){

            if($request->direction === 'desc'){
                $clubs = Club::orderBy('country', 'desc')->paginate(5);
            }
            else{
                $clubs = Club::orderBy('country', 'asc')->paginate(5);
            }
        }
        elseif ($request->sortBy === 'city'){

            if($request->direction === 'desc'){
                $clubs = Club::orderBy('city', 'desc')->paginate(5);
            }
            else{
                $clubs = Club::orderBy('city', 'asc')->paginate(5);
            }
        }
        elseif ($request->sortBy === 'tournament points'){

            if($request->direction === 'desc'){
                $clubs = Club::orderBy('tournament_points', 'desc')->paginate(5);
            }
            else{
                $clubs = Club::orderBy('tournament_points', 'asc')->paginate(5);
            }
        }
        elseif ($request->sortBy === 'trophies'){

            if($request->direction === 'desc'){
                $clubs = Club::orderBy('won_trophies', 'desc')->paginate(5);
            }
            else{
                $clubs = Club::orderBy('won_trophies', 'asc')->paginate(5);
            }
        }

        if (request()->ajax()){

            $firstView = view('layouts.elements.clubs.search.search')->render();
            $secondView = view('dynamic-content.clubs.list', compact('clubs'))->render();

            return response()->json([
                'search' => $firstView,
                'list' => $secondView
            ]);
        }
        else{
            return view('clubs.list', compact('clubs'));
        }
    }

    public function search(Request $request){

        if(request()->ajax()){

            $clubMinTournamentPointsValue = $request->clubMinTournamentPointsValue;
            $clubMaxTournamentPointsValue = $request->clubMaxTournamentPointsValue;

            $clubs = Club::where('name', 'like', $request->clubNameValue. '%')
                ->where('country', 'like', $request->clubCountryValue. '%')
                ->where('city', 'like', $request->clubCityValue. '%')
                ->when($clubMinTournamentPointsValue, function ($query) use ($clubMinTournamentPointsValue) {
                    return $query->where('tournament_points', '>=', $clubMinTournamentPointsValue);
                })
                ->when($clubMaxTournamentPointsValue, function ($query) use ($clubMaxTournamentPointsValue) {
                    return $query->where('tournament_points', '<=', $clubMaxTournamentPointsValue);
                })
                ->paginate(3);

            $view = view('dynamic-content.clubs.searchable-cards', compact('clubs'))->render();
            return response()->json($view);

        }
    }

    private function getRemainingContractsDuration($contracts): array {

        $remainingContractsDuration = [];

        foreach ($contracts as $contract){
            $remainingContractsDuration[$contract->id] =
                $this->computeRemainingContractDuration($contract->date_and_time_of_end);
        }

        return $remainingContractsDuration;
    }

    private function computeRemainingContractDuration($dateOfEnd){

        $currentDate = date_create(date('Y-m-d H:i'));
        $endDate = date_create($dateOfEnd);

        $dateDifference = $currentDate->diff($endDate);

        $remainingContractDurationInDays = $dateDifference->format('%a');
        $remainingContractDurationInHours = $dateDifference->format('%h');

        if ($remainingContractDurationInDays !== '0'){
            $remainingContractDuration = $dateDifference->format('%a day(s) left');
        }
        else{
            if ($remainingContractDurationInHours !== '0'){
                $remainingContractDuration = $dateDifference->format('%h hour(s) left');
            }
            else{
                $remainingContractDuration = $dateDifference->format('%i minute(s) left');
            }
        }

        return $remainingContractDuration;
    }

    private function checkIfContractExpired($dateOfEnd){

        $currentDate = date_create(date('Y-m-d H:i'));
        $endDate = date_create($dateOfEnd);

        $dateDifference = $currentDate->diff($endDate);

        $remainingContractDurationInDays = intval($dateDifference->format('%r%a'));
        $remainingContractDurationInHours = intval($dateDifference->format('%r%h'));
        $remainingContractDurationInMinutes = intval($dateDifference->format('%r%i'));

        if ($remainingContractDurationInDays <= 0 && $remainingContractDurationInHours <= 0
            && $remainingContractDurationInMinutes <= 0){
            return true;
        }

        return false;
    }
}
