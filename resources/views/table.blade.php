@extends('layouts.app')

@section('content')
    <div class="clearfix">
        <div class="content clearfix">
            <section class="body mb-5 mt-3 pt-3">
                <h2 class="section-heading mt-5 mb-5">{{ $league->name }}</h2>
                <h5 class="d-flex justify-content-center pb-2">Game table
                    @if ($tour < 6)
                        |
                        <span>
                            <a href=" {{ route('game', ['tour' => 'all']) }}">&nbspPlay all games</a>
                        </span>
                    @else
                        |<span>
                            <b>&nbsp You played last tour</b>
                        </span>
                    @endif
                </h5>
                <div class="purpose-radios-wrapper">
                    <table class="table table-hover">
                        <thead class="">
                            <tr>
                                <th scope="col">#Tours</th>
                                <th scope="col">Games</th>
                                <th scope="col">Scores</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($games as $game)
                                <tr>
                                    <th scope="row">{{ $game->tour }}</th>
                                    <td>{{ $game->homeClub->name }} - {{ $game->awayClub->name }}</td>
                                    <td>
                                        <input id="home_{{ $game->id }}" type="text"
                                            value="{{ $game->home_goal_count ?? '-' }}"> :
                                        <input id="away_{{ $game->id }}" type="text"
                                            value="{{ $game->away_goal_count ?? '-' }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-right float-right btnEdit"
                                            data-id="{{ $game->id }}">
                                            edit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="actions clearfix">
                        <div role="menu" class="float-right">
                            <span>
                                @if ($tour < 6)
                                    <a class="btn btn-dark mr-2" href="{{ route('game', ['tour' => $tour]) }}">Play
                                        #{{ ++$tour }} tour</a>
                                @else
                                    <p class="btn mr-2">Finished</p>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <hr class="mb-5 mt-5">
                <h5 class="mt-5 d-flex justify-content-center">Board</h5>
                <table class="table table-hover">
                    <thead class="thead">
                        <tr>
                            <th scope="col">Club</th>
                            <th scope="col">PTS</th>
                            <th scope="col">P</th>
                            <th scope="col">W</th>
                            <th scope="col">D</th>
                            <th scope="col">L</th>
                            <th scope="col">GD</th>
                            <th scope="col">Predictions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($league->leagueClubs as $club)
                            <tr>
                                <th>{{ $club->name }}</th>
                                <th scope="row">{{ $club->points }}</th>
                                <td>{{ $club->win + $club->draw + $club->lost }}</td>
                                <td>{{ $club->win }}</td>
                                <td>{{ $club->draw }}</td>
                                <td>{{ $club->lost }}</td>
                                <td>{{ $club->gd }}</td>
                                <td>
                                    {{ $league->totalPoints ? number_format(($club->points / $league->totalPoints) * 100, 2, ',', '') : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>
    </div>
@endsection

@section('page_script')
    <!-- JavaScript Bundle with Popper -->
    <script>
        $(".btnEdit").click(function() {
            var id = $(this).data('id');
            var home = $("#home_" + id).val();
            var away = $("#away_" + id).val();
            let formData = new FormData();
            formData.append('game', id);
            formData.append('home', home);
            formData.append('away', away);
            fetch("{{ route('game') }}", {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(function(result) {
                    if (result.success == true) {
                        location.reload();
                    }
                });
        })

    </script>
@endsection
