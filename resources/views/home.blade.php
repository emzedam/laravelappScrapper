@extends('layouts.app')

@section('content')
<h2>Daily Statistics</h2>
      <div class="table-responsive small">
        <table class="table table-sm">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Server</th>
              <th scope="col">User</th>
              <th scope="col">Name</th>
              <th scope="col">Size</th>
              <th scope="col">Today Bw</th>
              <th scope="col">Date</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($trafics as $trafic)
                <tr style="background: red">
                    <td>{{ $trafic->id }}</td>
                    <td>{{ $trafic->Server }}</td>
                    <td>{{ $trafic->User }}</td>
                    <td>{{ $trafic->Name }}</td>
                    <td>{{ $trafic->Size }}</td>
                    <td>{{ $trafic->TodayBwDicresed }}</td>
                    <td>{{ $trafic->j_created_at }}</td>
                </tr>
            @endforeach
          </tbody>
        </table>
      </div>
@endsection
