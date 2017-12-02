<div class="matches-list">
    @foreach($fourthRoundMatches as $fourthRoundMatch)
        @if($isFinalRound)
            <h5 class="font-bold">
                @if($fourthRoundMatch->isThirdPlaceMatch($thirdPlaceMatchId))
                    <i class="fa fa-trophy fa-fw" style="color: saddlebrown"></i>
                    Third place match
                @elseif($fourthRoundMatch->isFirstPlaceMatch($firstPlaceMatchId))
                    <i class="fa fa-trophy fa-fw" style="color: gold"></i>
                    FINAL
                @endif
            </h5>
        @endif
        <h5 class="font-bold" style="color: darkred">
            <i class="fa fa-clock-o fa-fw"></i>
            @if($fourthRoundMatch->hasStartDateAndTime())
                {{ \Carbon\Carbon::parse($fourthRoundMatch->start_date_and_time)->format('d/m/Y H:i') }}
            @else
                ---
            @endif
        </h5>
        <h6>
            <img src="{{ asset($fourthRoundMatch->first_club_emblem_dir. $fourthRoundMatch->first_club_emblem) }}"
                 width="60" height="60" class="img-fluid rounded-circle">
            {{ $fourthRoundMatch->first_club}}
            <span class="font-bold">vs</span>
            {{ $fourthRoundMatch->second_club}}
            <img src="{{ asset($fourthRoundMatch->second_club_emblem_dir. $fourthRoundMatch->second_club_emblem) }}"
                 width="60" height="60" class="img-fluid rounded-circle">
        </h6>
        <h5 class="font-bold" style="margin-top: -20px">
            <span class="badge badge-pill my-color-3">
                @if($fourthRoundMatch->hasResults())
                    {{ $fourthRoundMatch->result_first_club }} : {{ $fourthRoundMatch->result_second_club }}
                @else
                    --- : ---
                @endif
            </span>
        </h5>
        <hr>
    @endforeach
</div>
<div class="container pagination-links">
    <div class="row justify-content-center">
        {{ $fourthRoundMatches->links('layouts.pagination.matches.list') }}
    </div>
</div>