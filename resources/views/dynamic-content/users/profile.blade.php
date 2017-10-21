<div class="col-md-12">
    <div class="jumbotron jumbotron-main-page">
        <div class="row justify-content-center">
            <div class="col">
                <div class="tile tile-users chosen">
                    <img class="cover-image" src='{{ asset('images/clubs/menu/football-grass.jpg') }}'>
                    <h1 class="info-header">
                        <a id="delete-account" data-toggle="modal" data-target="#delete-account-modal"
                           class="btn btn-circle btn-danger" role="button"><i class="fa fa-trash"></i></a>
                    </h1>
                    <div class="text-header text-center">
                        <h1>Your profile</h1>
                        <h1><img src="{{ asset($user->avatar_dir. $user->avatar) }}"
                                 width="100" height="100" class="img-fluid rounded-circle"></h1>
                        <h1 class="text-display font-italic">Since from: </h1>
                        <h6>{{ $user->created_at }}</h6>
                        <hr>
                        <h1 class="text-display font-italic">Current club: </h1>
                        @if($user->haveClub())
                            <h6>
                                <img src="{{ asset($user->club->emblem_dir. $user->club->emblem) }}" width="45" height="45">
                            </h6>
                            <h6>{{ $user->club->name }}</h6>
                        @else
                            <h6>You don't belong to any club</h6>
                        @endif
                        <hr>
                        <h1 id="preferred-football-positions" class="text-display font-italic"
                            data-number-football-positions="{{ $user->numberOfFootballPositions() }}">
                            Preferred football positions:
                        </h1>
                        <h1 class="text-display font-italic">
                            <span id="football-positions-added"></span>
                            @foreach($userFootballPositions as $userFootballPosition)
                                <a href="{{ route('user-football-position-delete',
                                         [Auth::user()->id, $userFootballPosition->id]) }}"
                                   class="badge badge-pill my-color delete-football-position" role="button">
                                    <span>{{ $userFootballPosition->name }}</span>
                                    <i class="fa fa-remove"></i>
                                </a>
                            @endforeach
                            @if(!$user->haveThreeFootballPositions())
                                <a href="{{ route('user-football-position-add', Auth::user()->id) }}"
                                   class="btn btn-circle-position my-color-3 add-football-position-button" role="button">
                                    <i class="fa fa-plus"></i>
                                </a>
                                <select id="football-positions" class="search-select" href="{{ route('footballers-search') }}" name="#">
                                    <option value="0" selected="selected">---</option>
                                    @foreach($footballPositions as $footballPosition)
                                        <option value="{{$footballPosition->id}}">{{$footballPosition->name}}</option>
                                    @endforeach
                                </select>
                            @else
                                <a href="{{ route('user-football-position-add', Auth::user()->id) }}"
                                   class="btn btn-circle-position my-color-3 add-football-position-button" role="button" style="display: none">
                                    <i class="fa fa-plus"></i>
                                </a>
                                <select id="football-positions" class="search-select" href="{{ route('footballers-search') }}" name="#">
                                    <option value="0" selected="selected">---</option>
                                    @foreach($footballPositions as $footballPosition)
                                        <option value="{{$footballPosition->id}}">{{$footballPosition->name}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </h1>
                    </div>
                </div>
            </div>

            <div class="col">
                <form id="profile-form" enctype="multipart/form-data" method="POST"
                      action="{{ route('user-update', $user->id) }}">

                    {{ csrf_field() }}
                    {{ method_field('PUT') }}

                    <h3 class="font-italic">Update profile image</h3>
                    <div class="form-group">
                        <img src="{{ asset($user->avatar_dir. $user->avatar) }}"
                             width="110" height="110" class="img-fluid rounded-circle">
                        <input id="avatar" type="file" name="avatar">
                    </div>
                    <br>
                    <h3 class="font-italic">Change password</h3>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <div class="input-container">
                            <input type="password" id="password" name="password">
                            <label for="password">Password</label>
                            <div class="bar"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-container">
                            <input type="password" id="password-confirm" name="password_confirmation">
                            <label for="password-confirm">Confirm Password</label>
                            <div class="bar"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button class="btn my-color" type="submit">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.elements.delete-account-modal')